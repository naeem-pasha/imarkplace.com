<?php
namespace Sonic\Booking\Ui\Component;
use Magento\Framework\App\Config\ScopeConfigInterface;
class SonicBookingMassAction extends \Magento\Ui\Component\MassAction
{
    protected $_scopeConfig;

    public function __construct(
            \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
            array $components,
            array $data,
            ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $components, $data);
    }
    
    public function prepare()
    {
        parent::prepare();
        $isModuleEnabled = $this->_scopeConfig->getValue('sonicextension/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ( !$isModuleEnabled ) {
            $config = $this->getConfiguration();
            $allowedActions = [];
            foreach ($config['actions'] as $action) {
                if ('order_bulk' != $action['type']) {
                    $allowedActions[] = $action;
                }
            }
            $config['actions'] = $allowedActions;
            $this->setData('config', (array)$config);
        }
    }
}