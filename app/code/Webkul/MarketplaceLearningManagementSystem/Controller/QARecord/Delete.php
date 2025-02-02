<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceLearningManagementSystem\Controller\QARecord;

use Webkul\MarketplaceLearningManagementSystem\Model\QARecordFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\QAReplyFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;

/**
 * Delete QA Record
 */
class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var QARecordFactory
     */
    protected $qaRecordFactory;

    /**
     * @var QAReplyFactory
     */
    protected $qaReplyFactory;

    /**
     * @param Context $context
     * @param ManagerInterface $messageManager
     * @param QARecordFactory $qaRecordFactory
     * @param QAReplyFactory $qaReplyFactory
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        QARecordFactory $qaRecordFactory,
        QAReplyFactory $qaReplyFactory
    ) {
        parent::__construct($context);
        $this->qaRecordFactory = $qaRecordFactory;
        $this->_messageManager = $messageManager;
        $this->qaReplyFactory = $qaReplyFactory;
    }

    /**
     * Delete QA Record data
     */
    public function execute()
    {
        $error = false;
        $qaRecordModel= $this->qaRecordFactory->create();
        $qaReplyModel = $this->qaReplyFactory->create();
        $id = $this->getRequest()->getParam('id');
        try {
            $qaRecordModel->load($id);
            $qaRecordModel->delete();

            $replyData = $qaReplyModel->getCollection()->addFieldToFilter('qa_id', $id);

            foreach ($replyData as $replyItem) {
                $this->delete($replyItem);
            }
            
            $message = __('QA Record data delete successfully.');
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        if ($error) {
            $this->_messageManager->addError($message);
        } else {
            $this->_messageManager->addSuccess($message);
        }
        $this->_redirect($this->_redirect->getRefererUrl());
        //return $this;  
    }

    /**
     * Delete object
     *
     * @param object $object
     * @return void
     */
    public function delete($object)
    {
        $object->delete();
    }
}
