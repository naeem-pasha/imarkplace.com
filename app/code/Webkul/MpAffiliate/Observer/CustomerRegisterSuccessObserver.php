<?php
/**
 * Webkul MpAffiliate User Register Observer.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Webkul\MpAffiliate\Model\UserFactory;
use Webkul\MpAffiliate\Model\UserBalanceFactory;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Magento\Customer\Model\Session;
use \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Webkul MpAffiliate CustomerRegisterSuccessObserver Observer.
 */
class CustomerRegisterSuccessObserver implements ObserverInterface
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    private $customerSession;

    /**
     * @var userBalance
     */
    private $userBalance;

    /**
     * @var ManagerInterface
     */
    private $managerInterface;

    /**
     * @var AffDataHelper
     */
    private $affDataHelper;

    /**
     * @param UserFactory         $userFactory
     * @param UserBalanceFactory  $userBalance
     * @param ManagerInterface    $managerInterface
     * @param AffDataHelper       $affDataHelper
     */
    public function __construct(
        UserFactory $userFactory,
        UserBalanceFactory $userBalance,
        ManagerInterface $managerInterface,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        \Magento\Eav\Model\Entity $eavEntity,
        CollectionFactory $attributeCollection,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->customerSession = $customerSession;
        $this->userFactory = $userFactory;
        $this->userBalance = $userBalance;
        $this->messageManager = $managerInterface;
        $this->affDataHelper = $affDataHelper;
        $this->_eavEntity = $eavEntity;
        $this->_attributeCollection = $attributeCollection;
        $this->_customerRepository = $customerRepository;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_customerMapper = $customerMapper;
        $this->logger = $logger;
    }

    /**
     * customer register event handler.
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer['account_controller'];
        try {
            $paramData = $data->getRequest()->getParams();
            if (!empty($paramData['aff_conf']) && $paramData['aff_conf'] == 1) {
                $customer = $observer->getCustomer();
                $customerId = $customer->getId();
                $customerEmail = $paramData['email'];
                $customer->getEmail();
                $customerName = $paramData['firstname']." ".$paramData['lastname'];
                $affiliateColl = $this->userFactory->create()->getCollection()
                                                    ->addFieldToFilter('customer_id', $customerId);
                if ($affiliateColl->getSize() == 0) {
                    $affiConfig = $this->affDataHelper->getAffiliateConfig();
                    $status = $affiConfig['auto_approve'] ?? 0;
                    $affData = [
                        'customer_id' => $customerId,
                        'aff_name' => $customerName,
                        'aff_email' => $customerEmail,
                        'status' => $status
                    ];

                    if (isset($paramData['bloglink'])) {
                        $paramData['affiliate_blog_url'] = $paramData['bloglink'];
                    }
                    $paramData['affiliate_paypal_id'] = "";
                    $paramData['is_affiliate'] = 1;

                    $tempAff = $this->userFactory->create();
                    $tempAff->setData($affData);
                    $tempAff->save();

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
                        $paramData
                    );
                    $customData['id'] = $customerId;
                    $this->_dataObjectHelper->populateWithArray(
                        $customer,
                        $customData,
                        \Magento\Customer\Api\Data\CustomerInterface::class
                    );
                    $this->_customerRepository->save($customer);
                    $this->messageManager->addSuccess(__('Affiliate account created successfully.'));
                }
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        }
    }
}
