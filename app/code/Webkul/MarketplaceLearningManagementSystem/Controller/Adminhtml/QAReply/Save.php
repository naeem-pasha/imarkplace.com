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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\Adminhtml\QAReply;

use Webkul\MarketplaceLearningManagementSystem\Model\QAReplyFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\QARecordFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;

/**
 * Save QA Reply Data
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var QAReplyFactory
     */
    protected $qaReplyFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Session
     */
    protected $authSession;

    protected $date;  

    /**
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param SessionManagerInterface $session
     * @param QAReplyFactory $qaReplyFactory
     * @param JsonFactory $resultJsonFactory
     * @param QARecordFactory $qaRecordFactory
     * @param Session $authSession
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        SessionManagerInterface $session,
        QAReplyFactory $qaReplyFactory,
        JsonFactory $resultJsonFactory,
        QARecordFactory $qaRecordFactory,
        Session $authSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->qaReplyFactory = $qaReplyFactory;
        $this->authSession = $authSession;
        $this->qaRecordFactory = $qaRecordFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->date = $date;
    }

    /**
     * Save QA Reply Data
     */
    public function execute()
    {
        $error = false;
        $qaReplyModel = $this->qaReplyFactory->create();
        $formData = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();

        if (!empty($formData['back'])) {
            $resultJson->setData(
                [
                    'message' => '',
                    'error' => false,
                ]
            );
        
            return $resultJson;
        }

        $qaRecordModel = $this->qaRecordFactory->create();

        if ($this->formKeyValidator->validate($this->getRequest())) {
            try {
                if (!empty($formData['entity_id'])) {
                    $qaReplyModel->load($formData['entity_id']);
                }
                if ($this->session->getQaId() != null) {
                    $qaReplyModel->setQaId($this->session->getQaId());

                    if (empty($formData['entity_id'])) {
                        $qaRecordModel->load($this->session->getQaId());
                        $qaRecordModel->setStatus($formData['status']);
                        $replies = $qaRecordModel->getReplies()+1;
                        $qaRecordModel->setReplies($replies);
                        $qaRecordModel->save();
                    }
                }

                $username = $this->authSession->getUser()->getFirstName()." ".
                            $this->authSession->getUser()->getFirstName();

                $qaReplyModel->setRepliedBy($username);

                if (isset($formData['qna_thread'])) {
                    $qaReplyModel->setQnaThread($formData['qna_thread']);
                }
                if (!isset($formData['created_at'])) {
                    $qaReplyModel->setCreatedAt($this->date->date()->format('d-m-Y h:i:s'));
                }

                $qaReplyModel->save();
                $message = __('Content data save successfully.');

            } catch (\Exception $e) {
                $error = true;
                $message = $e->getMessage();
            }
        }
        $resultJson->setData(
            [
                'message' => $message,
                'error' => $error,
            ]
        );
    
        return $resultJson;
    }
}
