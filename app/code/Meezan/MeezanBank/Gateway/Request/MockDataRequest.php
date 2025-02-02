<?php
    namespace Meezan\MeezanBank\Gateway\Request;
    use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
    use Magento\Payment\Gateway\Request\BuilderInterface;
    use Meezan\MeezanBank\Gateway\Http\Client\ClientMock;
    class MockDataRequest implements BuilderInterface
    {
        const FORCE_RESULT = 'FORCE_RESULT';
        public function build(array $buildSubject)
        {
            if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) 
            {
                throw new \InvalidArgumentException('Payment data object should be provided');
            }
            $paymentDO = $buildSubject['payment'];
            $payment = $paymentDO->getPayment();
            $transactionResult = $payment->getAdditionalInformation('transaction_result');
            return [
                self::FORCE_RESULT => $transactionResult === null
                    ? ClientMock::SUCCESS
                    : $transactionResult
            ];
        } // end of build function
    } // end of MockDataRequest class