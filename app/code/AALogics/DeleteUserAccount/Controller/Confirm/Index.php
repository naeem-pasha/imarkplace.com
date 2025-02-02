<?php
namespace AALogics\DeleteUserAccount\Controller\Confirm;
use \Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{

	/**
	 * @var \Magento\Backend\Model\Auth\Session
	*/
    private $authSession;
	protected $_pageFactory;
	protected $_customerRepository;
	protected $request;
	protected $_registry;
	protected $_messageManager;
	// Redirect variables
	protected $_customerSession;
	protected $_urlInterface;
	protected $_resultFactory;


	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
		\Magento\Customer\Model\Session $authSession,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\UrlInterface $urlInterface,
		ResultFactory $resultFactory
	){
		$this->request = $request;
		$this->_pageFactory = $pageFactory;
		$this->authSession = $authSession;
		$this->_customerRepository = $customerRepository;
		$this->_registry = $registry;
		$this->_messageManager = $messageManager;
		$this->_customerSession = $customerSession;
		$this->_urlInterface = $urlInterface;
		$this->_resultFactory = $resultFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		try {
			$customerData = $this->authSession->getCustomer()->getData();
            $customerId = $this->request->getParam('entity_id');

			if($customerData && $customerData['entity_id'] == $customerId)
			{
				$this->_registry->register('isSecureArea', true);
				$customer = $this->_customerRepository->deleteById($customerId);

				$this->_messageManager->addSuccessMessage('Account deleted successfully.');
				$resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setUrl($this->_urlInterface->getUrl('customer/account/'));
				return $resultRedirect;
			}else{
				$this->_messageManager->addErrorMessage('An error occurred please try again later.');
				$resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setUrl($this->_urlInterface->getUrl('customer/account/'));
				return $resultRedirect;
			}
			
			
		} catch (\Exception $e) {
			echo "Error for Id :  ". $customerId ."  ". $e->getMessage();
		}
       
	}
}
