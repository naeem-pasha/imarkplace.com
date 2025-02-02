<?php

namespace Magento\Payriff\Api;

/**
 * Interface WebhookInterface
 *
 * @package Magento\Payriff\Api
 */
interface WebhookInterface
{
    /**
     * GET for Post api
     *
     * @return string
     */
    public function getResponse(): string;
}
