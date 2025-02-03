<?php
namespace AALogics\DeleteUserAccount\Controller\Email;

use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Controller\ResultFactory;

class Send extends \Magento\Framework\App\Action\Action
{
	private $dataPersistor;
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */

    protected $context;
    private $fileUploaderFactory;


    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */


    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    protected $_storeManager;
    protected $_messageManager;
    // Redirect variables 
    protected $_customerSession;
	protected $_urlInterface;
	protected $_resultFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $authSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\UrlInterface $urlInterface,
		ResultFactory $resultFactory

    ) {
        parent::__construct($context);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->authSession = $authSession;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_customerSession = $customerSession;
		$this->_urlInterface = $urlInterface;
		$this->_resultFactory = $resultFactory;
    }


	public function execute()
	{
        try {
      
            $customerData = $this->authSession->getCustomer()->getData(); //get all data of customerData.

            $user_id = $customerData['entity_id'];
            $user_email = $customerData['email'];
            $user_name = $customerData['firstname'].' '.$customerData['middlename'].' '.$customerData['lastname'];
            $url = $this->_storeManager->getStore()->getBaseUrl().'/customer_account/confirm/index?entity_id='.$user_id;
            $fromEmail= 'info@imarkplace.com';
            $fromName = 'IMARK';
    
            $templateVars = [
                'url' => $url,
                'user_name'   => $user_name
            ];

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
    
            $to = $user_email;     
    
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    
             $templateOptions = [
              'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
              'store' => 1
            ];
    
            $transport = $this->_transportBuilder->setTemplateIdentifier('userdelete_template')
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from)
                    ->addTo($to)          
                    ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
    
            $this->_messageManager->addSuccessMessage('Please check your registerd Email ID to confirm and delete your account permanently.');

        } catch (\Exception $e) {
            // echo"<pre>"; print_r($e); exit;
           $this->_messageManager->addErrorMessage('Emial not sent some error occurred. Please try again later.');
        }

        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_urlInterface->getUrl('customer/account/'));
        return $resultRedirect;	
	}
}
