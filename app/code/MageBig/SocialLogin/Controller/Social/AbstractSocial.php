<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\SocialLogin\Controller\Social;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageBig\SocialLogin\Helper\Social as SocialHelper;
use MageBig\SocialLogin\Model\Social;

/**
 * Class AbstractSocial
 *
 * @package MageBig\SocialLogin\Controller
 */
abstract class AbstractSocial extends Action
{
    /**
     * @type Session
     */
    protected $session;

    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type SocialHelper
     */
    protected $apiHelper;

    /**
     * @type Social
     */
    protected $apiObject;

    /**
     * @type
     */
    protected $cookieMetadataManager;

    /**
     * @type
     */
    protected $cookieMetadataFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * Login constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param SocialHelper $apiHelper
     * @param Social $apiObject
     * @param Session $customerSession
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        SocialHelper $apiHelper,
        Social $apiObject,
        Session $customerSession,
        RawFactory $resultRawFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->apiHelper = $apiHelper;
        $this->apiObject = $apiObject;
        $this->session = $customerSession;
        $this->resultRawFactory = $resultRawFactory;

        parent::__construct($context);
    }

    /**
     * Get Store object
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @param $userProfile
     * @param $type
     *
     * @return bool|Customer|mixed
     * @throws Exception
     * @throws LocalizedException
     */
    public function createCustomerProcess($userProfile, $type)
    {
        $name = explode(' ', $userProfile->displayName ?: __('New User'));

        $user = array_merge(
            [
                'email' => $userProfile->email ?: $userProfile->identifier . '@' . strtolower($type) . '.com',
                'firstname' => $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier),
                'lastname' => $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier),
                'identifier' => $userProfile->identifier,
                'type' => $type,
                'password' => $userProfile->password ?? null
            ],
            $this->getUserData($userProfile)
        );

        return $this->createCustomer($user, $type);
    }

    /**
     * Create customer from social data
     *
     * @param $user
     * @param $type
     *
     * @return bool|Customer|mixed
     * @throws Exception
     * @throws LocalizedException
     */
    public function createCustomer($user, $type)
    {
        $customer = $this->apiObject->getCustomerByEmail($user['email'], $this->getStore()->getWebsiteId());
        if ($customer->getId()) {
            $this->apiObject->setAuthorCustomer($user['identifier'], $customer->getId(), $type);
        } else {
            try {
                $customer = $this->apiObject->createCustomerSocial($user, $this->getStore());
            } catch (Exception $e) {
                $this->emailRedirect($e->getMessage(), false);

                return false;
            }
        }

        return $customer;
    }

    /**
     * @param $profile
     *
     * @return array
     */
    protected function getUserData($profile)
    {
        return [];
    }

    /**
     * Redirect to login page if social data is not contain email address
     *
     * @param $apiLabel
     * @param bool $needTranslate
     *
     * @return $this
     */
    public function emailRedirect($apiLabel, $needTranslate = true)
    {
        $message = $needTranslate ? __('Email is Null, Please enter email in your %1 profile', $apiLabel) : $apiLabel;
        $this->messageManager->addErrorMessage($message);
        $this->_redirect('customer/account/login');

        return $this;
    }

    /**
     * Return redirect url by config
     *
     * @return mixed
     */
    protected function _loginPostRedirect()
    {
        $url = '';
        $isCheckout = $this->session->getData('social_in_checkout');

        if ($this->apiHelper->getConfigValue('customer/startup/redirect_dashboard') && !$isCheckout) {
            $url = $this->_url->getUrl('customer/account');
        }

        $object = new DataObject(
            [
                'url' => $url,
            ]
        );

        $this->_eventManager->dispatch(
            'social_manager_get_login_redirect',
            [
                'object' => $object,
                'request' => $this->_request
            ]
        );

        return $object->getUrl();
    }

    /**
     * Return javascript to redirect when login success
     *
     * @param null $content
     *
     * @return Raw
     */
    public function _appendJs($content = null)
    {
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $this->_loginPostRedirect();
        $raw = $resultRaw->setContents(
            $content ?: sprintf(
                "<script>window.opener.socialCallback('%s', window);</script>",
                $this->_loginPostRedirect()
            )
        );

        return $raw;
    }

    /**
     * @param $customer
     *
     * @throws InputException
     * @throws FailureToSendException
     */
    public function refresh($customer)
    {
        if ($customer && $customer->getId()) {
            $this->session->setCustomerAsLoggedIn($customer);
            $this->session->regenerateId();

            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return     PhpCookieManager
     * @deprecated
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                PhpCookieManager::class
            );
        }

        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return     CookieMetadataFactory
     * @deprecated
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                CookieMetadataFactory::class
            );
        }

        return $this->cookieMetadataFactory;
    }

    /**
     * @param $message
     */
    protected function setBodyResponse($message)
    {
        $content = '<html><head></head><body>';
        $content .= '<div class="message message-error">' . __("Ooophs, we got an error: %1", $message) . '</div>';
        $content .= <<<Style
<style type="text/css">
    .message{
        background: #fffbbb;
        border: none;
        border-radius: 0;
        color: #333333;
        font-size: 1.4rem;
        margin: 0 0 10px;
        padding: 1.8rem 4rem 1.8rem 1.8rem;
        position: relative;
        text-shadow: none;
    }
    .message-error{
        background:#ffcccc;
    }
</style>
Style;
        $content .= '</body></html>';

        $this->getResponse()->setBody($content);
    }
}
