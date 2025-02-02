<?php
namespace AALogics\DeleteUserAccount\Block;

class Delete extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data=[]
    )
    {
        parent::__construct($context, $data);
    }

    public function delete()
    {
        return "Are you sure you want to delete this account?";
    }
}