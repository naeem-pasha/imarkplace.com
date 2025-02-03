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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\QAReply;

use Webkul\MarketplaceLearningManagementSystem\Model\QAReplyFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\QARecordFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;

/**
 * Delete QA Reply Data
 */
class Delete extends Action
{
    /**
     * @var QAReplyFactory
     */
    protected $qaReplyFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var QARecordFactory
     */
    protected $qaRecordFactory;

    /**
     * @param Context $context
     * @param QAReplyFactory $qaReplyFactory
     * @param QARecordFactory $qaRecordFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        QAReplyFactory $qaReplyFactory,
        QARecordFactory $qaRecordFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->qaReplyFactory = $qaReplyFactory;
        $this->qaRecordFactory = $qaRecordFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Delete QA Reply Data
     */
    public function execute()
    {
        $error = false;
        $qaReplyModel = $this->qaReplyFactory->create();

        $qaRecordModel = $this->qaRecordFactory->create();

        $id = $this->getRequest()->getParam('id');
        try {
            $qaReplyModel->load($id);

            $qaRecordModel->load($qaReplyModel->getQaId());
            $qaRecordModel->setReplies($qaRecordModel->getReplies()-1);
            $qaRecordModel->save();
            $qaReplyModel->delete();
            $message = __('Reply data delete successfully.');
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'message' => $message,
                'error' => $error,
            ]
        );
    
        return $resultJson;
    }
}
