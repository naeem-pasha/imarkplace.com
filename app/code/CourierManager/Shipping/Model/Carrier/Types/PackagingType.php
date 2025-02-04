<?php
namespace CourierManager\Shipping\Model\Carrier\Types;

use Magento\Framework\Data\OptionSourceInterface;

class PackagingType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return array(
            array('value' => 'plic', 'label'=> __('Envelope')),
            array('value' => 'colet', 'label'=> __('Package')),
        );
    }
}
