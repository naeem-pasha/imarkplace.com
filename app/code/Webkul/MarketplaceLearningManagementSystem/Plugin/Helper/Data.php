<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MarketplaceLearningManagementSystem\Plugin\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function afterGetAllowedProductTypes(
        \Webkul\Marketplace\Helper\Data $subject,
        $result
    ) {
        $productTypes = $this->scopeConfig->getValue(
            'marketplace/product_settings/allow_for_seller',
            ScopeInterface::SCOPE_STORE
        );
        $productTypes = explode(',', $productTypes);
        if (in_array('course', $productTypes)) {
            array_push(
                $result,
                ['value' => 'course', 'label' => __('Course')]
            );
        }
        
        return $result;
    }
}
