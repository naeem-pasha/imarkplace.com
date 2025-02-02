<?php
	namespace Meezan\MeezanBank\Gateway\Http;
	use Magento\Payment\Gateway\Http\TransferBuilder;
	use Magento\Payment\Gateway\Http\TransferFactoryInterface;
	use Magento\Payment\Gateway\Http\TransferInterface;
	use Meezan\MeezanBank\Gateway\Request\MockDataRequest;
	class TransferFactory implements TransferFactoryInterface 
	{
		private $transferBuilder;
		public function __construct(TransferBuilder $transferBuilder) 
		{
			$this->transferBuilder = $transferBuilder;
		} // end of __construct function
		public function create(array $request) 
		{
			return $this->transferBuilder->setBody($request)->setMethod('POST')->setHeaders(
				[ 'force_result' => isset($request[MockDataRequest::FORCE_RESULT]) ? $request[MockDataRequest::FORCE_RESULT] : null ]
			)->build();
		} // end of create function
	} // end of TransferFactory class