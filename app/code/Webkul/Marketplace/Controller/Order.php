<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Webkul\Marketplace\Helper\Notification as NotificationHelper;
use Webkul\Marketplace\Model\Notification;
use Webkul\Marketplace\Helper\Data as HelperData;
use Webkul\Marketplace\Model\SaleslistFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Webkul\Marketplace\Model\OrdersFactory as MpOrdersModel;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use Webkul\Marketplace\Model\SellerFactory as MpSellerModel;

abstract class Order extends Action
{
    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;

    /**
     * @var ShipmentSender
     */
    protected $_shipmentSender;

    /**
     * @var ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * @var Shipment
     */
    protected $_shipment;

    /**
     * @var CreditmemoSender
     */
    protected $_creditmemoSender;

    /**
     * @var CreditmemoRepositoryInterface;
     */
    protected $_creditmemoRepository;

    /**
     * @var CreditmemoFactory;
     */
    protected $_creditmemoFactory;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    /**
     * @var StockConfigurationInterface
     */
    protected $_stockConfiguration;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var OrderManagementInterface
     */
    protected $_orderManagement;

    /**
     * @var \Webkul\Marketplace\Helper\Orders
     */
    protected $orderHelper;

    /**
     * @var NotificationHelper
     */
    protected $notificationHelper;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    protected $creditmemoManagement;

    /**
     * @var SaleslistFactory
     */
    protected $saleslistFactory;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Webkul\Marketplace\Model\Order\Pdf\Creditmemo
     */
    protected $creditmemoPdf;

    /**
     * @var \Webkul\Marketplace\Model\Order\Pdf\Invoice
     */
    protected $invoicePdf;

    /**
     * @var MpOrdersModel
     */
    protected $mpOrdersModel;

    /**
     * @var InvoiceCollection
     */
    protected $invoiceCollection;

    /**
     * @var \Magento\Sales\Api\InvoiceManagementInterface
     */
    protected $invoiceManagement;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModel;

    /**
     * @var MpSellerModel
     */
    protected $mpSellerModel;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param Context                                          $context
     * @param PageFactory                                      $resultPageFactory
     * @param InvoiceSender                                    $invoiceSender
     * @param ShipmentSender                                   $shipmentSender
     * @param ShipmentFactory                                  $shipmentFactory
     * @param Shipment                                         $shipment
     * @param CreditmemoSender                                 $creditmemoSender
     * @param CreditmemoRepositoryInterface                    $creditmemoRepository
     * @param CreditmemoFactory                                $creditmemoFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface    $invoiceRepository
     * @param StockConfigurationInterface                      $stockConfiguration
     * @param OrderRepositoryInterface                         $orderRepository
     * @param OrderManagementInterface                         $orderManagement
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Webkul\Marketplace\Helper\Orders                $orderHelper
     * @param NotificationHelper                               $notificationHelper
     * @param HelperData                                       $helper
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param SaleslistFactory                                 $saleslistFactory
     * @param CustomerUrl                                      $customerUrl
     * @param DateTime                                         $date
     * @param FileFactory                                      $fileFactory
     * @param \Webkul\Marketplace\Model\Order\Pdf\Creditmemo   $creditmemoPdf
     * @param \Webkul\Marketplace\Model\Order\Pdf\Invoice      $invoicePdf
     * @param MpOrdersModel                                    $mpOrdersModel
     * @param InvoiceCollection                                $invoiceCollection
     * @param \Magento\Sales\Api\InvoiceManagementInterface    $invoiceManagement
     * @param \Magento\Catalog\Model\ProductFactory            $productModel
     * @param MpSellerModel                                    $mpSellerModel
     * @param \Psr\Log\LoggerInterface                         $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        InvoiceSender $invoiceSender,
        ShipmentSender $shipmentSender,
        ShipmentFactory $shipmentFactory,
        Shipment $shipment,
        CreditmemoSender $creditmemoSender,
        CreditmemoRepositoryInterface $creditmemoRepository,
        CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        StockConfigurationInterface $stockConfiguration,
        OrderRepositoryInterface $orderRepository,
        OrderManagementInterface $orderManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\Marketplace\Helper\Orders $orderHelper,
        NotificationHelper $notificationHelper,
        HelperData $helper,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        SaleslistFactory $saleslistFactory,
        CustomerUrl $customerUrl,
        DateTime $date,
        FileFactory $fileFactory,
        \Webkul\Marketplace\Model\Order\Pdf\Creditmemo $creditmemoPdf,
        \Webkul\Marketplace\Model\Order\Pdf\Invoice $invoicePdf,
        MpOrdersModel $mpOrdersModel,
        InvoiceCollection $invoiceCollection,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Catalog\Model\ProductFactory $productModel,
        MpSellerModel $mpSellerModel,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_invoiceSender = $invoiceSender;
        $this->_shipmentSender = $shipmentSender;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_shipment = $shipment;
        $this->_creditmemoSender = $creditmemoSender;
        $this->_creditmemoRepository = $creditmemoRepository;
        $this->_creditmemoFactory = $creditmemoFactory;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_stockConfiguration = $stockConfiguration;
        $this->_orderRepository = $orderRepository;
        $this->_orderManagement = $orderManagement;
        $this->_customerSession = $customerSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->orderHelper = $orderHelper;
        $this->notificationHelper = $notificationHelper;
        $this->helper = $helper;
        $this->creditmemoManagement = $creditmemoManagement;
        $this->saleslistFactory = $saleslistFactory;
        $this->customerUrl = $customerUrl;
        $this->date = $date;
        $this->fileFactory = $fileFactory;
        $this->creditmemoPdf = $creditmemoPdf;
        $this->invoicePdf = $invoicePdf;
        $this->mpOrdersModel = $mpOrdersModel;
        $this->invoiceCollection = $invoiceCollection;
        $this->invoiceManagement = $invoiceManagement;
        $this->productModel = $productModel;
        $this->mpSellerModel = $mpSellerModel;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Initialize order model instance.
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $order = $this->_orderRepository->get($id);
            $tracking = $this->orderHelper->getOrderinfo($id);
            if ($tracking) {
                if ($tracking->getOrderId() == $id) {
                    if (!$id) {
                        $this->messageManager->addError(__('This order no longer exists.'));
                        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                        return false;
                    }
                } else {
                    $this->messageManager->addError(__('You are not authorize to manage this order.'));
                    $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                    return false;
                }
            } else {
                $this->messageManager->addError(__('You are not authorize to manage this order.'));
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        } catch (InputException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);

        return $order;
    }

    /**
     * Initialize invoice model instance.
     *
     * @return \Magento\Sales\Api\InvoiceRepositoryInterface|false
     */
    protected function _initInvoice()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$invoiceId) {
            return false;
        }
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->_invoiceRepository->get($invoiceId);
        if (!$invoice) {
            return false;
        }
        try {
            $order = $this->_orderRepository->get($orderId);
            $tracking = $this->orderHelper->getOrderinfo($orderId);
            if ($tracking) {
                if ($tracking->getInvoiceId() == $invoiceId) {
                    if (!$invoiceId) {
                        $this->messageManager->addError(__('The invoice no longer exists.'));
                        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                        return false;
                    }
                } else {
                    $this->messageManager->addError(__('You are not authorize to view this invoice.'));
                    $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                    return false;
                }
            } else {
                $this->messageManager->addError(__('You are not authorize to view this invoice.'));
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        } catch (InputException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        $this->_coreRegistry->register('current_invoice', $invoice);

        return $invoice;
    }

    /**
     * Initialize shipment model instance.
     *
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function _initShipment()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$shipmentId) {
            return false;
        }
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->_shipment->load($shipmentId);
        if (!$shipment) {
            return false;
        }
        try {
            $order = $this->_orderRepository->get($orderId);
            $tracking = $this->orderHelper->getOrderinfo($orderId);
            if ($tracking) {
                if ($tracking->getShipmentId() == $shipmentId) {
                    if (!$shipmentId) {
                        $this->messageManager->addError(__('The shipment no longer exists.'));
                        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                        return false;
                    }
                } else {
                    $this->messageManager->addError(__('You are not authorize to view this shipment.'));
                    $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                    return false;
                }
            } else {
                $this->messageManager->addError(__('You are not authorize to view this shipment.'));
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        } catch (InputException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        $this->_coreRegistry->register('current_shipment', $shipment);

        return $shipment;
    }

    /**
     * Initialize invoice model instance.
     *
     * @return \Magento\Sales\Api\InvoiceRepositoryInterface|false
     */
    protected function _initCreditmemo()
    {
        $creditmemo = false;
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_orderRepository->get($orderId);

        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $this->_creditmemoRepository->get($creditmemoId);
        if (!$creditmemo) {
            return false;
        }
        try {
            $tracking = $this->orderHelper->getOrderinfo($orderId);
            if ($tracking) {
                $creditmemoArr = explode(',', $tracking->getCreditmemoId());
                if (in_array($creditmemoId, $creditmemoArr)) {
                    if (!$creditmemoId) {
                        $this->messageManager->addError(__('The creditmemo no longer exists.'));
                        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                        return false;
                    }
                } else {
                    $this->messageManager->addError(__('You are not authorize to view this creditmemo.'));
                    $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                    return false;
                }
            } else {
                $this->messageManager->addError(__('You are not authorize to view this creditmemo.'));
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->messageManager->addError($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        } catch (InputException $e) {
            $this->helper->logDataInLogger(
                "Controller_Order execute : ".$e->getMessage()
            );
            $this->messageManager->addError($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        $this->_coreRegistry->register('current_creditmemo', $creditmemo);

        return $creditmemo;
    }

    protected function _getItemQtys($order, $items)
    {
        $data = [];
        $subtotal = 0;
        $baseSubtotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)) {
                $data[$item->getItemId()] = intval($item->getQtyOrdered() - ($item->getQtyInvoiced() + $item->getQtyCanceled()));

                $_item = $item;

                // for bundle product
                $bundleitems = array_merge([$_item], $_item->getChildrenItems());

                if ($_item->getParentItem()) {
                    continue;
                }

                if ($_item->getProductType() == 'bundle') {
                    foreach ($bundleitems as $_bundleitem) {
                        if ($_bundleitem->getParentItem()) {
                            $data[$_bundleitem->getItemId()] = intval(
                                $_bundleitem->getQtyOrdered() - ($_bundleitem->getQtyInvoiced() + $_bundleitem->getQtyCanceled())
                            );
                        }
                    }
                }
                $subtotal += $_item->getRowTotal();
                $baseSubtotal += $_item->getBaseRowTotal();
            } else {
                if (!$item->getParentItemId()) {
                    $data[$item->getItemId()] = 0;
                }
            }
        }

        return ['data' => $data,'subtotal' => $subtotal,'baseSubtotal' => $baseSubtotal];
    }

    protected function _getShippingItemQtys($order, $items)
    {
        $data = [];
        $subtotal = 0;
        $baseSubtotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)) {
                $data[$item->getItemId()] = intval($item->getQtyOrdered() - $item->getQtyShipped());

                $_item = $item;

                // for bundle product
                $bundleitems = array_merge([$_item], $_item->getChildrenItems());

                if ($_item->getParentItem()) {
                    continue;
                }

                if ($_item->getProductType() == 'bundle') {
                    foreach ($bundleitems as $_bundleitem) {
                        if ($_bundleitem->getParentItem()) {
                            $data[$_bundleitem->getItemId()] = intval(
                                $_bundleitem->getQtyOrdered() - $item->getQtyShipped()
                            );
                        }
                    }
                }
                $subtotal += $_item->getRowTotal();
                $baseSubtotal += $_item->getBaseRowTotal();
            } else {
                if (!$item->getParentItemId()) {
                    $data[$item->getItemId()] = 0;
                }
            }
        }

        return ['data' => $data,'subtotal' => $subtotal,'baseSubtotal' => $baseSubtotal];
    }

    protected function isAllItemInvoiced($order)
    {
        $flag = 1;
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            } elseif ($item->getProductType() == 'bundle') {
                // for bundle product
                $bundleitems = array_merge([$item], $item->getChildrenItems());
                foreach ($bundleitems as $bundleitem) {
                    if ($bundleitem->getParentItem()) {
                        if (intval($bundleitem->getQtyOrdered() - $item->getQtyInvoiced())) {
                            $flag = 0;
                        }
                    }
                }
            } else {
                if (intval($item->getQtyOrdered() - $item->getQtyInvoiced())) {
                    $flag = 0;
                }
            }
        }

        return $flag;
    }

    /**
     * Updated notification, mark as read.
     */
    protected function _updateNotification()
    {
        $orderId = $this->_coreRegistry->registry('current_order')->getId();
        $orderData = $this->orderHelper->getOrderinfo($orderId);
        $type = Notification::TYPE_ORDER;
        $this->notificationHelper->updateNotification(
            $orderData,
            $type
        );
    }
}
