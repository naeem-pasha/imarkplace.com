<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Controller\Order\Creditmemo;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Webkul Marketplace Order Creditmemo Print pdf Controller.
 */
class Printpdf extends \Webkul\Marketplace\Controller\Order
{
    public function execute()
    {
        $helper = $this->helper;
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            if ($creditmemo = $this->_initCreditmemo()) {
                try {
                    $pdf = $this->creditmemoPdf->getPdf(
                        [$creditmemo]
                    );
                    $date = $this->date->date('Y-m-d_H-i-s');

                    return $this->fileFactory->create(
                        'creditmemo'.$date.'.pdf',
                        $pdf->render(),
                        DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());

                    return $this->resultRedirectFactory->create()->setPath(
                        'marketplace/order/history',
                        [
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                } catch (\Exception $e) {
                    $this->helper->logDataInLogger(
                        "Controller_Order_Creditmemo_Printpdf execute : ".$e->getMessage()
                    );
                    $this->messageManager->addError(
                        __('We can\'t print the creditmemo right now.').$e->getMessage()
                    );

                    return $this->resultRedirectFactory->create()->setPath(
                        'marketplace/order/history',
                        [
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    'marketplace/order/history',
                    [
                        '_secure' => $this->getRequest()->isSecure()
                    ]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
