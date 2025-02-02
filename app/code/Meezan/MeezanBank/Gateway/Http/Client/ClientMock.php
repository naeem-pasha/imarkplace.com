<?php
    namespace Meezan\MeezanBank\Gateway\Http\Client;
    use Magento\Payment\Gateway\Http\ClientInterface;
    use Magento\Payment\Gateway\Http\TransferInterface;
    use Magento\Payment\Model\Method\Logger;
    class ClientMock implements ClientInterface
    {
        const SUCCESS = 1;
        const FAILURE = 0;
        private $results = 
        [
            self::SUCCESS,
            self::FAILURE
        ];
        private $logger;
        public function __construct(Logger $logger) 
        {
            $this->logger = $logger;
        } // end of __construct function
        public function placeRequest(TransferInterface $transferObject)
        {
            $response = $this->generateResponseForCode($this->getResultCode($transferObject));
            $this->logger->debug(
                [
                    'request' => $transferObject->getBody(),
                    'response' => $response
                ]
            );
            return $response;
        } // end of placeRequest function
        protected function generateResponseForCode($resultCode)
        {
            return array_merge(
                [
                    'RESULT_CODE' => $resultCode,
                    'TXN_ID' => $this->generateTxnId()
                ],
                $this->getFieldsBasedOnResponseType($resultCode)
            );
        } // end of generateResponseForCode function
        protected function generateTxnId()
        {
            return md5(mt_rand(0, 1000));
        } // end of generateTxnId function
        private function getResultCode(TransferInterface $transfer)
        {
            $headers = $transfer->getHeaders();
            if (isset($headers['force_result'])) 
            {
                return (int)$headers['force_result'];
            }
            return $this->results[mt_rand(0, 1)];
        } // end of getResultCode function
        private function getFieldsBasedOnResponseType($resultCode)
        {
            switch ($resultCode) {
                case self::FAILURE:
                    return [
                        'FRAUD_MSG_LIST' => [
                            'Stolen card',
                            'Customer location differs'
                        ]
                    ];
            }
            return [];
        } // end of getFieldsBasedOnResponseType function
    } // end of ClientMock class