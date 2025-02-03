<?php

namespace Magento\Payriff\Controller\Success;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Session\SuccessValidator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Payriff\Helper\PayriffHelper;
use Magento\Payriff\Helper\PayriffRequestHelper;

/**
 * Class Index
 *
 * @package Magento\Payriff\Controller\Success
 */
class Index extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     *
     * @var SuccessValidator
     */
    private $successValidator;
    protected $payriffHelper;
    protected $payriffRequestHelper;

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
        CheckoutSession $checkoutSession,
        SuccessValidator $successValidator,
        PayriffHelper $payriffHelper, PayriffRequestHelper $payriffRequestHelper
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->successValidator = $successValidator;
        
        $this->payriffHelper = $payriffHelper;
        $this->payriffRequestHelper = $payriffRequestHelper;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = file_get_contents('php://input');
            $result = json_decode($data, true);
            $response = $this->payriffRequestHelper->get_order_information($result['payload']);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($result['payload']['orderStatus']);

            
            $this->payriffRequestHelper->addTransactionToOrder($order, $result['payload']);
        
            if($response->payload->orderStatus == 2) {
                $order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING)->save();
                echo 'success';
            } else {
                $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED)->save();
                echo 'cancel';
            }
        } else {
            $order = $this->checkoutSession->getLastRealOrder();
            $this->checkoutSession->setLastOrderId($order->getId())
            ->setLastSuccessQuoteId($order->getQuoteId())
            ->setLastRealOrderId($order->getIncrementId())
            ->setLastOrderStatus($order->getStatus());
            if (!$this->successValidator->isValid()) {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
        
            $state = $order->getState(); //Get Order State(Complete, Processing, ....)

            if($state == 'processing') {
                return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
            } else {
                return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
            }
        }
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
