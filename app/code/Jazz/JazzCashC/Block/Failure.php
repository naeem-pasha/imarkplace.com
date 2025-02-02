<?php

namespace Jazz\JazzCashC\Block;

class Failure extends \Magento\Framework\View\Element\Template {
	
	public function __construct(
			\Magento\Backend\Block\Template\Context $context, array $data = []) {
		parent::__construct ( $context, $data );
	}
	
	public function getMymodule() {
		return 'Jazz Cash Card Module Created Successfully';
	}
}
