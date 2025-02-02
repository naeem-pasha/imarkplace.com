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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\Adminhtml\Section;

use Webkul\MarketplaceLearningManagementSystem\Model\CourseSectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

/**
 * Delete Section
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var CourseSectionFactory
     */
    protected $courseSectionFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param CourseSectionFactory $courseSectionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        CourseSectionFactory $courseSectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->courseSectionFactory = $courseSectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Delete Section
     */
    public function execute()
    {
        $error = false;
        $courseSectionModel = $this->courseSectionFactory->create();
        $id = $this->getRequest()->getParam('id');
        try {
            $courseSectionModel->load($id);
            $courseSectionModel->delete();
            $message = __('Section delete successfully.');
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
