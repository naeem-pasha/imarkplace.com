<?php
/**
 * Webkul Odoomarketplaceconnect Seller Listing Component
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Webkul Odoomagentoconnect Customer Ui Component Class
 */
class Seller extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\Customer $customerModel,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
    
        $this->_urlBuilder  = $urlBuilder;
        $this->_customerModel=$customerModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['mp_seller_id'])) {
                    $customerObj = $this->_customerModel->load($item['mp_seller_id']);
                    $item['mp_seller_id'] = '<a href="'.$this->_urlBuilder->getUrl('customer/index/edit', ['id' => $item['mp_seller_id']]).'">'.$customerObj->getName().'</a>';
                }
            }
        }
        return $dataSource;
    }
}
