<?php
/**
 * Affiliate Sale ResourceModel.
 * @category  Webkul
 * @package   Webkul_Affiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Model\ResourceModel;

/**
 * Affiliate sale mysql resource.
 */
class Sale extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('mp_affiliate_sale', 'entity_id');
    }
}
