<?php
/**
* @author  Khurram Malik <khurram.malik@webworks.pk>
* @category    SmartLane Connect
* @package     Webworks\SmartLane
* @copyright   Copyright (c) 2017 Webworks.pk
*/


namespace Webworks\SmartLane\Controller\Adminhtml\OrderInvoice;

use Braintree\Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Psr\Log\LoggerInterface;
use Webworks\SmartLane\Helper\Data;
use Webworks\SmartLane\Model\OrderInvoiceRepository;


class OrderInvoice extends AbstractMassAction
{
/**
* @const: API_FAILED_CODE
**/
const API_FAILED_CODE = 422;
/**
* @const: API_SUCCESS_CODE
**/
const API_SUCCESS_CODE = 200;
/**
* @const: API_EXCEPTION_CODE
**/
const API_EXCEPTION_CODE = 500;
/**
* @var OrderManagementInterface
*/
protected $orderManagement;
/**
* @var OrderFactory
*/
protected $orderFactory;
/**
* @var CollectionFactory
*/

protected $collectionFactory;
/**
* @var LoggerInterface
*/

protected $logger;
protected $dataPersistor;
/**
* @var Data
*/

protected $smarthelper;
protected $orderInvoiceRepository;
protected $messageManager;


    /**
     * OrderInvoice constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param OrderFactory $orderFactory
     * @param LoggerInterface $logger
     * @param Data $smarthelper
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param OrderInvoiceRepository $orderInvoiceRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
    Context $context,
    Filter $filter,
    CollectionFactory $collectionFactory,
    OrderManagementInterface $orderManagement,
    OrderFactory $orderFactory,
    LoggerInterface $logger,
    Data $smarthelper,
    \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
    OrderInvoiceRepository $orderInvoiceRepository,
    \Magento\Framework\Message\ManagerInterface $messageManager
)
{
    parent::__construct($context, $filter);
    $this->collectionFactory = $collectionFactory;
    $this->orderManagement = $orderManagement;
    $this->orderFactory = $orderFactory;
    $this->logger = $logger;
    $this->smarthelper = $smarthelper;
    $this->dataPersistor = $dataPersistor;
    $this->orderInvoiceRepository = $orderInvoiceRepository;
    $this->messageManager = $messageManager;
}

/**
* Hold selected orders
*
* @param AbstractCollection $collection
* @return Redirect
*/
protected function massAction(AbstractCollection $collection)
{
    $status  = $this->smarthelper->createOrderStatusWithProcessingState('smartlane_invoice', 'SmartLane Invoice');
    $errors  = [];
    $success = [];
    if($status === true){
        foreach ($collection->getItems() as $order) {
            $res = $this->orderInvoiceRepository->createInvoice($order);
            if($res === true)
            {
                $success[] = $order->getIncrementId();

            }else{
                $errors[] = $order->getIncrementId();
            }
        }
    }

    if(count($errors) > 0){
        $this->messageManager->addErrorMessage("Following Order(s) Invoice already exists or cant be created ( ". implode(',', $errors)." )");
    }
    if(count($success) > 0){
        $this->messageManager->addSuccessMessage("Following Order(s) Invoice successfully created ( ". implode(',', $success)." )");
    }
    $resultRedirect = $this->resultRedirectFactory->create();
    $resultRedirect->setPath('sales/order/index/*/');

    return $resultRedirect;
    }

}
