<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    public const XML_PATH_CHECKED = "\x66\x61\x73\x74\x63\x68\x65\143\x6b\x6f\x75\164\57\147\145\x6e\x65\x72\141\154" .
    "\x2f\x69\163\x5f\x61\143\x74\151\x76\x65\137\x6d\157\x64\x75\x6c\145";
    public const XML_PATH_LOGO_CHECKOUT = 'fastcheckout/general/logo_checkout';
    public const XML_PATH_REDIRECT_CHECKOUT = 'fastcheckout/general/redirect_to_checkout';
    public const XML_PATH_BACK_TO_CART = 'fastcheckout/general/show_back_to_cart';
    public const XML_PATH_TOP_CONTENT = 'fastcheckout/general/top_content';
    public const XML_PATH_FOOTER_CONTENT = 'fastcheckout/general/footer_content';
    public const XML_PATH_FULL_NAME = 'fastcheckout/additional/show_full_name';
    public const XML_PATH_FULL_NAME_LABEL = 'fastcheckout/additional/full_name_label';
    public const XML_PATH_SORT_ADDRESS = 'fastcheckout/shipping_address/sort_address_fields';
    public const XML_PATH_STREET_ADDRESS = 'fastcheckout/additional/street_address_label';
    public const XML_PATH_PAGE_LAYOUT = 'fastcheckout/additional/page_layout';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerSession
     */
    protected $session;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $session
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CustomerSession $session
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->session = $session;
    }

    /**
     * Check is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->session->isLoggedIn();
    }

    /**
     * Check data
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getChecked(): int
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_CHECKED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get config
     *
     * @param string $path
     * @param bool $isStore
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfig(string $path, bool $isStore = false)
    {
        $storeId = null;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        if ($isStore) {
            $storeId = $this->_storeManager->getStore()->getId();
            $scope = ScopeInterface::SCOPE_STORE;
        }

        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * Get page layout
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getPageLayout()
    {
        return (int)$this->getConfig(self::XML_PATH_PAGE_LAYOUT);
    }

    /**
     * Get Full Name
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getFullName()
    {
        return (int)$this->getConfig(self::XML_PATH_FULL_NAME);
    }

    /**
     * Get Full Name label
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFullNameLabel()
    {
        return (string)$this->getConfig(self::XML_PATH_FULL_NAME_LABEL, true);
    }

    /**
     * Get shipping address label
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStreetLabel()
    {
        return (string)$this->getConfig(self::XML_PATH_STREET_ADDRESS, true);
    }

    /**
     * Get sort address fields
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSortAddress()
    {
        return json_decode($this->getConfig(self::XML_PATH_SORT_ADDRESS));
    }

    /**
     * Get logo url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLogo(): string
    {
        $imgUrl = '';
        $storeId = $this->_storeManager->getStore()->getId();

        $img = (string)$this->scopeConfig->getValue(
            self::XML_PATH_LOGO_CHECKOUT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($img) {
            $imgUrl = $this->getBaseMediaUrl() . 'wysiwyg/logo/' . $img;
        }

        return $imgUrl;
    }

    /**
     * Get base url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get media url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseMediaUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Show/hide back to cart
     *
     * @param mixed $storeId
     * @return int
     */
    public function getBackToCart(mixed $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_BACK_TO_CART,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Redirect to checkout page
     *
     * @param mixed $storeId
     * @return int
     */
    public function isRedirectToCheckout(mixed $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_REDIRECT_CHECKOUT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get top content
     *
     * @param mixed $storeId
     * @return string
     */
    public function getTopContent(mixed $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_TOP_CONTENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get footer content
     *
     * @param mixed $storeId
     * @return string
     */
    public function getFooterContent(mixed $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_FOOTER_CONTENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
