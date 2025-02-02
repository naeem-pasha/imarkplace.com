<?php

namespace Magento\Payriff\Controller\Fail;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Session\SuccessValidator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

/**
 * Class Index
 *
 * @package Magento\Payriff\Controller\Success
 */
class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     *
     * @param Context                  $context
     * @param OrderRepositoryInterface $orderRepository
     * @param CheckoutSession          $checkoutSession
     * @param SuccessValidator         $successValidator
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        CheckoutSession $checkoutSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED)->save();
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
    }
}
