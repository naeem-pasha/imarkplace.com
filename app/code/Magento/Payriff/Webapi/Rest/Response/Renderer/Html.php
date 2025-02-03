<?php

namespace Magento\Payriff\Webapi\Rest\Response\Renderer;

use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Response\RendererInterface;

/**
 * Class Html
 *
 * @package Magento\Payriff\Webapi\Rest\Response\Renderer
 */
class Html implements RendererInterface
{
    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return 'text/html';
    }

    /**
     * @param  array|bool|float|int|object|string|null $data
     * @return string
     * @throws Exception
     */
    public function render($data): string
    {
        return $data;  
        if (is_string($data)) {
            return $data;
        } else {
            throw new Exception(
                __('Data is not html.')
            );
        }
    }
}
