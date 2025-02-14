<?php
    namespace Meezan\MeezanBank\Gateway\Validator;
    use Magento\Payment\Gateway\Validator\AbstractValidator;
    use Magento\Payment\Gateway\Validator\ResultInterface;
    use Meezan\MeezanBank\Gateway\Http\Client\ClientMock;
    class ResponseCodeValidator extends AbstractValidator
    {
        const RESULT_CODE = 'RESULT_CODE';
        public function validate(array $validationSubject)
        {
            if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) 
            {
                throw new \InvalidArgumentException('Response does not exist');
            }
            $response = $validationSubject['response'];
            if ($this->isSuccessfulTransaction($response)) 
            {
                return $this->createResult(true,[]);
            } 
            else
            {
                return $this->createResult(false,[__('Gateway rejected the transaction.')]);
            }
        } // end of validate function
        private function isSuccessfulTransaction(array $response)
        {
            return isset($response[self::RESULT_CODE])
            && $response[self::RESULT_CODE] !== ClientMock::FAILURE;
        } // end of isSuccessfulTransaction function
    } // end of ResponseCodeValidator class