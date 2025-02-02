<?php
/**
 * Webkul MpAffiliate Pyament.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\PaymentFactory;
use Webkul\MpAffiliate\Model\SaleFactory;
use Magento\Sales\Model\OrderFactory;

class Payment extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @var Magento\Customer\Model\Session
     */
    private $customerSession;

    protected $_customer;

    /**
     * @var Webkul\Affiliate\Helper\Data
     */
    private $affDataHelper;

    private $paymentfactory;

    private $saleFactory ;

    private $orderFactory;

    /**
     * @param Context         $context
     * @param Session         $customerSession,
     * @param RedirectFactory $redirect,
     * @param AffDataHelper   $affDataHelper,
     * @param array           $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        \Magento\Customer\Model\Customer $customer,
        PaymentFactory $paymentFactory,
        SaleFactory $salefactory,
        OrderFactory $orderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->_customer = $customer;
        $this->customerSession = $customerSession;
        $this->paymentFactory = $paymentFactory;
        $this->saleFactory = $salefactory;
        $this->orderFactory = $orderFactory;
        $this->customerFactory = $customerFactory;
        $this->affDataHelper = $affDataHelper;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    public function getPaymentOfUser()
    {
        $filterEmail =  $this->getRequest()->getParam('seller_email')!=""?
                        $this->getRequest()->getParam('seller_email'):"";
        $filterName =   $this->getRequest()->getParam('seller_name')!=""?
                        $this->getRequest()->getParam('seller_name'):"";
        $sellerIds = $this->customerFactory->create()->getCollection()
                    ->addExpressionAttributeToSelect('name', 'LOWER(CONCAT({{firstname}}," ",
                    {{lastname}}))', ['firstname','lastname'])
                    ->addFieldToFilter('email', ['like'=>'%'.$filterEmail.'%'])
                    ->addFieldToFilter('name', ['like'=>'%'.$filterName.'%'])
                    ->getAllIds();
        $affId = $this->customerSession->getCustomerId();
        $paymentColl = $this->paymentFactory->create()->getCollection()
                                ->addFieldToFilter('aff_customer_id', $affId)
                                ->addFieldToFilter('seller_id', ['in'=>$sellerIds])
                                ->setOrder('created_at', 'DESC');
        ;
        return $paymentColl->getData();
    }
    public function getCustomerData($customerId)
    {
        return $this->affDataHelper->getCustomerData($customerId)->getData();
    }
}
