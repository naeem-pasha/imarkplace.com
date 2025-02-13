<?php

namespace MageBig\FastCheckout\Block;

use MageBig\FastCheckout\Helper\Data as DataHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Data implements ArgumentInterface
{
    /**
     * @var DataHelper
     */
    private $helperData;

    /**
     * @param DataHelper $helperData
     */
    public function __construct(
        DataHelper $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Get logo url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLogo(): string
    {
        return $this->helperData->getLogo();
    }
}
