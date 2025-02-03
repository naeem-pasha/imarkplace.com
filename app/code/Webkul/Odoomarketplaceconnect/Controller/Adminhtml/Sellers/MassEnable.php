<?php
/**
 * Webkul Odoomarketplaceconnect Product MassEnable Controller
 *
 * @Product   Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Controller\Adminhtml\Sellers;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Webkul Odoomarketplaceconnect Product MassEnable Controller class
 */
class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;
    /**
     * @param Context $context
     * @param Filter  $filter
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Webkul\Odoomarketplaceconnect\Model\Sellers $model
    ) {
        $this->_filter = $filter;
        $this->_model = $model;
        parent::__construct($context);
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->_model->getCollection();

            $selected = (array)$this->getRequest()->getParam('selected', []);
            $filters = (array)$this->getRequest()->getParam('filters', []);

            foreach ($filters as $field => $value) {
                if ($field == 'placeholder') {
                    continue;
                }
                $collection->addFieldToFilter($field, ['like'=>"%$value%"]);
            }

            if ($selected) {
                $collection = $collection->addFieldToFilter('entity_id', ['in', $selected]);
            }

            if ($collection->getSize()) {
                foreach ($collection as $modelObj) {
                    $modelObj->setNeedSync('yes');
                }
                $collection->save();
                $this->messageManager->addSuccess(__('Marketplace Seller(s) need sync enabled successfully.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
