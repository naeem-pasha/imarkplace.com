<?php

namespace Apps\Payfast\Controller\Response;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Apps\Payfast\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http;
use Magento\Sales\Model\Order\Payment\Transaction\Builder as TransactionBuilder;
use Magento\Sales\Model\Order\Payment\Transaction;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_objectmanager;
    protected $_checkoutSession;
    protected $_orderFactory;
    protected $urlBuilder;
    private $logger;
    protected $response;
    protected $config;
    protected $messageManager;
    protected $transactionRepository;
    protected $transactionBuilder;
    protected $cart;
    protected $inbox;

    public function __construct(Context $context, Session $checkoutSession, OrderFactory $orderFactory, Logger $logger, ScopeConfigInterface $scopeConfig, Http $response, TransactionBuilder $tb, \Magento\Checkout\Model\Cart $cart, \Magento\AdminNotification\Model\Inbox $inbox, \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
    ) {


        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->response = $response;
        $this->config = $scopeConfig;
        $this->transactionBuilder = $tb;
        $this->logger = $logger;
        $this->cart = $cart;
        $this->inbox = $inbox;
        $this->transactionRepository = $transactionRepository;
        $this->urlBuilder = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\UrlInterface');

        parent::__construct($context);
    }

    public function execute() {

        $basketId = $this->getRequest()->getParam('basket_id');
        $errorMsg = $this->getRequest()->getParam('err_msg');
        $errorCode = $this->getRequest()->getParam('err_code');
        $rdvMessageKey = $this->getRequest()->getParam('Rdv_Message_Key');
        $transactionId = $this->getRequest()->getParam('transaction_id');

        $transactionId = !empty($transactionId) ? $transactionId : $basketId;

        $order = $this->orderFactory->create()->load($basketId);

        if (!$order) {
            $this->_redirect($this->urlBuilder->getUrl('checkout/cart', ['_secure' => true]));
            return;
        }

        $currentOrderState = $order->getState();

        if ($currentOrderState !== Order::STATE_NEW) {
            $this->_redirect($this->urlBuilder->getUrl('checkout/cart', ['_secure' => true]));
            return;
        }


        $paymentData = [];
        $paymentData = [
            'PayFast Transaction ID' => $transactionId,
            'PayFast RDV Message Key' => $rdvMessageKey,
            'PayFast Error Code' => $errorCode,
            'PayFast Error Message' => $errorMsg
        ];

        $payment = $order->getPayment();


        if ($errorCode == '000') {
            $order->setState(Order::STATE_PROCESSING)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PAYMENT_REVIEW));
        } else {
            $order->setState(Order::STATE_PROCESSING)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CANCELED));
        }


        $trans = $this->transactionBuilder;

        $transaction = $trans->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($transactionId)
                ->setFailSafe(true)
                ->setAdditionalInformation(
                        [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
                )
                //build method creates the transaction and returns the object
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT);


        $transaction->save();


        if ($errorCode !== '000') {
            $payment->addTransactionCommentsToOrder(
                    $transaction, "PayFast transaction was not successful."
            );
            $order->cancel();
        } else {
            $payment->addTransactionCommentsToOrder(
                    $transaction, "PayFast transaction is completed successfully."
            );
            
            $order->setCanSendNewEmailFlag(true);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $objectManager->create('Magento\Sales\Model\OrderNotifier')->notify($order);
        }

        

        $order->save();
        $payment->save();

        if ($errorCode === '000') {
            $this->_redirect($this->urlBuilder->getUrl('checkout/onepage/success/', ['_secure' => true]));
        } else {
            $this->_redirect($this->urlBuilder->getUrl('checkout/cart', ['_secure' => true]));
        }
        return;
    }

}
