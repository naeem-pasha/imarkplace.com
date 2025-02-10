<?php

namespace CourierManager\Shipping\Model\Carrier;

use Exception;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;

/**
 * Class CourierManager
 * @package CourierManager\Shipping\Model\Carrier
 */
class CourierManager extends AbstractCarrier implements CarrierInterface
{

    const DEFAULT_COUNTRY = "RO";

    /**
     * @var string
     */
    protected $_code = 'couriermanager';

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /** @var RateRequest */
    protected $request;

    /** @var Session */
    protected $session;

    /** @var CourierManagerApi */
    protected $apiClient;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Session $session,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->session = $session;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->apiClient = new CourierManagerApi($this->getConfigData('api_url'), $this->getConfigData('api_key'));
    }

    /**
     * @param Quote $quote
     * @return array
     */
    private function getDeliveryData(Quote $quote): array
    {
        $destCountry = self::DEFAULT_COUNTRY;
        if ($this->request->getDestCountryId()) {
            $destCountry = $this->request->getDestCountryId();
        }

        $objectManager = ObjectManager::getInstance();

        /** @var Country $countryModel */
        $countryName = $objectManager->create(Country::class)->load($destCountry)->getName();

        /** @var Region $countryModel */
        $regionName = $objectManager->create(Region::class)->load($this->request->getDestRegionId())->getName();

        // API call data
        $deliveryData = array(
            "type" => $this->getConfigData('expedition_type'),
            "service_type" => $this->getConfigData('delivery_type_code'),
            "cnt" => $this->request->getPackageQty(),
            "weight" => $this->request->getPackageWeight(),
            "to_country" => $countryName,
            "to_county" => $regionName,
            "to_city" => $this->request->getDestCity(),
            "to_address" => str_replace("\n", " ", $this->request->getDestStreet()),
            "to_zipcode" => $this->request->getDestPostcode()
        );

        if ($quote->getPayment()->getMethod() == "cashondelivery") {
            $deliveryData["ramburs"] = $quote->getGrandTotal();
            $deliveryData["ramburs_type"] = $this->getConfigData('cod_type');
        }

        return $deliveryData;
    }

    /**
     * @return int|mixed|null
     */
    private function getShippingCost()
    {
        // Get cart object
        try {
            $quote = $this->session->getQuote();
        } catch (Exception $e) {
            return null;
        }

        // Check if total is upper minim free sum
        if (is_numeric($this->getConfigData('minim_free')) && $this->request->getBaseSubtotalInclTax() > $this->getConfigData('minim_free')) {
            return 0;
        }

        // Return fix amount if is set
        if (is_numeric($this->getConfigData('fix_amount')) && $this->getConfigData('fix_amount') > 0) {
            return $this->getConfigData('fix_amount');
        }

        // Call API
        $deliveryData = $this->getDeliveryData($quote);
        $apiPrice = $this->apiClient->priceAwb($deliveryData);

        // Check if response is ok
        if (!isset($apiPrice->status) || $apiPrice->status != "done") {
            return null;
        }

        $taxes = $apiPrice->data->price * (  $this->getConfigData('shipping_vat') / 100 );

        // Return delivery price
        return $apiPrice->data->price + $taxes;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // Set request
        $this->request = $request;

        $method = $this->rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('title'));

        $shippingCost = $this->getShippingCost();
        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);


        $result = $this->rateResultFactory->create();
        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
