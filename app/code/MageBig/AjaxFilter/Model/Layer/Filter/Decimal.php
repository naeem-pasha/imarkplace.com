<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageBig\AjaxFilter\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use \Magento\Framework\App\ObjectManager;
/**
 * Layer decimal filter
 */
class Decimal extends \Magento\CatalogSearch\Model\Layer\Filter\Decimal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Layer\Filter\Decimal
     */
    private $resource;

    protected $objectManager;

    protected $helper;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $filterDecimalFactory,
            $priceCurrency,
            $data
        );
        $this->resource = $filterDecimalFactory->create();
        $this->priceCurrency = $priceCurrency;
        $this->objectManager = ObjectManager::getInstance();
        $this->helper = $this->objectManager->get('MageBig\AjaxFilter\Helper\Data');
    }

    public function applyToCollection($productCollection, $request, $requestVar)
    {
        $filter = $request->getParam($requestVar);
        if (!$filter || is_array($filter)) {
            return $productCollection;
        }

        list($from, $to) = explode('-', $filter);

        $productCollection->addFieldToFilter(
            $this->getAttributeModel()->getAttributeCode(),
            ['from' => $from, 'to' => $to]
        );
        return $productCollection;
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }

        list($from, $to) = explode('-', $filter);

        $productCollection = $this->getLayer()->getProductCollection();
        $attribute = $this->getAttributeModel();
        $attributeCode = $attribute->getAttributeCode();

        $this->setBeforeApplyFacetedData($this->helper->getBeforeApplyFacetedData($productCollection, $attributeCode));

        $productCollection->addFieldToFilter(
            $this->getAttributeModel()->getAttributeCode(),
            ['from' => $from, 'to' => $to]
        );

        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
        );

        return $this;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();

        if (!$productCollection = $this->getBeforeApplyFacetedData()) {
            $productCollection = $this->getLayer()->getProductCollection();
        }

        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());
        $data = [];

        foreach ($facets as $key => $aggregation) {
            $count = $aggregation['count'];
            list($from, $to) = explode('_', $key);
            if ($from == '*') {
                $from = '';
            }
            if ($to == '*') {
                $to = '';
            }
            $label = $this->renderRangeLabel(
                empty($from) ? 0 : $from,
                empty($to) ? 0 : $to
            );
            $value = $from . '-' . $to;

            $data[] = [
                'label' => $label,
                'value' => $value,
                'count' => $count,
                'from' => $from,
                'to' => $to
            ];
        }

        return $data;
    }
}
