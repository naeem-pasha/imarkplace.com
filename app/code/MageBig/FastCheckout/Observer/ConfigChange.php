<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Observer;

use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Indexer\Model\IndexerFactory;

class ConfigChange implements ObserverInterface
{
    public const OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH = 'general/country/optional_zip_countries';
    public const XML_PATH_STATES_REQUIRED = 'general/region/state_required';
    public const XML_PATH_DISPLAY_ALL_STATES = 'general/region/display_all';
    public const XML_PATH_STREET_LINES = 'customer/address/street_lines';
    public const XML_PATH_TELEPHONE_SHOW = 'customer/address/telephone_show';
    public const XML_PATH_COMPANY_SHOW = 'customer/address/company_show';
    public const XML_PATH_FAX_SHOW = 'customer/address/fax_show';
    public const XML_PATH_VAT_SHOW = 'customer/create_account/vat_frontend_visibility';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var AllowedCountries
     */
    private $allowedCountryModel;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * @param RequestInterface $request
     * @param WriterInterface $configWriter
     * @param AllowedCountries $allowedCountryModel
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $configWriter,
        AllowedCountries $allowedCountryModel,
        IndexerFactory $indexerFactory
    ) {
        $this->request = $request;
        $this->configWriter = $configWriter;
        $this->allowedCountryModel = $allowedCountryModel;
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * Update data after change config
     *
     * @param EventObserver $observer
     * @return $this|void
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $params = $this->request->getParam('groups');

        if (isset($params['shipping_address'])) {
            $countries = implode(',', $this->allowedCountryModel->getAllowedCountries());
            $fields = $params['shipping_address']['fields'];
            $streetLinesValue = $fields['street_lines']['value'];
            $postcodeValue = $fields['postcode_show']['value'];
            $regionValue = $fields['region_show']['value'];
            $phoneValue = $fields['telephone_show']['value'];
            $companyValue = $fields['company_show']['value'];
            $faxValue = $fields['fax_show']['value'];
            $taxValue = $fields['taxvat_show']['value'];

            if (!$regionValue) {
                $this->configWriter->save(self::XML_PATH_STATES_REQUIRED, null);
                $this->configWriter->save(self::XML_PATH_DISPLAY_ALL_STATES, 0);
            } else {
                $states = 'AL,AR,AU,BG,BO,BR,BY,CA,CH,CL,CN,CO,DK,EC,EE,ES,' .
                    'GR,GY,HR,IN,IS,IT,LT,LV,MX,PE,PL,PT,PY,RO,SE,SR,US,UY,VE';
                $this->configWriter->save(
                    self::XML_PATH_STATES_REQUIRED,
                    $states
                );
                $this->configWriter->save(self::XML_PATH_DISPLAY_ALL_STATES, 1);
            }

            if (!$postcodeValue) {
                $this->configWriter->save(self::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH, $countries);
            } else {
                $this->configWriter->delete(self::OPTIONAL_ZIP_COUNTRIES_CONFIG_PATH);
            }

            if (!$taxValue) {
                $this->configWriter->delete(self::XML_PATH_VAT_SHOW);
            } else {
                $this->configWriter->save(self::XML_PATH_VAT_SHOW, 1);
            }

            $this->configWriter->save(self::XML_PATH_STREET_LINES, $streetLinesValue);
            $this->configWriter->save(self::XML_PATH_TELEPHONE_SHOW, $phoneValue);
            $this->configWriter->save(self::XML_PATH_COMPANY_SHOW, $companyValue);
            $this->configWriter->save(self::XML_PATH_FAX_SHOW, $faxValue);

            $indexer = $this->indexerFactory->create()->load('customer_grid');
            $indexer->reindexAll();
        }

        return $this;
    }
}
