<?php

namespace Webworks\SmartLane\Setup;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;


/**
 * Class UpgradeData
 *
 * @package Ayakil\OrdersModification\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    private $salesSetupFactory;
    protected $installer;
    protected $moduleContext;
    protected $logger;

    /**
     * Custom Processing Order-Status code
     */
    const ORDER_STATUS_SMARTLANE_ON_HOLD = 'smartlane_on_hold';
    const ORDER_STATUS_SMARTLANE_UN_HOLD = 'smartlane_un_hold';

    /**
     * Custom Processing Order-Status label
     */
    const ORDER_STATUS_SMARTLANE_ON_HOLD_LABEL = 'SmartLane On-Hold';
    const ORDER_STATUS_SMARTLANE_UN_HOLD_LABEL = 'SmartLane Un-Hold';

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

    protected $state;

    /**
     * Constructor
     *
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        LoggerInterface $logger,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        \Magento\Framework\App\State $state
    )
    {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->logger = $logger;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->state = $state;
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $this->installer = $setup;
        $this->moduleContext = $context;
        $this->createCourierTrackingInfoAttribute();
        $this->createSmartlaneOnHoldStatus();
        $this->createSmartlaneUnHoldStatus();


    }

    /**
     *
     */
    protected function createCourierTrackingInfoAttribute(){

        if (version_compare($this->moduleContext->getVersion(), "1.0.9", "<")) {
            $salesSetup = $this->salesSetupFactory->create(['setup' => $this->installer]);
            $salesSetup->addAttribute(
                'order',
                'smartlane_courier_tracking_info',
                [
                    'type' => 'text',
                    'visible' => false,
                    'required' => false,
                    'grid' => true
                ]
            );
        }

    }

    /**
     *
     */
    protected function createSmartlaneOnHoldStatus(){

        if (version_compare($this->moduleContext->getVersion(), "2.0.0", "<")) {
            $newSLStatuses = array(
                'on-hold' => array('status' => self::ORDER_STATUS_SMARTLANE_ON_HOLD, 'label' => self::ORDER_STATUS_SMARTLANE_ON_HOLD_LABEL, 'state' => Order::STATE_PROCESSING),
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

    /**
     *
     */
    protected function createSmartlaneUnHoldStatus(){

        if (version_compare($this->moduleContext->getVersion(), "2.3.0", "<")) {
            $newSLStatuses = array(
                'un-hold' => array('status' => self::ORDER_STATUS_SMARTLANE_UN_HOLD, 'label' => self::ORDER_STATUS_SMARTLANE_UN_HOLD_LABEL, 'state' => Order::STATE_PROCESSING),
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
}