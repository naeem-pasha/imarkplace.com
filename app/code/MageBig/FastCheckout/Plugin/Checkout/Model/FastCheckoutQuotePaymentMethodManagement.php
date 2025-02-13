<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Checkout\Model;

use MageBig\FastCheckout\Helper\Data;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\Quote;

class FastCheckoutQuotePaymentMethodManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param Data $helper
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Data $helper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helper = $helper;
    }

    /**
     * Before set payment method
     *
     * @param PaymentMethodManagementInterface $subject
     * @param int $cartId
     * @param PaymentInterface $method
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSet(PaymentMethodManagementInterface $subject, int $cartId, PaymentInterface $method)
    {
        if ($this->helper->getChecked()) {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->get($cartId);

            if (!$quote->isVirtual()) {
                $address = $quote->getShippingAddress();
                if ($address->getCountryId() === null) {
                    $address->setCountryId('');
                }
            }
        }
    }
}
