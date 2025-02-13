<?php

namespace MageBig\FastCheckout\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\File;

class Image extends File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
