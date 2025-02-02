<?php
namespace Paypro\PayproCheckout\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Status extends \Paypro\PayproCheckout\Controller\Index\Index
{
    public function execute()
    {
		$resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$status = $this->getRequest()->getParam('status');
        $desc = $this->getRequest()->getParam('desc');
        $orderRefNumber = $this->getRequest()->getParam('orderRefNumber');
		$resultRedirect = $this->status($status,$desc,$orderRefNumber);
        echo $resultRedirect;
//		return $resultRedirect;
	}
}