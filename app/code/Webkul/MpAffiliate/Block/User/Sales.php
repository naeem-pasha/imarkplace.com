<?php
/**
 * Webkul MpAffiliate Sales.
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

class Sales extends \Webkul\MpAffiliate\Block\User\UserAbstract
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
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
     */
    protected $timezone;

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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->_customer = $customer;
        $this->customerSession = $customerSession;
        $this->paymentFactory = $paymentFactory;
        $this->saleFactory = $salefactory;
        $this->orderFactory = $orderFactory;
        $this->customerFactory = $customerFactory;
        $this->timezone = $timezone;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    /*get data from sales table*/
    public function getSaleAff()
    {
        $customerId = $this->customerSession->getCustomerId();
        $orderId = $this->getRequest()->getParam('aff_order')!=""?$this->getRequest()->getParam('aff_order'):"";
        $saleColl = $this->saleFactory->create()->getCollection()
                                                ->addFieldToFilter('aff_customer_id', $customerId)
                                                ->addFieldToFilter('order_increment_id', ['like'=>'%'.$orderId.'%'])
                                                ->setOrder('entity_id', 'desc');
        return($saleColl->getData());
    }

    public function getSellerName($customerId)
    {
        return $this->customerFactory->create()->load($customerId)->getName();
    }
}
