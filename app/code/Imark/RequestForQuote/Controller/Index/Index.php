<?php
namespace Imark\RequestForQuote\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Imark\RequestForQuote\Controller\Index
{
                
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
