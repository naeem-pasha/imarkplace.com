<?php

namespace Magento\Payriff\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;

/**
 * Class PayriffRequestHelper
 *
 * @package Magento\Payriff\Helper
 */
class PayriffRequestHelper
{

    /**
     * @var PayriffHelper
     */
    protected $payriffHelper;
    protected $transactionBuilder;

    /**
     * PayriffRequestHelper constructor.
     *
     * @param PayriffHelper $payriffHelper
     */
    public function __construct(PayriffHelper $payriffHelper, BuilderInterface $transactionBuilder)
    {
        $this->payriffHelper = $payriffHelper;
        $this->transactionBuilder = $transactionBuilder;
    }
 
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPayriffUrl()
    {
        $response = $this->callCurl($this->payriffHelper->makePostVariables(), 'createOrder');
        return json_decode($response['content']);
    }

    public function get_order_information($request)
    {
        $response = $this->callCurl($this->payriffHelper->makePostInformationVariables($request), 'getOrderInformation');
        return json_decode($response['content']);
    }

    /**
     * @param  $variables
     * @return mixed|string
     */
    private function callCurl($variables, $method): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.abhipay.com.pk/api/v2/".$method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($variables));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: '. $this->payriffHelper->getMerchantKey(),
            'Content-type: application/json'));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return "Abhipay IFRAME connection error. err:".curl_error($ch);
        }

        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        
        curl_close($ch);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $result;

        return $header;
    }

    public function addTransactionToOrder($order, $paymentData = array()) {
        try {
            // Prepare payment object
            $payment = $order->getPayment();
            $payment->setMethod('Payriff'); 
            $payment->setLastTransId($paymentData['orderID']);
            $payment->setTransactionId($paymentData['orderID']);
            $payment->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]);

            // Formatted price
            $formatedPrice = $order->getBaseCurrency()->formatTxt($order->getGrandTotal());
 
            // Prepare transaction
            $transaction = $this->transactionBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($paymentData['orderID'])
            ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData])
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

            // Add transaction to payment
            $payment->addTransactionCommentsToOrder($transaction, __('The authorized amount is %1.', $formatedPrice));
            $payment->setParentTransactionId(null);

            // Save payment, transaction and order
            $payment->save();
            $order->save();
            $transaction->save();
 
            return  $transaction->getTransactionId();

        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }
    }
}
