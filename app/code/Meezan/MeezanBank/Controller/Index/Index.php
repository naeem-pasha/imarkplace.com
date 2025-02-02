<?php
	namespace Meezan\MeezanBank\Controller\Index;
	use Magento\Sales\Model\Order;
	use Magento\Framework\Controller\ResultFactory;
	class Index extends \Magento\Framework\App\Action\Action {
		public function __construct (\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
			$this->_resultPageFactory = $resultPageFactory;
			$this->_resultFactory = $context->getResultFactory();
			parent::__construct($context);
		} // END OF __construct FUNCTION
		public function execute() {
			$resultPage = $this->_resultPageFactory->create();
			$block = $resultPage->getLayout()->getBlock('meezanbank_index_index')->toHtml();
			return  $this->_resultPageFactory->create ( $block );
		} // END OF execute FUNCTION
	} // END OF Index CLASS