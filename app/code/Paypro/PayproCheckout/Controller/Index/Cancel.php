<?php
namespace Paypro\PayproCheckout\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Cancel extends \Paypro\PayproCheckout\Controller\Index\Index
{
    public function execute()
    {
        $orderID = $this->getRequest()->getParam('merchant_order_id');
        $key = $this->getRequest()->getParam('key');
        $tcode = $this->getRequest()->getParam('tcode');
        $transactionID = $this->getRequest()->getParam('tpaycode');
        $this->cancelPayProOrder($orderID,$key,$tcode,$transactionID);
	}
}