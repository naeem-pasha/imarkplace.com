<?php
namespace CourierManager\Shipping\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Message\Error;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\Exception;
use Magento\Store\Model\StoreManagerInterface;

class ApiAuthValidate extends Value
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * @return ApiAuthValidate|void
     * @throws Exception
     */
    public function validateBeforeSave()
    {
        $apiClient = new CourierManagerApi($this->getData('fieldset_data/api_url'), $this->getValue());
        $apiClient->getServicesList();
        if($apiClient->getError()) {
            $exception = new Exception(new Phrase($apiClient->getError()));
            $exception->addMessage(new Error($apiClient->getError()));
            throw $exception;
        }

        parent::validateBeforeSave();
    }


}
