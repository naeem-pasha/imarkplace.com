<?php
/**
 * Webkul MpAffiliate Paypal Failed controller.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Marketplace;

use Magento\Framework\Controller\ResultFactory;

class PaypalFailed extends \Magento\Customer\Controller\AbstractAccount
{
    public function execute()
    {
        $this->messageManager->addError(__('Your payment has been declined.'));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('mpaffiliate/marketplace/summary/');
    }
}
