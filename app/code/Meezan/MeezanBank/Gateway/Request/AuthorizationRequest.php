<?php
    namespace Meezan\MeezanBank\Gateway\Request;
    use Magento\Payment\Gateway\ConfigInterface;
    use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
    use Magento\Payment\Gateway\Request\BuilderInterface;
    class AuthorizationRequest implements BuilderInterface
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
            $payment = $buildSubject['payment'];
            $order = $payment->getOrder();
            $address = $order->getShippingAddress();
            return [
                'TXN_TYPE' => 'A',
                'INVOICE' => $order->getOrderIncrementId(),
                'AMOUNT' => $order->getGrandTotalAmount(),
                'CURRENCY' => $order->getCurrencyCode(),
                'EMAIL' => $address->getEmail(),
                'MERCHANT_KEY' => $this->config->getValue(
                    'merchant_gateway_key',
                    $order->getStoreId()
                )
            ];
        } // end of build function
    } // end of AuthorizationRequest class