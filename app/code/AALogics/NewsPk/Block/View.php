<?php
namespace AALogics\NewsPk\Block;
use AALogics\NewsPk\Model\Newspk;

class View extends \Magento\Framework\View\Element\Template
{

    protected $newspk; 

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data=[],
		Newspk $newspk
    )
    {
        $this->newspk = $newspk;
        parent::__construct($context, $data);
    }

    public function view_news()
    {
        //get ID from the request
        $id = $this->getRequest()->getParam('id');
        // `getCollection` method to return a collection
        $collection = $this->newspk->getCollection();
        //add the paramater as a filter
        $collection->addFieldToFilter('newspk_id', $id);  
        //get the first item of the collection (load will be called automatically)
        $news = $collection->getFirstItem();
        return $news;
    }
}