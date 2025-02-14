<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\ResourceModel\Catalog\Product;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class Simple extends \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product
{
    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Eav\Model\ConfigFactory
     */
    protected $eavConfigFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $helperStoreUrl;

    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\FlexibleCanonical
     */
    protected $flexibleCanonicalResource;

    /**
     * CrossDomain constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Eav\Model\ConfigFactory $eavConfigFactory
     * @param \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver
     * @param FlexibleCanonical $flexibleCanonical
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \MageWorx\SeoBase\Helper\Data $helperData,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Eav\Model\ConfigFactory $eavConfigFactory,
        \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\FlexibleCanonical $flexibleCanonical,
        $resourcePrefix = null
    ) {
        $this->storeManager              = $storeManager;
        $this->objectFactory             = $objectFactory;
        $this->productVisibility         = $productVisibility;
        $this->productStatus             = $productStatus;
        $this->eavConfigFactory          = $eavConfigFactory;
        $this->helperData                = $helperData;
        $this->helperStoreUrl            = $helperStoreUrl;
        $this->flexibleCanonicalResource = $flexibleCanonical;

        parent::__construct($context, $productResource, $linkFieldResolver, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * @param string $urlType
     * @param array $storeId
     * @param int $productId
     * @return \Magento\Framework\DB\Select|null
     */
    public function getCanonicalData($storeId, $productId)
    {
        $select = $this->getConnection()
                       ->select()
                       ->from(
                           ['url_b' => $this->getTable('url_rewrite')],
                           [
                               'url'       => 'url_b.request_path',
                               'store_id'  => 'url_b.store_id',
                               'entity_id' => 'url_b.entity_id',
                               'metadata'  => 'url_b.metadata'
                           ]
                       )
                       ->where('url_b.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE)
                       ->where('url_b.entity_id = ?', $productId)
                       ->where('url_b.store_id = ?', $storeId)
                       ->where('url_b.is_autogenerated = 1');

        $this->flexibleCanonicalResource->addFlexibleConditions($select, $storeId, 'url_b');

        $query = $this->getConnection()->query($select);
        $row   = $query->fetch();

        if (!is_array($row)) {
            return false;
        }

        return $this->prepareProduct($row, $storeId);
    }


    /**
     * Prepare and convert simple canonical data to object
     *
     * @param array $productRow
     * @param int $storeId
     * @return \Magento\Framework\Object
     */
    protected function prepareProduct(array $productRow, $storeId)
    {
        if (empty($productRow['url'])) {
            return false;
        }
        $product = $this->objectFactory->create();
        $product->addData($productRow);
        $product['id']       = $productRow[$this->getIdFieldName()];
        $product['store_id'] = $storeId;
        $product->setUrl($this->helperStoreUrl->getUrl($productRow['url'], $storeId, true));

        return $product;
    }
}