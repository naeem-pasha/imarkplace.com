<?php
/**
 * Webkul MpAffiliate User BecomeAffiliate controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

class BecomeAffiliate extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        UserFactory $userFactory,
        \Magento\Eav\Model\Entity $eavEntity,
        CollectionFactory $attributeCollection,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->affDataHelper = $affDataHelper;
        $this->userFactory = $userFactory;
        $this->affDataHelper = $affDataHelper;
        $this->_eavEntity = $eavEntity;
        $this->_attributeCollection = $attributeCollection;
        $this->_customerRepository = $customerRepository;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_customerMapper = $customerMapper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Request for affiliate
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $customer = $this->customerSession->getCustomer();
            if ($this->getRequest()->isPost()) {
                $customerId = $customer->getId();
                $customerName = $customer->getFirstname()." ".$customer->getLastname();
                $customerEmail = $customer->getEmail();
                $affiliateColl = $this->userFactory->create()->getCollection()
                                        ->addFieldToFilter('customer_id', $customerId);
                $postValues = $this->getRequest()->getPostValue();
                $affiConfig = $this->affDataHelper->getAffiliateConfig();
                $autoApprove = $affiConfig['auto_approve'];
                if (isset($postValues['aff_conf']) && $postValues['aff_conf'] == 1) {
                    $affiUserTmp = $this->userFactory->create();
                    $affData = [
                        'customer_id' => $customerId,
                        'aff_name' => $customerName,
                        'aff_email' => $customerEmail,
                        'status' => $autoApprove,
                    ];
                    $affiUserTmp->setData($affData);
                    $affiUserTmp->save();
                    //customer attribute
                    if (isset($postValues['blog_url'])) {
                        $postValues['affiliate_blog_url'] = $postValues['blog_url'];
                    }
                    $postValues['affiliate_paypal_id'] = "";
                    $postValues['is_affiliate'] = 1;
                    $typeId = $this->_eavEntity->setType('customer')->getTypeId();
                    $collection = $this->_attributeCollection->create()
                    ->setEntityTypeFilter($typeId)
                    ->addVisibleFilter()
                    ->addFilter('is_user_defined', 1)
                    ->setOrder('sort_order', 'ASC');
                    $savedCustomerData = $this->_customerRepository->getById($customerId);
                    $customer = $this->_customerDataFactory->create();
                    $customData = array_merge(
                        $this->_customerMapper->toFlatArray($savedCustomerData),
                        $postValues
                    );
                    $customData['id'] = $customerId;
                    $this->_dataObjectHelper->populateWithArray(
                        $customer,
                        $customData,
                        \Magento\Customer\Api\Data\CustomerInterface::class
                    );
                    $this->_customerRepository->save($customer);
                    $this->messageManager->addSuccess(__('Successfully applied for affiliate user.'));
                } else {
                    $this->messageManager->addError(__('Accept affiliate terms for register as affiliate user.'));
                }
                $resultRedirect = $this->resultRedirectFactory->create();
                $defaultUrl = $this->_url->getUrl('*/*/status', ['_secure' => true]);
                $resultRedirect->setUrl($defaultUrl);
                return $resultRedirect;
            } else {
                $affiUserColl = $this->userFactory->create()->getCollection()
                                            ->addFieldToFilter('customer_id', $customer->getId());
                $redirect = true;
                if ($affiUserColl->getSize()) {
                    foreach ($affiUserColl as $affiliateUser) {
                        $redirect = $affiliateUser->getStatus() ? false : true;
                    }
                }
                if (!$redirect) {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $defaultUrl = $this->_url->getUrl('*/*/status', ['_secure' => true]);
                    $resultRedirect->setUrl($defaultUrl);
                    return $resultRedirect;
                }
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__('Affiliate Request Panel'));
                return $resultPage;
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addException(
                $e,
                __($e->getMessage())
            );
        }
    }
}
