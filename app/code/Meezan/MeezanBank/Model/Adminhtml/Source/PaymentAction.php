<?php
namespace Meezan\MeezanBank\Model\Adminhtml\Source;
use Magento\Payment\Model\Method\AbstractMethod;
class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize')
            ]
        ];
    } // END OF toOptionArray FUNCTION
} // END OF PaymentAction CLASS