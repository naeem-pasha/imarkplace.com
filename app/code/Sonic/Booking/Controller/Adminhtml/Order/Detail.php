<?php
namespace Sonic\Booking\Controller\Adminhtml\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
class Detail extends \Magento\Backend\App\Action
{
	/**
	* @var \Magento\Framework\View\Result\PageFactory
	*/
	protected $resultPageFactory;
	/**
	* Constructor
	*
	* @param \Magento\Backend\App\Action\Context $context
	* @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	*/
	public function __construct(
	\Magento\Backend\App\Action\Context $context,
	\Magento\Framework\View\Result\PageFactory $resultPageFactory

	) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	/**
	* Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
	*
	* @return \Magento\Framework\View\Result\Page
	*/
	public function execute()
	{
		
		$resultPage = $this->resultPageFactory->create();

		
        return $resultPage;

	}
}
?>
