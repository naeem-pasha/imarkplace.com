<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Webkul\Marketplace\Helper\Data as HelperData;

/**
 * Webkul Marketplace Product Add Controller.
 */
class Add extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Array of actions which can be processed without secret key validation.
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @param Context                                       $context
     * @param Webkul\Marketplace\Controller\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory    $resultPageFactory
     * @param \Magento\Customer\Model\Session               $customerSession
     * @param CustomerUrl                                   $customerUrl
     * @param HelperData                                    $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Webkul\Marketplace\Controller\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        CustomerUrl $customerUrl,
        HelperData $helper
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context
        );
        $this->productBuilder = $productBuilder;
        $this->_resultPageFactory = $resultPageFactory;
        $this->customerUrl = $customerUrl;
        $this->helper = $helper;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Seller Product Add Action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            try {
                $set = $this->getRequest()->getParam('set');
                $type = $this->getRequest()->getParam('type');
                if (isset($set) && isset($type)) {
                    $allowedsets = explode(',', $helper->getAllowedAttributesetIds());
                    $allowedtypes = explode(',', $helper->getAllowedProductType());
                    if (!in_array($type, $allowedtypes) || !in_array($set, $allowedsets)) {
                        $this->messageManager->addError(
                            __('Product Type Or Attribute Set Invalid or Not Allowed')
                        );

                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/create',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    }
                    $product = $this->productBuilder->build(
                        $this->getRequest()->getParams(),
                        $helper->getCurrentStoreId()
                    );
                    $resultPage = $this->_resultPageFactory->create();
                    if ($helper->getIsSeparatePanel()) {
                        $resultPage->addHandle('marketplace_layout2_product_add');
                    }
                    $resultPage->getConfig()->getTitle()->set(
                        __('Add Product')
                    );

                    return $resultPage;
                } else {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/create',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                $this->helper->logDataInLogger(
                    "Controller_Product_Add execute : ".$e->getMessage()
                );
                $this->messageManager->addError($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/create',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
