<?php

namespace Paypro\PayproCheckout\Block\Info;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PayproCheckout extends \Magento\Payment\Block\Info
{
    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->paymentConfig = $paymentConfig;
        $this->scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_request = $request;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare information specific to current payment method
     *
     * @param null | array $transport
     * @return \Magento\Framework\DataObject
     */

    protected function _prepareSpecificInformation($transport = null) {
        if ($this->_paymentSpecificInformation !== null) {
            return $this->_paymentSpecificInformation;
        }
        return $transport;
    }

    /**
    Returns admin config parameters
     **/
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue($config_path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
    Retreives current order information to be posted to PayPro
     **/
    public function getOrder()
    {
        $order  = $this->_checkoutSession->getLastRealOrder();
        return $order;
    }

    /**
     * Get Store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get Store base url
     *
     * @return string
     */
    public function getStoreBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
    Return 2nd redirection url
     **/
    public function getConfirmUrl() {
        return $this->getUrl('payprocheckout/index/confirm', array('_secure' => true));
    }

    /**
    Returns postback url
     **/
    public function getStatusUrl() {
        return $this->getUrl('payprocheckout/index/status', array('_secure' => true));
    }
    /**
    Returns cancelorder url
     **/
    public function getCancelUrl() {
        return $this->getUrl('payprocheckout/index/cancel', array('_secure' => true));
    }
}
