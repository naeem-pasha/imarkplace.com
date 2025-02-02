<?php

namespace Magento\Payriff\Block;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payriff\Helper\PayriffHelper;

/**
 * Class Installment
 *
 * @package Magento\Payriff\Block
 */
class Installment extends Template
{
    protected $_product = null;
    protected $_registry;
    protected $_productFactory;
    protected $payriffHelper;
    protected $productLinks;

    /**
     * Installment constructor.
     *
     * @param Context        $context
     * @param Registry       $registry
     * @param ProductFactory $productFactory
     * @param PayriffHelper    $payriffHelper
     * @param array          $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $productFactory,
        PayriffHelper $payriffHelper,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_productFactory = $productFactory;
        $this->payriffHelper = $payriffHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * @return array|int[]
     */
    public function installmentSettings(): array
    {
        return $this->payriffHelper->calculateInstallment(
            $this->payriffHelper->getCategoryInstallment(),
            $this->getCurrentProduct()->getCategoryIds(),
            true
        );
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->payriffHelper->getMerchantId();
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }


    /**
     * @return array
     */
    public function getProductIds(): array
    {
        $product_ids    = [];
        $productId      = $this->getCurrentProduct()->getId();
        $_objectManager = ObjectManager::getInstance();
        $_product       = $_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $_childProducts = $_product->getTypeInstance()->getUsedProducts($_product);
        foreach ($_childProducts as $simpleProduct) {
            $product_ids[$simpleProduct->getId()]
                = number_format($simpleProduct->getPrice(), 2, '.', '.');
        }
        return $product_ids;
    }
}
