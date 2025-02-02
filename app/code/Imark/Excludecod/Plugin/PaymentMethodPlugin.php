<?php

namespace Imark\Excludecod\Plugin;

use Magento\Quote\Model\Quote;

class PaymentMethodPlugin
{
    /**
     * Restrict COD payment method based on product attribute.
     *
     * @param \Magento\Payment\Model\MethodInterface $subject
     * @param bool $result
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function afterIsAvailable(
        \Magento\Payment\Model\MethodInterface $subject,
        $result,
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        if (!$result || !$quote || $subject->getCode() !== 'cashondelivery') {
            return $result;
        }

        foreach ($quote->getAllItems() as $item) {
            $product = $item->getProduct();
            // Ensure the product attribute is loaded
            if (!$product->hasData('exclude_cod')) {
                $product = $product->load($product->getId());
            }

            if ($product->getExcludeCod()) {
                return false; // Restrict COD if any product has the attribute set to true
            }
        }

        return $result;
    }
}
