<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Block;

use MageBig\FastCheckout\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class FastCheckout extends Template
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FilterProvider
     */
    protected $_cmsFilter;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param StoreManagerInterface $storeManager
     * @param FilterProvider $cmsFilter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        StoreManagerInterface $storeManager,
        FilterProvider $cmsFilter,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->_storeManager = $storeManager;
        $this->_cmsFilter = $cmsFilter;
        parent::__construct($context, $data);
    }

    /**
     * Get back to cart
     *
     * @return int
     */
    public function getBackToCart(): int
    {
        return $this->helperData->getBackToCart();
    }

    /**
     * Get top content
     *
     * @return string
     * @throws \Exception
     */
    public function getTopContent(): string
    {
        return $this->_cmsFilter->getBlockFilter()->filter($this->helperData->getTopContent());
    }

    /**
     * Get footer content
     *
     * @return string
     * @throws \Exception
     */
    public function getFooterContent(): string
    {
        return $this->_cmsFilter->getBlockFilter()->filter($this->helperData->getFooterContent());
    }

    /**
     * Check is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->helperData->isLoggedIn();
    }
}
