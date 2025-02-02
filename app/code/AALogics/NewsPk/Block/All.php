<?php
namespace AALogics\NewsPk\Block;
use AALogics\NewsPk\Model\ResourceModel\Newspk\Collection;

class All extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data=[],
        Collection $collection
    )
    {
        $this->collection = $collection;
        parent::__construct($context, $data);
    }

    public function see_all()
    {
        return $this->collection->setOrder('created_at','desc')->getData();
    }
}