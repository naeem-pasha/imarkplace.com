<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageBig\FastCheckout\Model\Config\Source\Payment;

use Magento\Payment\Helper\Data;

class Allmethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get payment data
     *
     * @var Data
     */
    protected $_paymentData;

    /**
     * @param Data $paymentData
     */
    public function __construct(Data $paymentData)
    {
        $this->_paymentData = $paymentData;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $result = [
            ['value' => '', 'label' => __('No')]
        ];

        $payments = $this->_paymentData->getPaymentMethodList(true, true, true);

        return array_merge($result, $payments);
    }
}
