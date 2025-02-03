<?php

namespace Magento\Payriff\Plugin\Magento\Framework\Webapi\Rest;

/**
 * Class Request
 *
 * @package Magento\Payriff\Plugin\Magento\Framework\Webapi\Rest
 */
class Request
{

    /**
     * @param  \Magento\Framework\Webapi\Rest\Request $subject
     * @param  array                                  $result
     * @return array|string[]
     */
    public function afterGetAcceptTypes(\Magento\Framework\Webapi\Rest\Request $subject, array $result): array
    {
        if ($subject->getRequestUri() === '/rest/V1/payriff/callback/' || $subject->getRequestUri() === '/index.php/rest/V1/payriff/callback/') {
            $result = ['text/html'];
        }
        return $result;
    }
}
