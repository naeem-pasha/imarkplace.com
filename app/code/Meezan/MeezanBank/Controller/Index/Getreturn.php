<?php
namespace Meezan\MeezanBank\Controller\Index;
class Getreturn extends \Magento\Framework\App\Action\Action 
{
	protected $_pageFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	} // END OF __construct FUNCTION
	public function execute()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$baseurl=$storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		$returnurl=$baseurl.'meezan/index/response';
		echo "<h1>Checking your transaction Status...</h1>";
		echo ("<script>window.top.location='$returnurl'</script>");
	} // END OF execute FUNCTION
} // END OF Getreturn CLASS