<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Checkout\Helper;

use Magento\Checkout\Helper\Data;

class DataPlugin
{
    /**
     * Force display billing address after payment method
     *
     * @param Data $subject
     * @param \Closure $proceed
     * @return false
     */
    public function aroundIsDisplayBillingOnPaymentMethodAvailable(Data $subject, \Closure $proceed)
    {
        return false;
    }
}
