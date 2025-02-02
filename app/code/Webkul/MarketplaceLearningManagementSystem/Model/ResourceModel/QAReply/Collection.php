<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Webkul_MarketplaceLearningManagementSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QAReply;

use Webkul\MarketplaceLearningManagementSystem\Model\QAReply as QAReplyModel;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QAReply;

/**
 * QA Replu Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(QAReplyModel::class, QAReply::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
