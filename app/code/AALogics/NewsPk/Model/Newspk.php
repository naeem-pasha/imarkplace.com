<?php
namespace AALogics\NewsPk\Model;

class Newspk extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'aalogics_newspk_newspk';
	protected $_cacheTag = 'aalogics_newspk_newspk';
	protected $_eventPrefix = 'aalogics_newspk_newspk';
    
    public function _construct()
    {
        $this->_init('AALogics\NewsPk\Model\ResourceModel\Newspk');
    }

    public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}