<?php

namespace Magento\Payriff\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Payriff\Helper\PayriffHelper;
use Magento\Payriff\Helper\PayriffRequestHelper;


/**
 * Class PayriffPaymentMethodIframe
 *
 * @package Magento\Payriff\Model
 */
class PayriffPaymentMethodIframe extends AbstractMethod
{

    protected $_code                = 'payriff_iframe';
    protected $_isInitializeNeeded  = true;
    protected $_isOffline           = false;
    protected $_canAuthorize        = true;
    protected $_isGateway           = true;
    protected $_canRefund           = true;




    public function getConfig(): array
    {
        $objectManager   = ObjectManager::getInstance();
        $logo            = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getValue('payment/payriff_iframe/payriff_logo');

        return [
            'payment' => [
                'payriff' => [
                    'logo_url' => 'https://abhipay.com.pk/assets/images/logo.svg',
                    'logo_visible' => $logo ? 'display: inline' : 'display: none'
                ]
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function getOrderPlaceRedirectUrl()
    {
        return ObjectManager::getInstance()->get('Magento\Framework\UrlInterface')->getUrl("payriff/redirect");
    }

}
