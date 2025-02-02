<?php
    namespace Meezan\MeezanBank\Gateway\Response;
    use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
    use Magento\Payment\Gateway\Response\HandlerInterface;
    use Magento\Sales\Model\Order\Payment;
    class FraudHandler implements HandlerInterface
    {
        const FRAUD_MSG_LIST = 'FRAUD_MSG_LIST';
        public function handle(array $handlingSubject, array $response)
        {
            if (!isset($response[self::FRAUD_MSG_LIST]) || !is_array($response[self::FRAUD_MSG_LIST])) 
            {
                return;
            }
            if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) 
            {
                throw new \InvalidArgumentException('Payment data object should be provided');
            }
            $paymentDO = $handlingSubject['payment'];
            $payment = $paymentDO->getPayment();
            $payment->setAdditionalInformation(
                self::FRAUD_MSG_LIST,
                (array)$response[self::FRAUD_MSG_LIST]
            );
            $payment->setIsTransactionPending(true);
            $payment->setIsFraudDetected(true);
        } // end of handle function
    } // end of FraudHandler class