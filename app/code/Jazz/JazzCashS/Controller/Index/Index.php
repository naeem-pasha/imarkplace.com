<?php



namespace Jazz\JazzCashS\Controller\Index;



use Magento\Sales\Model\Order;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{

	public function __construct (
			\Magento\Framework\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->_resultPageFactory = $resultPageFactory;
		$this->_resultFactory = $context->getResultFactory();
		parent::__construct($context);
	}

	/**
	 This method executes when place order is clicked on checkout page. It is responsible for 1st redirection.
	 **/
	public function execute() {


			

		$resultPage = $this->_resultPageFactory->create();
		$block = $resultPage->getLayout()->getBlock('jazzcashs_index_index')->toHtml();
		//$this->getResponse()->setBody( $block );
		return  $this->_resultPageFactory->create ( $block );

	}

}