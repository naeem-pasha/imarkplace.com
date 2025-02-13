<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Checkout\Model;

use MageBig\FastCheckout\Helper\Data;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;

class FastCheckoutPaymentMethodManagement
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param Data $helper
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        Data $helper
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->helper = $helper;
    }

    /**
     * Before save payment information
     *
     * @param GuestPaymentInformationManagementInterface $subject
     * @param string $cartId
     * @param string $email
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSavePaymentInformation(
        GuestPaymentInformationManagementInterface $subject,
        string $cartId,
        string $email,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        if ($this->helper->getChecked()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
            if (!$quote->isVirtual()) {
                $address = $quote->getShippingAddress();

                if ($address->getCountryId() === null) {
                    $address->setCountryId('');
                    $quote->save();
                }
            }
        }
    }
}
