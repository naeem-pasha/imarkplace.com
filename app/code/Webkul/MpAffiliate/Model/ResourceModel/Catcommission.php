<?php
/**
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Model\ResourceModel;

/**
 * Affiliate Catcommission mysql resource.
 */
class Catcommission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('mp_affiliate_catcommission', 'entity_id');
    }
}
