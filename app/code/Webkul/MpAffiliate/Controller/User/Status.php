<?php
/**
 * Webkul MpAffiliate User Status controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

class Status extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Webkul\MpAffiliate\Helper\Data
     */
    private $affDataHelper;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserBalanceFactory
     */
    private $userBalance;

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
        UserBalanceFactory $userBalance,
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
        $this->userBalance = $userBalance;
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
     * Add Auction on product page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $customer = $this->customerSession->getCustomer();
            $customerId = $customer->getId();
            if ($this->getRequest()->isPost()) {
                $postValues = $this->getRequest()->getPostValue();
                //customer attribute
                if (isset($postValues['blog_url']) && $postValues['blog_url']!="") {
                    $flag = $this->validateBlogUrl($postValues['blog_url']);
                    if ($flag) {
                        try {
                            $postValues['affiliate_blog_url'] = $postValues['blog_url'];
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
                        } catch (\Exception $e) {
                            $this->logger->info($e->getMessage());
                        }
                        $this->messageManager->addSuccess(__('Successfully blog url updated.'));
                    } else {
                        $this->messageManager->addError(__('Please enter valid blog url.'));
                    }
                } else {
                    $this->messageManager->addError(__('Please enter blog url.'));
                }
                $resultRedirect = $this->resultRedirectFactory->create();
                $defaultUrl = $this->_url->getUrl('*/*/status', ['_secure' => true]);
                $resultRedirect->setUrl($defaultUrl);
                return $resultRedirect;
            } else {
                $affiliateColl = $this->userFactory->create()->getCollection()
                                        ->addFieldToFilter('customer_id', $customerId);
                $redirect = true;
                if ($affiliateColl->getSize()) {
                    foreach ($affiliateColl as $affiliateUser) {
                        $redirect = $affiliateUser->getStatus() ? false : true;
                    }
                }
                if ($redirect) {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $defaultUrl = $this->_url->getUrl('*/*/becomeaffiliate', ['_secure' => true]);
                    $resultRedirect->setUrl($defaultUrl);
                    return $resultRedirect;
                }
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__('Affiliate Blog Link'));
                return $resultPage;
            }
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('We can\'t save the customer.')
            );
        }
    }

    public function validateBlogUrl($blogUrl)
    {
        $flag = 0;
        if (filter_var($blogUrl, FILTER_VALIDATE_URL)) {
            $flag = 1;
        }
        return $flag;
    }
}
