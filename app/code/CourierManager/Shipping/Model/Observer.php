<?php
namespace CourierManager\Shipping\Model;

use CourierManager\Shipping\Model\Carrier\CourierManagerApi;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\Country;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Observer implements ObserverInterface
{
    const COURIER_CODE = "couriermanager_couriermanager";

    /** @var CourierManagerApi */
    protected $apiClient;

    /** @var Store */
    protected $store;

    /** @var OrderRepositoryInterface */
    protected $orderRepository;

    /** @var ProductRepository */
    protected $productRepository;

    /** @var Session */
    protected $customerSession;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * Observer constructor.
     * @param OrderRepositoryInterface $OrderRepositoryInterface
     * @param ProductRepository $ProductRepository
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        OrderRepositoryInterface $OrderRepositoryInterface,
        ProductRepository $ProductRepository,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->orderRepository = $OrderRepositoryInterface;
        $this->productRepository = $ProductRepository;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->apiClient = new CourierManagerApi($this->getConfigData('api_url'), $this->getConfigData('api_key'));
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getDeliveryData(Order $order): array
    {
        // Delivery data
        $name = $order->getShippingAddress()->getFirstname() . " " . $order->getShippingAddress()->getLastname();

        $destCountry = "RO";
        if ($order->getShippingAddress()->getCountryId()) {
            $destCountry = $order->getShippingAddress()->getCountryId();
        }

        /** @var Country $countryModel */
        $objectManager = ObjectManager::getInstance();
        $countryName = $objectManager->create(Country::class)->load($destCountry)->getName();

        // API call data
        $deliveryData = array(
            "type" => $this->getConfigData('expedition_type'),
            "service_type" => $this->getConfigData('delivery_type_code'),
            "cnt" => $order->getTotalQtyOrdered(),
            "weight" => $order->getWeight(),
            "to_name" => $name,
            "to_contact" => $name,
            "to_phone" => $order->getShippingAddress()->getTelephone(),
            "to_email" => $order->getShippingAddress()->getEmail(),
            "to_country" => $countryName,
            "to_county" => $order->getShippingAddress()->getRegion(),
            "to_city" =>  $order->getShippingAddress()->getCity(),
            "to_address" => implode(", ", $order->getShippingAddress()->getStreet()),
            "to_zipcode" => $order->getShippingAddress()->getPostcode()
        );

        // Add cash on delivery
        if ($order->getPayment()->getMethod() == "cashondelivery") {

            $deliveryData["ramburs"] = $order->getBaseSubtotalInclTax();
            if ($this->getConfigData('payment_dest') == "1") {
                $deliveryData["ramburs"] = $order->getBaseGrandTotal();
            }

            $deliveryData["ramburs_type"] = $this->getConfigData('cod_type');
        }

        return $deliveryData;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer): Observer
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Store store */
        $this->store = $order->getStore();

        /** Check if is Courier Manager */
        if($order->getShippingMethod() != self::COURIER_CODE) {
            return $this;
        }

        /** Call API */
        $deliveryData = $this->getDeliveryData($order);
        $apiResponse = $this->apiClient->createAwb($deliveryData);

        if (isset($apiResponse->status) && $apiResponse->status == "done" && isset($apiResponse->data->no)) {
            $order->setShippingDescription("{$this->getConfigData('title')} (AWB {$apiResponse->data->no})");
        }

        return $this;
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getConfigData($field)
    {
        return $this->scopeConfig->getValue(
            'carriers/couriermanager/' . $field,
            ScopeInterface::SCOPE_STORE,
            $this->store
        );
    }
}
