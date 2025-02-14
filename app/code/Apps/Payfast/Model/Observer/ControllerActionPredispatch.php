<?php

namespace Apps\Payfast\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

# added this observer if javascript don't redirect to apps/redirect url.

class ControllerActionPredispatch implements ObserverInterface {

    protected $checkoutSession;
    protected $orderFactory;

    public function __construct(
    Session $checkoutSession, OrderFactory $orderFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $request = $observer->getData('request');
        if ($request->getModuleName() == "checkout" and $request->getActionName() == "success") {
            $orderId = $this->checkoutSession->getLastOrderId();
            if ($orderId) {
                $order = $this->orderFactory->create()->load($orderId);
                if ($order->getPayment()->getMethodInstance()->getCode() == "payfast" and $order->getState() == Order::STATE_NEW) {
                    $this->urlBuilder = \Magento\Framework\App\ObjectManager::getInstance()
                            ->get('Magento\Framework\UrlInterface');
                    $url = $this->urlBuilder->getUrl("payfast/redirect");
                    header("Location:$url");

                    exit;
                }
            }
        }
    }

}
