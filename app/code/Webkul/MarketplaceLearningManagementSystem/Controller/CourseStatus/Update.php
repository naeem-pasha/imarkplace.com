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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\CourseStatus;

use Magento\Framework\App\Action\Context;
use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseStatusFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseDataProviderFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;

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
     * @var CourseStatusFactory
     */
    protected $courseStatusFactory;

    /**
     * @var CourseDataProviderFactory
     */
    protected $courseDataProvider;

    /**
     * @param Data $helper
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CourseDataProviderFactory $courseDataProvider
     * @param CourseStatusFactory $courseStatusFactory
     */
    public function __construct(
        Data $helper,
        Context $context,
        JsonFactory $resultJsonFactory,
        CourseDataProviderFactory $courseDataProvider,
        CourseStatusFactory $courseStatusFactory
    ) {
        $this->helper = $helper;
        $this->courseDataProvider = $courseDataProvider;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->courseStatusFactory = $courseStatusFactory;
        parent::__construct($context);
    }

    /**
     * Update Course Status
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $error = false;
        $courseStatusModel = $this->courseStatusFactory->create();

        $customerId = $this->helper->getCurrentCustomerId();
        $customercourses = $this->courseDataProvider->create()->getCurrentCustomerCourseIds();
        $formData = $this->getRequest()->getParams();
        try {
            $courseStatusCollection = $this->courseStatusFactory->create()
                                        ->getCollection()
                                        ->addFieldToFilter('course_id', $formData['courseId'])
                                        ->addFieldToFilter('content_id', $formData['contentId'])
                                        ->addFieldToFilter('customer_id', $customerId);
            
            if (!$courseStatusCollection->getSize() && in_array($formData['courseId'], $customercourses)) {
                $courseStatusModel->setCustomerId($customerId);
                $courseStatusModel->setContentId($formData['contentId']);
                $courseStatusModel->setCourseId($formData['courseId']);

                $courseStatusModel->save();
            }

            $message = __('Course Status update successfully.');
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
