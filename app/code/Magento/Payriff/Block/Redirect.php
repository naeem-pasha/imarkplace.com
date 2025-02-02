<?php

namespace Magento\Payriff\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Payriff\Helper\PayriffHelper;
use Magento\Payriff\Helper\PayriffRequestHelper;

/**
 * Class Redirect
 *
 * @package Magento\Payriff\Block
 */
class Redirect extends \Magento\Framework\View\Element\Template
{

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var PayriffHelper
     */
    protected $payriffHelper;

    /**
     * @var PayriffRequestHelper
     */
    protected $payriffRequestHelper;

    /**
     * Redirect constructor.
     *
     * @param Context            $context
     * @param ManagerInterface   $messageManager
     * @param PayriffHelper        $payriffHelper
     * @param PayriffRequestHelper $payriffRequestHelper
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        PayriffHelper $payriffHelper,
        PayriffRequestHelper $payriffRequestHelper
    ) {
        $this->config = $context->getScopeConfig();
        $this->_messageManager = $messageManager;
        $this->payriffHelper = $payriffHelper;
        $this->payriffRequestHelper = $payriffRequestHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _prepareLayout(): bool
    {
        try {
            if (!$this->payriffHelper->getOrder()->getRealOrderId()) {
                header('Location: '. $this->_storeManager->getStore()->getBaseUrl());
                return false;
            }

            $url = $this->payriffRequestHelper->getPayriffUrl();

            $payriff_data = [
                'status' => 'success',
                'url' => $url->payload->paymentUrl,
                'message' => ''
            ];

            $this->setAction(json_encode($payriff_data));

            header('Location: '. $url->payload->paymentUrl);
            return false;

        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage('An error occurred. Please try again.');
        }
        return true;
    }
}
