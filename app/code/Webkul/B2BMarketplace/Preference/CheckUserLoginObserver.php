<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_B2BMarketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\B2BMarketplace\Preference;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Check captcha on user login page observer.
 */
class CheckUserLoginObserver extends \Magento\Captcha\Observer\CheckUserLoginObserver
{
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Authentication
     *
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * CheckUserLoginObserver constructor.
     *
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Session\SessionManagerInterface $customerSession
     * @param CaptchaStringResolver $captchaStringResolver
     * @param \Magento\Customer\Model\Url $customerUrl
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $_messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SessionManagerInterface $customerSession,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->_messageManager = $_messageManager;
        $this->_session = $customerSession;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->_customerUrl = $customerUrl;
    }

    /**
     * Get customer repository
     *
     * @return \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private function getCustomerRepository()
    {

        if (!($this->customerRepository instanceof \Magento\Customer\Api\CustomerRepositoryInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Customer\Api\CustomerRepositoryInterface::class
            );
        } else {
            return $this->customerRepository;
        }
    }

    /**
     * Get authentication
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication()
    {

        if (!($this->authentication instanceof AuthenticationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                AuthenticationInterface::class
            );
        } else {
            return $this->authentication;
        }
    }

    /**
     * Check captcha on user login page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'user_login';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $loginParamsData = $controller->getRequest()->getPost('login');
        $supplier = $controller->getRequest()->getPost('supplier_login');
        $login = (is_array($loginParamsData) && array_key_exists('username', $loginParamsData))
            ? $loginParamsData['username']
            : null;
        if ($captchaModel->isRequired($login)) {
            $word = $this->captchaStringResolver->resolve($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
                try {
                    $customer = $this->getCustomerRepository()->get($login);
                    $this->getAuthentication()->processAuthenticationFailure($customer->getId());
                    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
                } catch (NoSuchEntityException $e) {
                    //do nothing as customer existence is validated later in authenticate method
                }
                $this->_messageManager->addErrorMessage(__('Incorrect CAPTCHA'));
                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->_session->setUsername($login);
                $beforeUrl = $this->_session->getBeforeAuthUrl();
                $url = $beforeUrl ? $beforeUrl : $this->_customerUrl->getLoginUrl();
                $moduleName = $this->request->getModuleName();
                if ($supplier) {
                    $url = $this->urlBuilder->getUrl('b2bmarketplace/supplier/login/');
                }
                $controller->getResponse()->setRedirect($url);
            }
        }
        $captchaModel->logAttempt($login);

        return $this;
    }
}
