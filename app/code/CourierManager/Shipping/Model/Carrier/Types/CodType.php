<?php
namespace CourierManager\Shipping\Model\Carrier\Types;

use Magento\Framework\Data\OptionSourceInterface;

class CodType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return array(
            array('value' => 'cont', 'label' => __('Bank account')),
            array('value' => 'cash', 'label' => __('Cash')),
        );
    }
}
