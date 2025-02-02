<?php

namespace Webworks\SmartLane\Setup;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * Custom Processing Order-Status code
     */
    const ORDER_STATUS_SMARTLANE_BOOK = 'smartlane_book';
    const ORDER_STATUS_SMARTLANE_DISPATCHED = 'smartlane_dispatched';
    const ORDER_STATUS_SMARTLANE_DELIVERED = 'smartlane_delivered';
    const ORDER_STATUS_SMARTLANE_RETURNED = 'smartlane_returned';
    const ORDER_STATUS_SMARTLANE_REFUSED = 'smartlane_refused';
    const ORDER_STATUS_SMARTLANE_READYFORPICKUP= 'smartlane_ready';
    const ORDER_STATUS_SMARTLANE_ATTEMPTDELIVERY= 'smartlane_attempted_delivery';
    const ORDER_STATUS_SMARTLANE_CANCEL = 'smartlane_cancel';

    /**
     * Custom Processing Order-Status label
     */
    const ORDER_STATUS_SMARTLANE_BOOK_LABEL = 'SmartLane Book Now';
    const ORDER_STATUS_SMARTLANE_DISPATCHED_LABEL = 'SmartLane Dispatched';
    const ORDER_STATUS_SMARTLANE_DELIVERED_LABEL = 'SmartLane Delivered';
    const ORDER_STATUS_SMARTLANE_RETURNED_LABEL = 'SmartLane Returned';
    const ORDER_STATUS_SMARTLANE_REFUSED_LABEL = 'Smartlane Customer Refused';
    const ORDER_STATUS_SMARTLANE_READYFORPICKUP_LABEL= 'SmartLane Ready for pickup';
    const ORDER_STATUS_SMARTLANE_ATTEMPTED_DELIVERY_LABEL= 'SmartLane Attempted Delivery';
    const ORDER_STATUS_SMARTLANE_CANCEL_LABEL= 'SmartLane Cancel';

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;


    /**
      * @var ConfigBasedIntegrationManager
      */

    private $integrationManager;

    protected $installer;
    protected $moduleContext;
    protected $state;

    /**
     * InstallData constructor
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        \Magento\Framework\App\State $state

    )
    {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->state = $state;
        //$this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer = $setup;
        $this->moduleContext = $context;
        $this->addNewOrderProcessingStatus();
    }

    /**
     * Create new order processing status and assign it to the existent state
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addNewOrderProcessingStatus()
    {
        $newSLStatuses = array(
            'book' => array('status' => self::ORDER_STATUS_SMARTLANE_BOOK, 'label' => self::ORDER_STATUS_SMARTLANE_BOOK_LABEL, 'state' => Order::STATE_PROCESSING),
            'ready'=> array('status' => self::ORDER_STATUS_SMARTLANE_READYFORPICKUP, 'label' => self::ORDER_STATUS_SMARTLANE_READYFORPICKUP_LABEL, 'state' => Order::STATE_PROCESSING),
            'attempted'=> array('status' => self::ORDER_STATUS_SMARTLANE_ATTEMPTDELIVERY, 'label' => self::ORDER_STATUS_SMARTLANE_ATTEMPTED_DELIVERY_LABEL, 'state' => Order::STATE_PROCESSING),
            'cancel'=> array('status' => self::ORDER_STATUS_SMARTLANE_CANCEL, 'label' => self::ORDER_STATUS_SMARTLANE_CANCEL_LABEL, 'state' => Order::STATE_PROCESSING),
            'dispatched' => array('status' => self::ORDER_STATUS_SMARTLANE_DISPATCHED, 'label' => self::ORDER_STATUS_SMARTLANE_DISPATCHED_LABEL, 'state' => Order::STATE_PROCESSING),
            'delivered' => array('status' => self::ORDER_STATUS_SMARTLANE_DELIVERED, 'label' => self::ORDER_STATUS_SMARTLANE_DELIVERED_LABEL, 'state' => Order::STATE_COMPLETE),
            'returned' => array('status' => self::ORDER_STATUS_SMARTLANE_RETURNED, 'label' => self::ORDER_STATUS_SMARTLANE_RETURNED_LABEL, 'state' => Order::STATE_COMPLETE),
            'refused' => array('status' => self::ORDER_STATUS_SMARTLANE_REFUSED, 'label' => self::ORDER_STATUS_SMARTLANE_REFUSED_LABEL, 'state' => Order::STATE_COMPLETE)
        );

        foreach ($newSLStatuses as $newStatus) {
            /** @var StatusResource $statusResource */
            $statusResource = $this->statusResourceFactory->create();
            /** @var Status $status */
            $status = $this->statusFactory->create();
            $status->setData([
                'status' => $newStatus['status'],
                'label' => $newStatus['label']
            ]);
            try {
                $statusResource->save($status);
            } catch (AlreadyExistsException $exception) {

                return;
            }
            $status->assignState($newStatus['state'], false, true);
        }
    }


}