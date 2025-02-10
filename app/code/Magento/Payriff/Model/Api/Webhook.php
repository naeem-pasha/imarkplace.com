<?php

namespace Magento\Payriff\Model\Api;

use Exception;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\Builder as TransactionBuilder;
use Magento\Sales\Model\OrderFactory;
use Magento\Payriff\Helper\PayriffHelper;

/**
 * Class Webhook
 *
 * @package Magento\Payriff\Model\Api
 */
class Webhook
{
    protected $orderFactory;
    protected $config;
    protected $transactionBuilder;
    protected $transactionRepository;
    protected $request;
    protected $payriffHelper;

    /**
     * Webhook constructor.
     *
     * @param OrderFactory                   $orderFactory
     * @param Context                        $context
     * @param TransactionBuilder             $tb
     * @param TransactionRepositoryInterface $transactionRepository
     * @param Request                        $request
     * @param PayriffHelper                    $payriffHelper
     */
    public function __construct(
        OrderFactory $orderFactory,
        Context $context,
        TransactionBuilder $tb,
        TransactionRepositoryInterface $transactionRepository,
        Request $request,
        PayriffHelper $payriffHelper
    ) {
        $this->orderFactory             = $orderFactory;
        $this->config                   = $context->getScopeConfig();
        $this->transactionBuilder       = $tb;
        $this->transactionRepository    = $transactionRepository;
        $this->request                  = $request;
        $this->payriffHelper              = $payriffHelper;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getResponse(): string
    {
        $response = $this->responseNormalize($this->request->getBodyParams());
        return array_key_exists('status', $response) && $response['status'] === 'success'
            ? $this->getSuccessResponse($response)
            : $this->getFailedResponse($response);
    }

    /**
     * @param  $response
     * @return string
     */
    public function getSuccessResponse($response): string
    {
        if ($this->validateHash($response, $response['hash'])) {
            $order_id   = $this->normalizeMerchantOid($response['merchant_oid']);
            $order      = $this->orderFactory->create()->load($order_id);
            return $this->addTransactionToOrder($order, $response);
        } else {
            return 'Payriff notification failed: bad hash';
        }
    }

    /**
     * @param  $response
     * @return string
     * @throws Exception
     */
    public function getFailedResponse($response): string
    {
        if ($this->validateHash($response, $response['hash'])) {
            $order_id   = $this->normalizeMerchantOid($response['merchant_oid']);
            $order      = $this->orderFactory->create()->load($order_id);
            $order->addStatusHistoryComment($response['failed_reason_msg']);
            $order->cancel();
            $order->setState(Order::STATE_CANCELED);
            $order->setStatus("canceled");
            $order->save();
            return 'OK';
        } else {
            return 'Payriff notification failed: bad hash';
        }
    }

    /**
     * @param  $response
     * @param  $hash
     * @return bool
     */
    public function validateHash($response, $hash): bool
    {
        return base64_encode(hash_hmac('sha256', $response['merchant_oid'].$this->payriffHelper->getMerchantSalt().$response['status'].$response['total_amount'], $this->payriffHelper->getMerchantKey(), true)) === $hash;
    }

    /**
     * @param  $params
     * @return array
     */
    public function responseNormalize($params): array
    {
        $items = [];
        foreach ($params as $key => $param) {
            $items[$key] = $param;
        }
        return $items;
    }

    /**
     * @param  $merchant_oid
     * @return mixed|string
     */
    public function normalizeMerchantOid($merchant_oid): string
    {
        $merchant_oid = explode('SP', $merchant_oid);
        $merchant_oid = explode('MG', $merchant_oid[1]);
        return $merchant_oid[0];
    }

    /**
     * @param  $order
     * @param  $response
     * @return string
     */
    public function addTransactionToOrder($order, $response): string
    {
        if($order->getState())
        {
            $payment = $order->getPayment();
            $payment->setTransactionId($response['merchant_oid']);
            $trn = $payment->addTransaction(Transaction::TYPE_ORDER, null, true);
            $trn->setIsClosed(1)->save();
            $payment->addTransactionCommentsToOrder(
                $trn,
                $this->customNote($response, $order)
            );
            $payment->setParentTransactionId(null);
            $payment->save();
            $order->setStatus($this->payriffHelper->getOrderStatus());
            $order->save();
            return 'OK';
        }
        return 'Xəta: Sifariş statusu tamamlanmadı. Tekrar deneniyor.';
    }

    /**
     * @param  $response
     * @param  $order
     * @return string
     */
    public function customNote($response, $order): string
    {
        $currency               = $this->orderFactory->create()->load($order->getRealOrderId());
        $currency               = $currency->getOrderCurrency()->getId();
        $maturity_difference    = 'Vade Farkı: '.(round(($response['total_amount'] - $response['payment_amount']) / 100)).' '.$currency.'<br>';
        $total_amount           = number_format(($response['total_amount'] / 100), 2, '.', '.');
        $amount                 = number_format(($response['payment_amount'] / 100), 2, '.', '.');
        $note = '<b>'.__('Abhipay NOTICE - Payment Accepted').'</b><br>';
        $note .= __('Total Paid').': '.$total_amount.' '.$currency.'<br>';
        $note .= __('Paid').': '.$amount.' '.$currency.'<br>';
        $note .= ($response['installment_count'] === '1' ? '' : $maturity_difference);
        $note .= __('Installment Count').': '.($response['installment_count'] === '1' ? 'Birdəfəlik ödəmə' : $response['installment_count']).'<br>';
        return $note;
    }
}
