<?php

/**
 * Webkul_MpAffiliate email helper
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Helper;

use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Webkul\MpAffiliate\Helper\Data as AffiliateHelper;
use Magento\Customer\Model\Session;

/**
 * Webkul Affiliate Email helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customer;

    /**
     * @var Webkul\Affiliate\Helper\Data
     */
    private $affiliateHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context  $context,
     * @param tateInterface                          $inlineTranslation,
     * @param TransportBuilder                       $transportBuilder,
     * @param StoreManagerInterface                  $storeManager,
     * @param CustomerRepositoryInterface            $customer,
     * @param AffiliateHelper                        $affiliateHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customer,
        AffiliateHelper $affiliateHelper,
        Session $customerSession,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->logger = $logger;
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->affiliateHelper = $affiliateHelper;
    }

    /**
     * [generateTemplate description]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    public function generateTemplate(
        $emailTemplateVariables,
        $senderInfo,
        $receiverInfo,
        $emailTempId
    ) {
        try {
            $template =  $this->transportBuilder->setTemplateIdentifier($emailTempId)
            ->setTemplateOptions(
                ['area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()]
            )->setTemplateVars($emailTemplateVariables)->setFrom($senderInfo)->addTo(
                $receiverInfo['email'],
                $receiverInfo['name']
            );
            return $this;
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to Affiliate Manager
     * @param int $affUserId affiliate customer id
     * @param int $orderId id
     * @return void
     */

    public function sendMailToAffiliateAdmin($affUserId, $orderId)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();

            $senderInfo = [
                'name' => (string)__('noreply'),
                'email' => $this->scopeConfig->getValue('trans_email/ident_sales/email')
            ];
            $receiverInfo = [
                'name' => (string)__('Affiliate Manager'),
                'email' => $affiliateConfig['manager_email']
            ];
            $action = __('Please review this order'). '#'.$orderId
                            .__(' and approve affiliate order status');

            $emailTempVariables = [
                'notify' => "Order #".$orderId.__(" Placed by reference of Affiliate User ")
                                            .$customer->getFirstName()." ".$customer->getLastName(),
                'action' => $action
            ];
            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $affiliateConfig['manager_email_template']
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to Affiliate User on order complete
     * @param int $affUserId affiliate customer id
     * @param int $orderId id
     * @return void
     */
    public function sendMailToAffiliateUser($affUserId, $sellerId, $orderId)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affuserName = $customer->getFirstName()." ".$customer->getLastName();
            $receiverInfo = [
                'name' => $affuserName,
                'email' => $customer->getEmail()
            ];

            $customer = $this->customer->getById($sellerId);
            $senderInfo = [
                'name' => $customer->getFirstName()." ".$customer->getLastName(),
                'email' => $customer->getEmail()
            ];

            $emailTempVariables = [
                'notify' => __("Order No. #").$orderId.__(" has been completed"),
                'message' => __('Order No. #').$orderId
                                .__(' placed with your reference completed and payment added 
                                to your account')
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $this->scopeConfig->getValue('Marketplaceaffiliate/general/aff_user_email_template')
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    public function sendMailToSeller($sellerId, $orderId)
    {
        try {
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();

            $senderInfo = [
                'name' => (string)__('noreply'),
                'email' => $this->scopeConfig->getValue('trans_email/ident_sales/email')
            ];
            
            $customer = $this->customer->getById($sellerId);
            $receiverInfo = [
                'name' => (string)$customer->getFirstName()." ".$customer->getLastName(),
                'email' => $customer->getEmail()
            ];

            $emailTempVariables = [
                'notify' => "Order No. #".$orderId." has been completed",
                'message' => __('Order No. #').$orderId
                                .__(' for your product has been completed and payment added to the 
                                corresponding Affiliate user account')
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $this->scopeConfig->getValue('Marketplaceaffiliate/general/seller_email_template_for_order')
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to Affiliate User For Account Update
     * @param int $affUserId affiliate customer id
     * @param int $orderId id
     * @return void
     */
    public function accountUpdateNotify($affUserId, $status)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();

            $senderInfo = [
                            'name' => (string)__('Affiliate Manager'),
                            'email' => (string)$affiliateConfig['manager_email']
                        ];
            $receiverInfo = [
                'name' => $customer->getFirstName()." ".$customer->getLastName(),
                'email' => (string)$customer->getEmail()
            ];

            $emailTempVariables = [
                'notify' => __('Your Affiliate account status updated'),
                'message' => __('Your Affiliate account ').$status
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $this->scopeConfig->getValue('Marketplaceaffiliate/general/aff_user_update_email_template')
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to Affiliate User For Account Update
     * @param int $affUserId affiliate customer id
     * @param int $orderId id
     * @return void
     */
    public function accountUpdateNotifyBySeller($affUserId, $status)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();
            $senderInfo = [
                'name' => $this->customerSession->getCustomer()->getName(),
                'email' => $this->customerSession->getCustomer()->getEmail()
            ];
            $receiverInfo = [
                'name' => $customer->getFirstName()." ".$customer->getLastName(),
                'email' => $customer->getEmail()
            ];

            $emailTempVariables = [
                'notify' => __('Your affiliate request updated'),
                'message' => __('Your affiliate request to ').$this->customerSession->getCustomer()
                            ->getName()." has been ".$status
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $this->scopeConfig->getValue('Marketplaceaffiliate/general/aff_user_update_email_template_by_seller')
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to Seller For Request
     * @return void
     */
    public function sellerNotifyforAffiliateRequest($sellerId)
    {
        try {
            $customer = $this->customer->getById($sellerId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();
            $senderInfo = [
                'name' => $this->customerSession->getCustomer()->getName(),
                'email' => $this->customerSession->getCustomer()->getEmail()
            ];
            $receiverInfo = [
                'name' => $customer->getFirstName()." ".$customer->getLastName(),
                'email' => $customer->getEmail()
            ];

            $emailTempVariables = [
                'notify' => __('You have a Affiliate request'),
                'message' => __('You have a Affiliate request from ').$this->customerSession->getCustomer()
                            ->getName()
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $this->scopeConfig->getValue('Marketplaceaffiliate/general/affiliate_request_notification')
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to Affiliate User for normal notification
     * @param int $affUserId affiliate customer id
     * @param int $orderId id
     * @return void
     */

    public function sendMailToAffUserForNotify($affUserId, $sellerId, $data)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();
            if ($sellerId) {
                $sender = $this->customer->getById($sellerId);
                $senderInfo = [
                    'name' => $sender->getFirstName()." ".$sender->getLastName(),
                    'email' => $sender->getEmail()
                ];
            } else {
                $senderInfo = [
                    'name' => (string)__('Affiliate Manager'),
                    'email' => $affiliateConfig['manager_email']
                ];
            }
                
            $receiverInfo = [
                'name' => $customer->getFirstName()." ".$customer->getLastName(),
                'email' => $customer->getEmail()
            ];

            $emailTempVariables = [
                'message' => $data['message'],
                'subject' => $data['subject'],
                'name'    => $receiverInfo['name']
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $affiliateConfig['user_notify_by_seller']
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * send mail to User for email campaign
     * @param int $affUserId
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function emailCampaignMail($affUserId, $email, $subject, $message)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();

            $senderInfo = [
                'name' => $this->customerSession->getCustomer()->getName(),
                'email' => $this->customerSession->getCustomer()->getEmail()
            ];
            $receiverInfo = ['name' => 'Friends', 'email' => $email];
            $messageList = explode(' ', $message);
            $message='';
            foreach ($messageList as $mess) {
                if ($mess!='' && filter_var($mess, FILTER_VALIDATE_URL)) {
                    $message = $message.'<a href="'.$mess.'">"'.$mess.'"</a>, ';
                } elseif ($mess!='') {
                    $message = $message.$mess." ";
                }
            }
            $message=trim(trim($message), ",");
            $emailTempVariables = ['message' => $message,'subject' => $subject];
            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $affiliateConfig['aff_email_campaign']
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * Send mail to affiliate user when paymet credited in his bank account
     * @param int $affUserId affiliate customer id
     * @param int $orderId id
     * @return void
     */

    public function mailToAffPaymentCreditedNotify($affUserId, $data)
    {
        try {
            $customer = $this->customer->getById($affUserId);
            $affiliateConfig = $this->affiliateHelper->getAffiliateConfig();

            $senderInfo = [
                'name' => $this->customerSession->getCustomer()->getName(),
                'email' => $this->customerSession->getCustomer()->getEmail()
            ];
            $receiverInfo = [
                'name' => $customer->getFirstName()." ".$customer->getLastName(),
                'email' => $customer->getEmail()
            ];

            $emailTempVariables = [
                'message' => $data['email_content'],
                'subject' => $data['email_subject'],
                'name'    => $receiverInfo['name']
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $affiliateConfig['payment_credit_template']
            );

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}
