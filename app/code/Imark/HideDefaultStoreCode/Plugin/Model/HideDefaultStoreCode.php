<?php
namespace Imark\HideDefaultStoreCode\Plugin\Model;

class HideDefaultStoreCode
{
     /**
     *
     * @var \Vendor\HideDefaultStoreCode\Helper\Data 
     */
    protected $helper;


    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */

     protected $storeManager;

    const CACHE_TAG = 'imark_hidedefaultstorecode_hide_default_store_code';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'hide_default_store_code';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Imark\HideDefaultStoreCode\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

     /**
     * 
     * @param \Magento\Store\Model\Store $subject
     * @param string $url
     * @return string
     */
    public function afterGetBaseUrl(\Magento\Store\Model\Store $subject, $url)
    {
        if ($this->helper->isHideDefaultStoreCode()) {
            $url = str_replace('/'.$this->storeManager->getDefaultStoreView()->getCode().'/','/', $url);
        }
        return $url;
    }
}
