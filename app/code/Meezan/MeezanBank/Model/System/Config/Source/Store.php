<?php
namespace Meezan\MeezanBank\Model\System\Config\Source;
class Store implements \Magento\Framework\Data\OptionSourceInterface
{
  public function toOptionArray()
  {
    return [
      ['value' => '1', 'label' => __('Iframe')],
      ['value' => '2', 'label' => __('Redirection')]
    ];
  } // END OF toOptionArray FUNCTION
} // END OF Store CLASS