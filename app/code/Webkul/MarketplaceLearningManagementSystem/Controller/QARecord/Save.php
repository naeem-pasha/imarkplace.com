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

use Magento\Framework\App\Action\Context;
use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Webkul\MarketplaceLearningManagementSystem\Model\QARecordFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Save extends Action
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
     * @param QARecordFactory $qaRecordFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Data $helper,
        Context $context,
        JsonFactory $resultJsonFactory,
        QARecordFactory $qaRecordFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->qaRecordFactory = $qaRecordFactory;
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
        $qaRecordModel = $this->qaRecordFactory->create();

        $customerId = $this->helper->getCurrentCustomerId();
        $customer = $this->customerRepositoryInterface->getById($customerId);

        $formData = $this->getRequest()->getParams();
        try {
            if (!empty($formData['entity_id'])) {
                $qaRecordModel->load($formData['entity_id']);
            }
            if (isset($formData['title'])) {
                
                $qaRecordModel->setTitle($formData['title']);
            }
            if (isset($formData['detail'])) {
                $qaRecordModel->setDetail($formData['detail']);
            }
            if (isset($formData['contentId'])) {
                $qaRecordModel->setChapter($formData['contentId']);
            }
            $qaRecordModel->setReplies('0');
            $qaRecordModel->setStudentId($customerId);
            $qaRecordModel->setStudentName($customer->getFirstName()." ".$customer->getLastName());
            $qaRecordModel->setStatus('pending');
            $qaRecordModel->setCreatedDate($this->date->date()->format('d-m-Y h:i:s'));  
            if (isset($formData['courseId'])) { 
                $qaRecordModel->setCourseId($formData['courseId']);
                $qaRecordModel->save();
            }
            $message = __('QA Record save successfully.');
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
