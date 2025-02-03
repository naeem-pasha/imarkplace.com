<?php
/**
 * Webkul MpAffiliate Save Configuration controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Webkul\MpAffiliate\Model\ConfigrationForAffiliateFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

class Saveconfig extends \Magento\Customer\Controller\AbstractAccount
{
    public function __construct(
        Context $context,
        ConfigrationForAffiliateFactory $configrationForAffiliateFactory,
        \Magento\Customer\Model\Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Webkul\MpAffiliate\Logger\Logger $logger
    ) {
        $this->configrationForAffiliateFactory = $configrationForAffiliateFactory;
        $this->customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Affiliate Saveconfig
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            if ($this->getRequest()->isPost()) {
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/affiliateconfiguration',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
                $sellerId = $this->customerSession->getCustomerId();
                $wholedata = $this->getRequest()->getParams();
                $wholedata['seller_id'] =  $sellerId;
                list($datacol, $errors) = $this->validatePost($wholedata);
                if (empty($errors)) {
                    $model = $this->configrationForAffiliateFactory->create();
                    $model->load($sellerId, 'seller_id');
                    $id = $model->getId();
                    $model->setData($wholedata);
                    $model->setId($id);
                    $model->save();
                    $this->messageManager->addSuccess(__('Setting has been saved.'));
                } else {
                    foreach ($errors as $message) {
                        $this->messageManager->addError($message);
                    }
                }
            }
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/affiliateconfiguration',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/affiliateconfiguration',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError($e->getMessage());
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/affiliateconfiguration',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
        }
    }

    /**
     * validatePost Validate data when seller configure affiliate setting
     * @return Mixed Contains validated data and Errors(if any)
     */
    private function validatePost($wholeData)
    {
        $errors = [];
        $data = [];
        foreach ($wholeData as $code => $value) {
            switch ($code) {
                case 'per_click':
                    if (!preg_match("/^([0-9])+?[0-9.]*$/", $value)) {
                        $errors[] = __('Pay Per Click should contain only decimal numbers.');
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'unique_click':
                    if (!preg_match("/^([0-9])+?[0-9.]*$/", $value)) {
                        $errors[] = __('Per Unique Click should contain only decimal numbers.');
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'fixed_commission':
                    if (!preg_match("/^([0-9])+?[0-9.]*$/", $value)) {
                        $errors[] = __('Fixed Commission should contain only decimal numbers.');
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'percent_commission':
                    if (!preg_match("/^([0-9])+?[0-9.]*$/", $value)) {
                        $errors[] = __('Percent Commision should contain only decimal numbers.');
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'payout_balance':
                    if (!preg_match("/^([0-9])+?[0-9.]*$/", $value)) {
                        $errors[] = __('Minimum Payout should contain only decimal numbers.');
                    } else {
                        $data[$code] = $value;
                    }
                    break;
                case 'payment_day':
                    if (!preg_match("/^([0-9])+?[0-9.]*$/", $value)) {
                        $errors[] = __('Payment Day should contain only an integer number.');
                    } else {
                        $data[$code] = $value;
                    }
                    break;
            }
        }
        return [$data, $errors];
    }
}
