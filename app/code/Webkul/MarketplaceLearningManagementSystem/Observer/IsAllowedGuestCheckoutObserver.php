<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @auther    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceLearningManagementSystem\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class IsAllowedGuestCheckoutObserver implements ObserverInterface
{

    /**
     * Check is allowed guest checkout if quote contain course product(s)
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $result = $observer->getEvent()->getResult();

        /* @var $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getEvent()->getQuote();

        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct())
                && $product->getTypeId() ==
                \Webkul\MarketplaceLearningManagementSystem\Model\Product\CourseProduct::TYPE_ID
            ) {
                $result->setIsAllowed(false);
                break;
            }
        }

        return $this;
    }
}
