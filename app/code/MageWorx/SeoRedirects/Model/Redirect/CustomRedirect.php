<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect;

use MageWorx\SeoRedirects\Model\Redirect;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use MageWorx\SeoRedirects\Helper\CustomRedirect\Data as HelperData;
use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;

class CustomRedirect extends \MageWorx\SeoRedirects\Model\Redirect implements CustomRedirectInterface
{
    /**
     * Must be minimal
     */
    const REDIRECT_TYPE_CUSTOM      = 1;
    const REDIRECT_TYPE_PRODUCT     = 2;
    const REDIRECT_TYPE_CATEGORY    = 3;
    const REDIRECT_TYPE_PAGE        = 4;
    const REDIRECT_TYPE_LANDINGPAGE = 5;

    /**
     * @var Url
     */
    protected $urlModel;

    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoredirects_customredirect';

    /**
     * cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mageworx_seoredirects_customredirect';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoredirects_customredirect';

    /**
     * filter model
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * CustomRedirect constructor.
     *
     * @param FilterManager $filter
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param HelperData $helperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        FilterManager $filter,
        Context $context,
        Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        HelperData $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filter       = $filter;
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(CustomRedirectInterface::REDIRECT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectCode()
    {
        return $this->getData(CustomRedirectInterface::REDIRECT_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestEntityType()
    {
        return $this->getData(CustomRedirectInterface::REQUEST_ENTITY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntityType()
    {
        return $this->getData(CustomRedirectInterface::TARGET_ENTITY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestEntityIdentifier()
    {
        return $this->getData(CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntityIdentifier()
    {
        return $this->getData(CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(CustomRedirectInterface::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestPath()
    {
        return $this->getData(CustomRedirectInterface::REQUEST_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(CustomRedirectInterface::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateCreated()
    {
        return $this->getData(CustomRedirectInterface::DATE_CREATED);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateModified()
    {
        return $this->getData(CustomRedirectInterface::DATE_MODIFIED);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAutogenerated()
    {
        return $this->getData(CustomRedirectInterface::IS_AUTOGENERATED);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsImported()
    {
        return $this->getData(CustomRedirectInterface::IS_IMPORTED);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(CustomRedirectInterface::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartAt()
    {
        return $this->getData(CustomRedirectInterface::START_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinishAt()
    {
        return $this->getData(CustomRedirectInterface::FINISH_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(CustomRedirectInterface::REDIRECT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setRedirectCode($code)
    {
        return $this->setData(CustomRedirectInterface::REDIRECT_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestEntityType($type)
    {
        return $this->setData(CustomRedirectInterface::REQUEST_ENTITY_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetEntityType($type)
    {
        return $this->setData(CustomRedirectInterface::TARGET_ENTITY_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestEntityIdentifier($identifier)
    {
        return $this->setData(CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetEntityIdentifier($identifier)
    {
        return $this->setData(CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(CustomRedirectInterface::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function setDateCreated($date)
    {
        return $this->setData(CustomRedirectInterface::DATE_CREATED, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function setDateModified($date)
    {
        return $this->setData(CustomRedirectInterface::DATE_MODIFIED, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAutogenerated($isAutogenerated)
    {
        return $this->setData(CustomRedirectInterface::IS_AUTOGENERATED, $isAutogenerated);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsImported($isImported)
    {
        return $this->setData(CustomRedirectInterface::IS_IMPORTED, $isImported);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($statusCode)
    {
        return $this->setData(CustomRedirectInterface::STATUS, $statusCode);
    }

    /**
     * {@inheritDoc}
     */
    public function setStartAt($time)
    {
        return $this->setData(CustomRedirectInterface::START_AT, $time);
    }

    /**
     * {@inheritDoc}
     */
    public function setFinishAt($time)
    {
        return $this->setData(CustomRedirectInterface::FINISH_AT, $time);
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return ['status' => static::STATUS_ENABLED];
    }
}
