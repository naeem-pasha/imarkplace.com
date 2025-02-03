<?php
/**
 * Webkul Odoomarketplaceconnect Connection Helper
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomarketplaceconnect\Helper;

use \Webkul\Odoomagentoconnect\Helper\Data;

class Connection extends \Webkul\Odoomagentoconnect\Helper\Connection
{
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Session $session,
        Data $odoomagentoconnectData
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_session = $session;
        $this->_odoomagentoconnectData = $odoomagentoconnectData;
        parent::__construct($scopeConfig, $objectManager, $storeManager, $session, $odoomagentoconnectData);
    }

    // public function getSocketConnect()
    // {
    //     $parent = parent::getSocketConnect();
    //     return $parent;
    // }

    public function updateMpSetting()
    {
        $commision = $this->_scopeConfig->getValue('marketplace/general_settings/percent');
        $partnerApproval = $this->_scopeConfig->getValue('odoomagentoconnect/odoomarketplaceconnect/auto_seller');
        $productApproval = $this->_scopeConfig->getValue('odoomagentoconnect/odoomarketplaceconnect/auto_product');
        $data = [
            'commission'=>(double)$commision,
            'auto_product'=>$productApproval,
            'auto_seller'=>$partnerApproval,
        ];
        $resp = $this->callOdooMethod("connector.instance", "updateMpSetting", [$data]);
    }
}
