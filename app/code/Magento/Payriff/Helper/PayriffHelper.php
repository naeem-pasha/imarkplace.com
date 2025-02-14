<?php

namespace Magento\Payriff\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class PayriffHelper
 *
 * @package Magento\Payriff\Helper
 */
class PayriffHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config               = $context->getScopeConfig();
        $this->checkoutSession      = $checkoutSession;
        $this->orderFactory         = $orderFactory;
        $this->_storeManager        = $storeManager;
    }

    /**
     * @return string
     */
    public function getScopeInterface(): string
    {
        return ScopeInterface::SCOPE_STORE;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->config->getValue('payment/payriff_iframe/merchant_id', $this->getScopeInterface()) ?? '151591';
    }


    public function getOrderStatus()
    {
        return $this->config->getValue('payment/payriff_iframe/order_status', $this->getScopeInterface());
    }

    /**
     * @return mixed
     */
    public function getMerchantKey()
    {
        return $this->config->getValue('payment/payriff_iframe/merchant_key', $this->getScopeInterface()) ?? 'NnYLzPw6CTtoNk5K';
    }

    /**
     * @return mixed
     */
    public function getDebugOn()
    {
        return $this->config->getValue('payment/payriff_iframe/debug_on', $this->getScopeInterface());
    }

    /**
     * @return int|mixed
     */
    public function getNoInstallment(): int
    {
        return $this->calculateInstallment($this->getCategoryInstallment(), $this->getCategoryIds())['no_installment'] ?? 0;
    }

    /**
     * @return int|mixed
     */
    public function getMaxInstallment(): int
    {
        return $this->calculateInstallment($this->getCategoryInstallment(), $this->getCategoryIds())['max_installment'] ?? 0;
    }

    /**
     * @return mixed
     */
    public function getTestMode()
    {
        return $this->config->getValue('payment/payriff_iframe/test_mode', $this->getScopeInterface()) ?? 1;
    }

    /**
     * @return mixed
     */
    public function getCategoryInstallment()
    {
        return json_decode($this->config->getValue('payment/payriff_iframe/categoryinstallment', $this->getScopeInterface()), true);
    }

    /**
     * @return mixed
     */
    public function getRealOrderId()
    {
        return $this->checkoutSession->getLastRealOrder()->getId();
    }

    /**
     * @return Order|OrderFactory
     */
    public function getOrder()
    {
        return $this->orderFactory->create()->load($this->getRealOrderId());
    }

    /**
     * @return mixed
     */
    public function getUserIp()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        return $ip;
    }

    /**
     * @return string
     */
    public function getUserBasket(): string
    {
        $user_basket = [];
        foreach ($this->checkoutSession->getLastRealOrder()->getAllVisibleItems() as $items) {
            $user_basket[] = [
                $items->getName(),
                number_format($items->getBaseOriginalPrice(), 2, '.', '.'),
                $items->getQtyToShip()
            ];
        }
        return base64_encode(json_encode($user_basket));
    }

    /**
     * @return string
     */
    public function getMerchantOid(): string
    {
        return 'SP'.$this->getRealOrderId().'MG'.strtotime($this->getOrder()->getUpdatedAt());
    }

    /**
     * @return OrderAddressInterface|null
     */
    public function getBilling(): ?OrderAddressInterface
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getBilling()->getEmail();
    }

    /**
     * @return false|string
     */
    public function getPaymentAmount()
    {
        return substr(str_replace('.', '', $this->getOrder()->getBaseGrandTotal()), 0, -2);
    }

    /**
     * @return mixed|string
     */
    public function getCurrency(): string
    {
        $currency = $this->getOrder()->getOrderCurrency()->getId();
        return $currency === 'TRY' ? 'TL' : $currency;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getBilling()->getFirstname() . ' ' . $this->getBilling()->getLastname();
    }

    /**
     * @return string
     */
    public function getUserPhone(): string
    {
        return $this->getBilling()->getTelephone();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMerchantOkUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl()."payriff/success";
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMerchantFailUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl()."payriff/fail";
    }

    /**
     * @return int
     */
    public function getTimeoutLimit(): int
    {
        return 30;
    }

    /**
     * @return string
     */
    public function getUserAddress(): string
    {
        return $this->getBilling()->getStreet()[0]
            . ' ' . $this->getBilling()->getCity()
            . ' ' . $this->getBilling()->getRegion()
            . ' ' . $this->getBilling()->getRegion()
            . ' ' . $this->getBilling()->getCountryId()
            . ' ' . $this->getBilling()->getPostcode();
    }

    /**
     * @return array
     */
    public function getCategoryIds(): array
    {
        $ids = [];
        foreach ($this->checkoutSession->getLastRealOrder()->getAllItems() as $items) {
            $ids[] = $items->getProduct()->getCategoryIds();
        }
        return $ids;
    }

    /**
     * @param  $categoryInstallment
     * @param  $categoryIds
     * @param  false $in_table
     * @return array|int[]
     */
    public function calculateInstallment($categoryInstallment, $categoryIds, $in_table = false): array
    {
        $current_installments = [];
        if ($in_table) {
            foreach ($categoryIds as $id) {
                if (array_key_exists($id, $categoryInstallment)) {
                    $current_installments[] = $categoryInstallment[$id][0];
                }
            }
            return $this->getCurrentInstallment($current_installments);
        }
        foreach ($categoryIds as $key => $ids) {
            foreach ($ids as $id) {
                if (array_key_exists($id, $categoryInstallment)) {
                    $current_installments[] = $categoryInstallment[$id][0];
                }
            }
        }
        return $this->getCurrentInstallment($current_installments);
    }

    /**
     * @param  $installments
     * @return array|int[]
     */
    public function getCurrentInstallment($installments): array
    {
        if (in_array('1', $installments)) {
            return [
                'no_installment'    => 1,
                'max_installment'   => 0,
            ];
        } elseif (($key = array_search('0', $installments)) !== false && count($installments) > 1) {
            unset($installments[$key]);
            return [
                'no_installment'    => 0,
                'max_installment'   => min($installments),
            ];
        }
        return [
            'no_installment'    => 0,
            'max_installment'   => count($installments) ? min($installments) : 0,
        ];
    }

    /**
     * @return mixed|string
     */
    public function getLang(): string
    {
        $objectManager = ObjectManager::getInstance()->get('Magento\Framework\Locale\Resolver')->getLocale();
        return explode('_', $objectManager)[0] ?? 'en';
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function makePostVariables(): array
    {
        
        $request_data = [
            'body' => [
                'amount'        => $this->getPaymentAmount()/100,
                'currencyType'  => 'PKR',//$this->getCurrency(),
                'description'   => $this->getRealOrderId(),
                'language'      => 'EN',
                'approveURL'    => $this->getMerchantOkUrl(),
                'cancelURL'     => $this->getMerchantFailUrl(),
                'declineURL'    => $this->getMerchantFailUrl(),
            ],
            'merchant'          => $this->getMerchantId()
        ];

        return $request_data;


        return array(
            'merchant_id'       =>  $this->getMerchantId(),
            'user_ip'           =>  $this->getUserIp(),
            'merchant_oid'      =>  $this->getMerchantOid(),
            'email'             =>  $this->getEmail(),
            'payment_amount'    =>  $this->getPaymentAmount(),
            'payriff_token'     =>  $this->getToken(),
            'user_basket'       =>  $this->getUserBasket(),
            'debug_on'          =>  $this->getDebugOn(),
            'no_installment'    =>  $this->getNoInstallment(),
            'max_installment'   =>  $this->getMaxInstallment(),
            'user_name'         =>  $this->getUsername(),
            'user_address'      =>  $this->getUserAddress(),
            'user_phone'        =>  $this->getUserPhone(),
            'merchant_ok_url'   =>  $this->getMerchantOkUrl(),
            'merchant_fail_url' =>  $this->getMerchantFailUrl(),
            'timeout_limit'     =>  $this->getTimeoutLimit(),
            'currency'          =>  $this->getCurrency(),
            'test_mode'         =>  $this->getTestMode(),
            'lang'              =>  $this->getLang()
        );
    }

    public function encryptionToken(): string
    {
       return $this->getRealOrderId() . time() . rand();
    }

        /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function makePostInformationVariables($request): array
    {

        $request_data = [
            'body' => [
                'languageType'  => 'EN',
                'sessionId'     => $request['sessionId'],
            ],
            'merchant'          => $this->getMerchantId()
        ];

        return $request_data;
    }
}
