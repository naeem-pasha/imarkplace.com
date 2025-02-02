<?php
/**
 * Webkul Odoomarketplaceconnect Order Listing Component
 *
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomarketplaceconnect\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Webkul Odoomarketplaceconnect Order Ui Component Class
 */
class Order extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */

    /** Url Path */
    const ORDER_PATH_VIEW = 'sales/order/view';
    const PRODUCT_PATH_VIEW = 'catalog/product/edit';

    public function __construct(
        array $data = [],
        array $components = [],
        ContextInterface $context,
        UrlInterface $urlBuilder,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Catalog\Model\Product $catalogProduct,
        UiComponentFactory $uiComponentFactory,
        \Webkul\Marketplace\Model\Saleslist $salesList
    ) {
        $this->_salesList = $salesList;
        $this->_catalogProduct = $catalogProduct;
        $this->urlBuilder = $urlBuilder;
        $this->_orderModel = $orderModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['magento_id'])) {
                    $orderId = $item['magento_id'];
                    $name = $this->_orderModel->load($orderId)->getIncrementId();
                    $productId = $this->_salesList->load($item['mp_sale_id'])->getMageproductId();
                    $proName = $this->_catalogProduct->load($productId)->getName();
            
                    $item['magento_id'] = $name;
                    // $item['magento_id'] = '<a href="'.$this->urlBuilder->getUrl(self::ORDER_PATH_VIEW, ['order_id' => $item['magento_id']]).'" target="_blank" rel="noopener">'.$name.'</a>';
                    $item['order_product'] = '<a href="'.$this->urlBuilder->getUrl(self::PRODUCT_PATH_VIEW, ['id' => $productId]).'" target="_blank" rel="noopener">'.$proName.'</a>';
                }
            }
        }
        return $dataSource;
    }
}
