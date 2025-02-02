<?php

namespace AALogics\CommissionWebKul\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Webkul\B2BMarketplace\Helper\Data;
use Webkul\Marketplace\Model\SaleperpartnerFactory;

class ConfigChange implements ObserverInterface
{
    private $request;
    private $configWriter;

    /**
     * @var SaleperpartnerFactory
     */
    protected $saleperpartner;

    /**
     * @var Webkul\B2BMarketplace\Helper\Data
     */
    protected $_helperData;

    public function __construct(
        RequestInterface $request, 
        WriterInterface $configWriter,
        SaleperpartnerFactory $saleperpartner,
        Data $marketplaceHelper
    )
    {
        $this->request = $request;
        $this->configWriter = $configWriter;
        $this->saleperpartner = $saleperpartner;
        $this->_helperData = $marketplaceHelper;
    }

    public function execute(EventObserver $observer)
    {
        $meetParams = $this->request->getParam('groups');
        // Get Commission Rate value
        $commissionRate = $meetParams['general_settings']['fields']['percent']['value'];
        // Get custom supplier commission rates
        $salePerPartnerCollection = $this->saleperpartner->create()
            ->getCollection()
            ->addFieldToFilter('commission_status', 1);

        if ($salePerPartnerCollection->getSize() > 0) {
            foreach ($salePerPartnerCollection as $supplier) {
                // Update Commission Rates
                $collectionUpdate = $this->saleperpartner->create()->load($supplier->getEntityId());
                $collectionUpdate->setCommissionRate($commissionRate);
                $collectionUpdate->save();
            }
        }

        return $this;
    }
}
?>