<?php

namespace Magento\Payriff\Helper;

/**
 * Class InstallmentType
 *
 * @package Magento\Payriff\Helper
 */
class InstallmentType
{

    /**
     * @return string[]
     */
    public function getInstallmentTypes(): array
    {
        return [
            0 => __('Single Payment'),
        ];
    }
}
