<?php
/**
 * Webkul MpAffiliate Traffic.
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
use Webkul\MpAffiliate\Model\ResourceModel\Clicks\CollectionFactory;

class Traffic extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @var \Webkul\Affiliate\Model\ResourceModel\Clicks\CollectionFactory
     */
    private $trafficlicks;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context           $context
     * @param Session           $customerSession,
     * @param AffDataHelper     $affDataHelper,
     * @param CollectionFactory $collectionFactory,
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        CollectionFactory $collectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    /**
     * @return bool|\Magento\Ctalog\Model\ResourceModel\Product\Collection
     */
    public function getAllTraffic()
    {
        if (!($customerId = $this->getCustomerSession()->getCustomerId())) {
            return false;
        }
        if (!$this->trafficlicks) {
            $collection = $this->collectionFactory->create()->addFieldToFilter('aff_customer_id', $customerId)
                                        ->setOrder('entity_id', 'desc');
            $uniqueFilter = $this->getRequest()->getParam('u');
            if ($uniqueFilter) {
                $collection->getSelect()->group('customer_ip');
            }
            $this->trafficlicks = $collection;
        }
        return $this->trafficlicks;
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllTraffic()) {
            $pager = $this->getLayout()
                        ->createBlock(\Magento\Theme\Block\Html\Pager::class, 'mpaffiliate.traffic.list.pager')
                        ->setCollection($this->getAllTraffic());
            $this->setChild('pager', $pager);
            $this->getAllTraffic()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * getDateTimeAsLocale
     * @param string $data in base Time zone
     * @return string date in current Time zone
     */
    public function getDateTimeAsLocale($data)
    {
        if ($data) {
            return $this->_localeDate->date($data)->format('g:ia \o\\n l jS F Y');
        }
        return $data;
    }
}
