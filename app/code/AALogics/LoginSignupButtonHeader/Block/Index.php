<?php
namespace AALogics\LoginSignupButtonHeader\Block;


class Index extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data=[],
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function is_login()
    {
        if($this->_customerSession->isLoggedIn())
		{
            return 'login';
        }else{
            return 'logout';
        }
        
    }
}