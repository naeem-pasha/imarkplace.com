<?php

namespace Magento\Payriff\Controller\Redirect;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Payriff\Helper\PayriffHelper;
use Magento\Payriff\Helper\PayriffRequestHelper;


/**
 * Class Index
 *
 * @package Magento\Payriff\Controller\Redirect
 */
class Index extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    protected $payriffHelper;
    protected $payriffRequestHelper;
    

    /**
     * Index constructor.
     *
     * @param Context     $context
     * @param PageFactory $pageFactory
     */
    public function __construct(Context $context, PageFactory $pageFactory, PayriffHelper $payriffHelper, PayriffRequestHelper $payriffRequestHelper)
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->payriffHelper = $payriffHelper;
        $this->payriffRequestHelper = $payriffRequestHelper;

    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $url = $this->payriffRequestHelper->getPayriffUrl();

        header("Location: ". $url->payload->paymentUrl);
        exit();
    }
}