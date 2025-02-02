<?php
/**
 * Webkul MpAffiliate User Mass Unapprove Controller
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Controller\Adminhtml\User;

use Magento\Framework\Controller\ResultFactory;

class MassUnapprove extends \Webkul\MpAffiliate\Controller\Adminhtml\User\MassApprove
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $recordUpdate = 0;
            foreach ($collection as $affiUser) {
                if ($affiUser->getStatus() == 1) {
                    $affiUser->setStatus(0);
                    $this->_saveObject($affiUser);
                    $recordUpdate++;
                    /** send account unapprove mail notification to Affiliate User*/
                    $this->helperEmail->accountUpdateNotify($affiUser->getCustomerId(), 'Unapproved');
                }
            }

            $this->messageManager->addSuccess(__('A total of %1 users(s) have been unapproved.', $recordUpdate));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index/');
    }
}
