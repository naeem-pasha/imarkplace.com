<?php
    namespace Meezan\MeezanBank\Gateway\Response;
    use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
    use Magento\Payment\Gateway\Response\HandlerInterface;
    class TxnIdHandler implements HandlerInterface
    {
        const TXN_ID = 'TXN_ID';
        public function handle(array $handlingSubject, array $response)
        {
            if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) 
            {
                throw new \InvalidArgumentException('Payment data object should be provided');
            }
            $paymentDO = $handlingSubject['payment'];
            $payment = $paymentDO->getPayment();
            $payment->setTransactionId($response[self::TXN_ID]);
            $payment->setIsTransactionClosed(false);
        } // end of handle function
    } // end of TxnIdHandler class