<?php
	namespace Meezan\MeezanBank\Block;
	use Magento\Framework\Phrase;
	use Magento\Payment\Block\ConfigurableInfo;
	use Meezan\MeezanBank\Gateway\Response\FraudHandler;
	class Info extends ConfigurableInfo {
		protected function getLabel($field) {
			return __($field);
		} // END OF getLabel FUNCTION
		protected function getValueView($field, $value) {
			switch ($field) {
				case FraudHandler::FRAUD_MSG_LIST:
					return implode('; ', $value);
			}
			return parent::getValueView($field, $value);
		} // END OF getValueView FUNCTION
	} // END OF Info CLASS