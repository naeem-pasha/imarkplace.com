<?php
namespace AALogics\DeleteUserAccount\Controller\Delete;
use \Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\UrlInterface
	*/
    protected $url;

    /**
     * @param \Magento\Framework\UrlInterface $url
    */

	protected $_pageFactory;
	// Redirect variables
	protected $_customerSession;
	protected $_urlInterface;
	protected $_resultFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\UrlInterface $urlInterface,
		ResultFactory $resultFactory
		)
	{
		$this->_pageFactory = $pageFactory;
		$this->_customerSession = $customerSession;
		$this->_urlInterface = $urlInterface;
		$this->_resultFactory = $resultFactory;

		return parent::__construct($context);
	}

	public function execute()
	{
		$this->_view->loadLayout(); 
		$this->_view->renderLayout(); 

		if(!$this->_customerSession->isLoggedIn())
		{
			$resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setUrl($this->_urlInterface->getUrl('customer/account/login'));
        	return $resultRedirect;			
		}

		return $this->_pageFactory->create();
	}
}
