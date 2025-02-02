<?php

namespace Jazz\JazzCashC\Controller\Index;

use Magento\Sales\Model\Order;
use Magento\Framework\Controller\ResultFactory;

class Rmessage extends \Magento\Framework\App\Action\Action {
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->_resultPageFactory = $resultPageFactory;
		$this->_resultFactory = $context->getResultFactory ();
		parent::__construct ( $context );
	}
	public function execute() {
		
		$resultPage = $this->_resultPageFactory->create ();
		$block = $resultPage->getLayout ()->getBlock ( 'jazzcashc_rmessage' )->toHtml ();
		// $this->getResponse()->setBody( $block );
		return $this->_resultPageFactory->create ( $block );	
		
	}
	
	
}