<?php
namespace AALogics\BlogCategoryWidget\Block;

class BlogsShowPdpBlock extends \Magento\Framework\View\Element\Template
{
	protected $_request;

	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\RequestInterface $request	
	)
	{
		parent::__construct($context);
		$this->_request = $request;

	}


}