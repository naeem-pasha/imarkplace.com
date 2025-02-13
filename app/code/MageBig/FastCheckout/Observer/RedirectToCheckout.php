<?php

namespace MageBig\FastCheckout\Observer;

use MageBig\FastCheckout\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class RedirectToCheckout implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Redirect to checkout page after add product to cart
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->getChecked()
            && $this->helper->isRedirectToCheckout()
        ) {
            $request = $observer->getRequest();
            if (!$request->getParam('return_url')) {
                $request->setParam(
                    'return_url',
                    $this->helper->getBaseUrl() . 'checkout'
                );
            }
        }
    }
}
