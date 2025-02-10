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


namespace Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseContent;

use Webkul\MarketplaceLearningManagementSystem\Model\CourseContent as CourseContentModel;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseContent;

/**
 * Course Content Collection
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
        $this->_init(CourseContentModel::class, CourseContent::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
