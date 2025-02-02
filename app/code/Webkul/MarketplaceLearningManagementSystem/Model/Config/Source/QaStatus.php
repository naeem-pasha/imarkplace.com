<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceLearningManagementSystem\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class QaStatus extends AbstractSource
{

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Get All Options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            [
                'label' => __('Select Status'),
                'value' => ''
            ],
            [
                'label' => __('Pending'),
                'value' => 'pending'
            ],
            [
                'label' => __('Answered'),
                'value' => 'answered'
            ],
        ];
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
