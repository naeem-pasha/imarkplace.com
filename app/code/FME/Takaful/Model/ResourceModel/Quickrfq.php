<?php

namespace FME\Takaful\Model\ResourceModel;

class Quickrfq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
        
        
           
    protected function _construct()
    {
        $this->_init('FME_Takaful', 'takaful_id');
    }
}
