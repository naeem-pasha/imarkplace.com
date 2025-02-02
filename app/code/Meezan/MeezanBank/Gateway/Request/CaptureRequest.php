<?php
    namespace Meezan\MeezanBank\Gateway\Request;
    use Magento\Payment\Gateway\ConfigInterface;
    use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
    use Magento\Payment\Gateway\Request\BuilderInterface;
    use Magento\Sales\Api\Data\OrderPaymentInterface;
    class CaptureRequest implements BuilderInterface
    {
        private $config;
        public function __construct(ConfigInterface $config) 
        {
            $this->config = $config;
        } // end of __construct function
        public function build(array $buildSubject)
        {
            if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) 
            {
                throw new \InvalidArgumentException('Payment data object should be provided');
            }
            $paymentDO = $buildSubject['payment'];
            $order = $paymentDO->getOrder();
            $payment = $paymentDO->getPayment();
            if (!$payment instanceof OrderPaymentInterface) 
            {
                throw new \LogicException('Order payment should be provided.');
            }
            return [
                'TXN_TYPE' => 'S',
                'TXN_ID' => $payment->getLastTransId(),
                'MERCHANT_KEY' => $this->config->getValue(
                    'merchant_gateway_key',
                    $order->getStoreId()
                )
            ];
        } // end of build function
    } // end of CaptureRequest class