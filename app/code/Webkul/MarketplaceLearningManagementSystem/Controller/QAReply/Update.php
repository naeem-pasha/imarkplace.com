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

use Magento\Framework\App\Action\Context;
use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Webkul\MarketplaceLearningManagementSystem\Model\QAReplyFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\QARecordFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Update extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var QAReplyFactory
     */
    protected $qaReplyFactory;

    /**
     * @var QARecordFactory
     */
    protected $qaRecordFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    protected $date;  


    /**
     * @param Data $helper
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param QAReplyFactory $qaReplyFactory
     * @param QARecordFactory $qaRecordFactory
     * @param SessionManagerInterface $session
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Data $helper,
        Context $context,
        JsonFactory $resultJsonFactory,
        QAReplyFactory $qaReplyFactory,
        QARecordFactory $qaRecordFactory,
        SessionManagerInterface $session,
        CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->qaReplyFactory = $qaReplyFactory;
        $this->qaRecordFactory = $qaRecordFactory;
        $this->session = $session;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->date = $date;
        parent::__construct($context);
    }

    /**
     * Get QA Record Data
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $error = false;
        $qaReplyModel = $this->qaReplyFactory->create();
        $qaRecordModel = $this->qaRecordFactory->create();

        $customerId = $this->helper->getCurrentCustomerId();
        $customer = $this->customerRepositoryInterface->getById($customerId);

        $formData = $this->getRequest()->getParams();
        try {
            if (!empty($formData['entity_id'])) {
                $qaReplyModel->load($formData['entity_id']);
            }
            if (isset($formData['qna_thread'])) {
                
                $qaReplyModel->setQnaThread($formData['qna_thread']);
            }
            if (isset($formData['qa_id']) || $this->session->getQaId() != null) {
                if (isset($formData['qa_id'])) {
                    $qaId = $formData['qa_id'];
                } else {
                    $qaId = $this->session->getQaId();
                }
                $qaReplyModel->setQaId($qaId);

                $qaRecordModel->load($qaId);
                $qaRecordModel->setStatus($formData['status']);
                $replies = $qaRecordModel->getReplies()+1;
                $qaRecordModel->setReplies($replies);
                $qaRecordModel->save();
            }
            if (isset($formData['contentId'])) {
                $qaReplyModel->setChapter($formData['contentId']);
            }
            $qaReplyModel->setCreatedAt($this->date->date()->format('d-m-Y h:i:s'));  
            $qaReplyModel->setRepliedBy($customer->getFirstName()." ".$customer->getLastName());

            $qaReplyModel->save();

            $message = __('QA Reply save successfully.');
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

    /**
     * Get User Sort Name
     *
     * @param string $name
     * @return string
     */
    protected function getUserSortName($name)
    {
        $name = explode(' ', $name);
        return $name[0][0].$name[1][0];
    }
}
