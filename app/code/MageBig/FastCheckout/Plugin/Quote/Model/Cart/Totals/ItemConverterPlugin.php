<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Quote\Model\Cart\Totals;

use Magento\Catalog\Block\Product\View;
use Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Model\Cart\Totals\Item;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class ItemConverterPlugin
{
    /**
     * @var TotalsItemExtensionInterfaceFactory
     */
    private $totalsItemExtensionFactory;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterface;

    /**
     * @var View
     */
    private $productView;

    /**
     * @param TotalsItemExtensionInterfaceFactory $totalsItemExtensionFactory
     * @param StockRegistryInterface $stockRegistryInterface
     * @param View $productView
     */
    public function __construct(
        TotalsItemExtensionInterfaceFactory $totalsItemExtensionFactory,
        StockRegistryInterface $stockRegistryInterface,
        View $productView
    ) {
        $this->totalsItemExtensionFactory = $totalsItemExtensionFactory;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->productView = $productView;
    }

    /**
     * After model
     *
     * @param ItemConverter $subject
     * @param Item $resultTotalsItem
     * @param QuoteItem $quoteItem
     * @return Item
     */
    public function afterModelToDataObject(
        ItemConverter $subject,
        Item $resultTotalsItem,
        QuoteItem $quoteItem
    ) {
        $extensionAttributes = $resultTotalsItem->getExtensionAttributes()
            ? $resultTotalsItem->getExtensionAttributes()
            : $this->totalsItemExtensionFactory->create();

        $qtyIncrement = $this->stockRegistryInterface
            ->getStockItem($quoteItem->getProductId())
            ->getQtyIncrements();

        if (!$qtyIncrement) {
            $qtyIncrement = $this->productView->getProductDefaultQty($quoteItem->getProduct());
        }

        if ($qtyIncrement) {
            $extensionAttributes->setMbQtyIncrement($qtyIncrement);
            $resultTotalsItem->setExtensionAttributes($extensionAttributes);
        }

        return $resultTotalsItem;
    }
}
