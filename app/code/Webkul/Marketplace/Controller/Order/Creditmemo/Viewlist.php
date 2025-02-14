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

namespace Webkul\Marketplace\Controller\Order\Creditmemo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Webkul\Marketplace\Helper\Data as HelperData;
use Magento\Customer\Model\Url as CustomerUrl;

/**
 * Webkul Marketplace Order Creditmemo Viewlist Controller.
 */
class Viewlist extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var \Webkul\Marketplace\Helper\Orders
     */
    protected $orderHelper;

    /**
     * @param Context                         $context
     * @param PageFactory                     $resultPageFactory
     * @param OrderRepositoryInterface        $orderRepository
     * @param \Magento\Framework\Registry     $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param HelperData                      $helper
     * @param CustomerUrl                     $customerUrl
     * @param \Webkul\Marketplace\Helper\Orders $orderHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        HelperData $helper,
        CustomerUrl $customerUrl,
        \Webkul\Marketplace\Helper\Orders $orderHelper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_orderRepository = $orderRepository;
        $this->_customerSession = $customerSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->customerUrl = $customerUrl;
        $this->orderHelper = $orderHelper;
        parent::__construct($context);
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
     * Initialize order model instance.
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            $order = $this->_orderRepository->get($id);
            $tracking = $this->orderHelper->getOrderinfo($id);
            if ($tracking) {
                if ($tracking->getOrderId() == $id) {
                    if (!$id) {
                        $this->messageManager->addError(
                            __('This order no longer exists.')
                        );
                        $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                        return false;
                    }
                } else {
                    $this->messageManager->addError(
                        __('You are not authorize to manage this order.')
                    );
                    $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                    return false;
                }
            } else {
                $this->messageManager->addError(
                    __('You are not authorize to manage this order.')
                );
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(
                __('This order no longer exists.')
            );
            $this->helper->logDataInLogger(
                "Controller_Order_Creditmemo_Viewlist _initOrder : ".$e->getMessage()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(
                __('This order no longer exists.')
            );
            $this->helper->logDataInLogger(
                "Controller_Order_Creditmemo_Viewlist _initOrder : ".$e->getMessage()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);

        return $order;
    }

    /**
     * View Creditmemo List page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            if ($order = $this->_initOrder()) {
                /** @var \Magento\Framework\View\Result\Page $resultPage */
                $resultPage = $this->_resultPageFactory->create();
                if ($helper->getIsSeparatePanel()) {
                    $resultPage->addHandle('marketplace_layout2_order_creditmemo_viewlist');
                }
                $resultPage->getConfig()->getTitle()->set(
                    __('Credit Memo - Order #%1', $order->getRealOrderId())
                );

                return $resultPage;
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/history',
                    [
                        '_secure' => $this->getRequest()->isSecure()
                    ]
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
