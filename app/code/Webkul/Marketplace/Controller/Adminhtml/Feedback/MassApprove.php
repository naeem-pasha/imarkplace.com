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

namespace Webkul\Marketplace\Controller\Adminhtml\Feedback;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Marketplace\Model\ResourceModel\Feedback\CollectionFactory;
use Webkul\Marketplace\Helper\Notification as NotificationHelper;

/**
 * Class MassApprove used to multiple feedback approved.
 */
class MassApprove extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var NotificationHelper
     */
    protected $notificationHelper;
    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime          $dateTime
     * @param CollectionFactory                           $collectionFactory
     * @param \Webkul\Marketplace\Helper\Data             $helper
     * @param NotificationHelper                          $notificationHelper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        CollectionFactory $collectionFactory,
        \Webkul\Marketplace\Helper\Data $helper,
        NotificationHelper $notificationHelper
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_date = $date;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        $this->notificationHelper = $notificationHelper;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection as $item) {
            $item->setStatus(1);
            $item->setUpdatedAt($this->_date->gmtDate());
            $item->setSellerPendingNotification(1);
            $item->save();
            $reviewId = $item->getId();
            $this->notificationHelper->saveNotification(
                \Webkul\Marketplace\Model\Notification::TYPE_REVIEW,
                $reviewId,
                $reviewId
            );
        }

        $this->messageManager->addSuccess(
            __(
                'A total of %1 record(s) has been approved.',
                $collection->getSize()
            )
        );
        // clear cache
        $this->helper->clearCache();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Marketplace::feedback');
    }
}
