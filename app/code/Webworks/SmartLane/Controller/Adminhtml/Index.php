<?php


abstract class Index extends \Magento\Backend\App\Action
{
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Webworks_SmartLane::manage');
	}
}
?>