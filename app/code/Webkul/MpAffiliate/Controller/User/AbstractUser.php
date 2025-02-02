<?php
/**
 * Webkul MpAffiliate User Summary controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;

class AbstractUser extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Webkul\Affiliate\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UserFactory $userFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UserFactory $userFactory,
        AffDataHelper $affDataHelper,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->userFactory = $userFactory;
        $this->affDataHelper = $affDataHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $configData = $this->affDataHelper->getAffiliateConfig();
        if (!$configData['enable']) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $defaultUrl = $this->_url->getUrl('*/*/', ['_secure' => true]);
            $resultRedirect->setUrl($defaultUrl);
            return $resultRedirect;
        }
        $customerId = $this->customerSession->getCustomerId();
        $affiUserColl = $this->userFactory->create()->getCollection()
                                            ->addFieldToFilter('customer_id', $customerId);
        $redirect = true;
        if ($affiUserColl->getSize()) {
            foreach ($affiUserColl as $affiliateUser) {
                $redirect = $affiliateUser->getStatus() ? false : true;
            }
        }
        if ($redirect) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $defaultUrl = $this->_url->getUrl('*/*/becomeaffiliate', ['_secure' => true]);
            $resultRedirect->setUrl($defaultUrl);
            return $resultRedirect;
        }
        return parent::dispatch($request);
    }

    /**
     * User Controller page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Affiliate User Summary'));
        return $resultPage;
    }

    /**
     * getCustomerSession
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * getAffiliateUserFactory
     *
     * @return Webkul\Affiliate\Model\UserFactory
     */
    protected function getAffiliateUserFactory()
    {
        return $this->userFactory;
    }

    /**
     * getResultPageFactory
     *
     * @return Magento\Framework\View\Result\PageFactory
     */
    protected function getResultPageFactory()
    {
        return $this->resultPageFactory;
    }

    /**
     * saveObject
     * @return void
     */
    protected function saveObject($object)
    {
        $object->save();
    }
}
