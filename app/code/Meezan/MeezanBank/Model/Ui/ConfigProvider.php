<?php
namespace Meezan\MeezanBank\Model\Ui;
use Magento\Checkout\Model\ConfigProviderInterface;
use Meezan\MeezanBank\Gateway\Http\Client\ClientMock;
use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'meezan_bank';
    protected $method;
    protected $scopeConfig;
    public function __construct(ScopeConfig $scopeConfig) 
    {
    $this->scopeConfig = $scopeConfig;
    }
    public function getConfig()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$baseurl=$storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		$url=$baseurl.'meezan/index/meezanform';
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ],
                    'description' => $this->scopeConfig->getValue('payment/meezan_bank/description', $storeScope),
                    'url' => $url
                ]
            ]
        ];
    } // END OF getConfig FUNCTION
} // END OF ConfigProvider CLASS