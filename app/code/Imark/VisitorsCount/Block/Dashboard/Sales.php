<?php
namespace Imark\VisitorsCount\Block\Dashboard;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Module\Manager;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Model\Visitor;

class Sales extends \Magento\Backend\Block\Dashboard\Sales
{
    protected $visitors;
    protected $vendorCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Manager $moduleManager,
        Visitor $visitors,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->visitors = $visitors;
        parent::__construct($context, $collectionFactory,$moduleManager, $data);
    }

    protected function _prepareLayout() 
    {
        parent::_prepareLayout();
        $collection = $this->visitors->getCollection();
        
        $this->addCustomerVisitor(__('Total Visitors :'), count($collection));
    }
    public function addCustomerVisitor($label, $value, $isQuantity = false)
    {
        $decimals = '';
        $this->_totals[] = ['label' => $label, 'value' => "<span style='color: #eb5202;font-size: 2.4rem;'>".$value."</span>",'decimals' => $decimals];
        return $this;
    }
}
