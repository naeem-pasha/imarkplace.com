<?php

namespace MageBig\FastCheckout\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class AddressSort extends Field
{
    /**
     * Form template
     *
     * @var string
     */
    protected $_template = 'MageBig_FastCheckout::system/config/option-sort.phtml';

    /**
     * Get element
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Get base html Id
     *
     * @return string
     */
    public function getHtmlId()
    {
        $htmlId = $this->getData('html_id');
        if (!$htmlId) {
            $htmlId = '_' . uniqid();
            $this->setData('html_id', $htmlId);
        }
        return $htmlId;
    }

    /**
     * Init options
     *
     * @return array
     */
    public function getOptions()
    {
        $output = [];
        $values = $this->getElement()->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        $options = [
            ['value' => 'firstname', 'label' => 'First Name'],
            ['value' => 'lastname', 'label' => 'Last Name'],
            ['value' => 'telephone', 'label' => 'Phone Number'],
            ['value' => 'street', 'label' => 'Street Address'],
            ['value' => 'country_id', 'label' => 'Country'],
            ['value' => 'region', 'label' => 'Region'],
            ['value' => 'city', 'label' => 'City'],
            ['value' => 'postcode', 'label' => 'Zip/Postal'],
            ['value' => 'company', 'label' => 'Company'],
            ['value' => 'fax', 'label' => 'Fax']
        ];

        foreach ($values as $value) {
            foreach ($options as $option) {
                if ($option['value'] == $value) {
                    $output[] = [
                        'value' => $option['value'],
                        'label' => $option['label']
                    ];
                }
            }
        }

        return $output;
    }
}
