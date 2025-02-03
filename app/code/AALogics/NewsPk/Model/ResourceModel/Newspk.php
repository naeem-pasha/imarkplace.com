<?php
namespace AALogics\NewsPk\Model\ResourceModel;


class Newspk extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}

    protected function _construct()
    {
        $this->_init("newspk", "newspk_id");
    }
    
}