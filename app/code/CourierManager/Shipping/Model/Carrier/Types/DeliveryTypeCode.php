<?php

namespace CourierManager\Shipping\Model\Carrier\Types;

use CourierManager\Shipping\Model\Carrier\CourierManagerApi;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class DeliveryTypeCode implements OptionSourceInterface
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * DeliveryTypeCode constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function toOptionArray(): array
    {
        $deliveryTypeCodes = [];
        if ($this->getConfigData('api_url') && $this->getConfigData('api_key')) {
            $apiClient = new CourierManagerApi($this->getConfigData('api_url'), $this->getConfigData('api_key'));

            $deliveryServices = $apiClient->getServicesList();
            if(!$apiClient->getError()) {
                foreach ($deliveryServices as $service) {
                    $deliveryTypeCodes[] = ['value' => $service->value, 'label' => $service->name];
                }
            }
        }

        return $deliveryTypeCodes;
    }

    /**
     * @param $field
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfigData($field)
    {
        return $this->scopeConfig->getValue(
            'carriers/couriermanager/' . $field,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
