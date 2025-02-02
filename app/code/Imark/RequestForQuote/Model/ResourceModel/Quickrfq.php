<?php

namespace Imark\RequestForQuote\Model\ResourceModel;

class Quickrfq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
        
        
           
    protected function _construct()
    {
        $this->_init('fme_requestforquote', 'quickrfq_id');
    }
}
