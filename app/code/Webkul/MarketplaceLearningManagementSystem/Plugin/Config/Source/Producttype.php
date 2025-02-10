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

namespace Webkul\MarketplaceLearningManagementSystem\Plugin\Config\Source;

/**
 * Used in creating options for getting product type value.
 */
class Producttype
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function afterToOptionArray(
        \Webkul\Marketplace\Model\Config\Source\Producttype $subject,
        $result
    ) {
        array_push(
            $result,
            ['value' => 'course', 'label' => __('Course')]
        );
        return $result;
    }
}
