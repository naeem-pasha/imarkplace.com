<?php
/**
 * Webkul MpAffiliate Payment Block.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Block\Marketplace;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\PaymentFactory;

class Payment extends \Magento\Framework\View\Element\Template
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
        PaymentFactory $paymentFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->paymentFactory = $paymentFactory;
        $this->customerFactory = $customerFactory;
        $this->affDataHelper = $affDataHelper;
        parent::__construct($context, $data);
    }

    public function getPaymentOfSeller()
    {
        $filterEmail =  $this->getRequest()->getParam('aff_email')!=""?$this->getRequest()->getParam('aff_email'):"";
        $filterName =   $this->getRequest()->getParam('aff_name')!=""?$this->getRequest()->getParam('aff_name'):"";
        $affIds = $this->customerFactory->create()->getCollection()
                                    ->addExpressionAttributeToSelect(
                                        'name',
                                        'LOWER(CONCAT({{firstname}}," ",{{lastname}}))',
                                        ['firstname','lastname']
                                    )
                                    ->addFieldToFilter('email', ['like'=>'%'.$filterEmail.'%'])
                                    ->addFieldToFilter('name', ['like'=>'%'.$filterName.'%'])
                                    ->getAllIds();
        $sellerId = $this->customerSession->getCustomerId();
        $paymentColl = $this->paymentFactory->create()->getCollection()
                                ->addFieldToFilter('seller_id', $sellerId)
                                ->addFieldToFilter('aff_customer_id', ['in'=>$affIds])
                                ->setOrder('created_at', 'DESC');
        ;
        return $paymentColl->getData();
    }
    public function getCustomerData($customerId)
    {
        return $this->affDataHelper->getCustomerData($customerId)->getData();
    }
}
