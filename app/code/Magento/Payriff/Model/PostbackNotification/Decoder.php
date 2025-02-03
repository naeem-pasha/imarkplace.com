<?php

namespace Magento\Payriff\Model\PostbackNotification;

use Magento\Framework;

/**
 * Class Decoder
 *
 * @package Magento\Payriff\Model\PostbackNotification
 */
class Decoder implements DecoderInterface
{
    /**
     * @param  string $data
     * @return mixed
     */
    public function decode($data)
    {
        parse_str($data, $result);
        return $result;
    }
}
