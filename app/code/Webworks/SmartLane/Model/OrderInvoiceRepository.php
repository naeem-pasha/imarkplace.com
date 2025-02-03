<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 8/25/20
 * Time: 10:34 AM
 */

namespace Webworks\SmartLane\Model;


use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Psr\Log\LoggerInterface;

class OrderInvoiceRepository
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $_invoiceCollectionFactory;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;
    protected $orderFactory;

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
    protected $messageManager;
    protected $logger;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param OrderFactory $orderFactory
     * @param StatusResourceFactory $statusResourceFactory
     * @param StatusFactory $statusFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory,
        StatusResourceFactory $statusResourceFactory,
        StatusFactory $statusFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        LoggerInterface  $logger

    ) {
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_invoiceService = $invoiceService;
        $this->_transactionFactory = $transactionFactory;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->statusFactory = $statusFactory;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }


    /**
     * @param $orderData
     * @return bool
     */
    public function createInvoice($orderData)
    {
        try {
            $order = $this->orderFactory->create()->load($orderData->getEntityId());
            if ($order){
                $invoices = $this->_invoiceCollectionFactory->create()
                    ->addAttributeToFilter('order_id', array('eq' => $order->getId()));
                $invoices->getSelect()->limit(1);
                if ((int)$invoices->count() !== 0) {
                    $invoices = $invoices->getFirstItem();
                    $invoice = $this->_invoiceRepository->get($invoices->getId());
                    return false;
                }

                if(!$order->canInvoice()) {
                    return false;
                }

                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                $invoice->register();
                $invoice->getOrder()->setCustomerNoteNotify(false);
                $invoice->getOrder()->setState("processing");
                $invoice->getOrder()->setStatus("smartlane_invoice");
                $order->addStatusHistoryComment(__('Automatically INVOICED'), false);
                $transactionSave = $this->_transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();

                return true;
        }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}