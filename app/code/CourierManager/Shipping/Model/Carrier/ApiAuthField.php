<?php
namespace CourierManager\Shipping\Model\Carrier;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ApiAuthField extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        if (!$this->getConfigData('api_url') || !$this->getConfigData('api_key')) {
            $element->setDisabled('disabled');
        }

        return $element->getElementHtml();
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getConfigData($field)
    {
        $ObjectManager = ObjectManager::getInstance(); // get ObjectManager instance
        $scopeConfig = $ObjectManager->create(ScopeConfigInterface::class);
        $storeManager = $ObjectManager->create(StoreManagerInterface::class);

        return $scopeConfig->getValue(
            'carriers/couriermanager/' . $field,
            ScopeInterface::SCOPE_STORE,
            $storeManager->getStore()->getId()
        );
    }
}
