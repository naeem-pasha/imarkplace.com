<?php
/**
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Observer;

use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ObserverInterface;

class MappingDeleteAfterObserver implements ObserverInterface
{
    public function __construct(
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Magento\Backend\Model\Session $session
    ) {
        $this->_connection = $connection;
        $this->_session = $session;

    }

    /**
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $status = false;
        $resetData = $this->_session->getResetData();
        $models = ['Category', 'Product', 'Sellers', 'Transaction', 'Order'];

        $resetModels = ' Sellers, Transaction';
        foreach ($models as $model) {
            $collections = $this->_connection->getModel('\Webkul\Odoomarketplaceconnect\Model\\'.$model)->getCollection();
            if ($collections) {
                $collections->walk('delete');
                $status = true;
            }
            if ($status) {
                $status = false;
            }
        }
        $resetData = $this->_session->setResetData($resetModels);
        return true;
    }
}
