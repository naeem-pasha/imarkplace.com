<?php
namespace AALogics\NewsPk\Model\ResourceModel\Newspk;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'newspk_id';
    protected $_eventPrefix = 'aalogics_newspk_collection';
	protected $_eventObject = 'newspk_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
    public function _construct()
    {
        $this->_init('AALogics\NewsPk\Model\Newspk', 'AALogics\NewsPk\Model\ResourceModel\Newspk');
    }
}