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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\Content;

use Webkul\MarketplaceLearningManagementSystem\Model\CourseContentFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

/**
 * Delete course content data
 */
class Delete extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * @var CourseContentFactory
     */
    protected $courseContentFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param CourseContentFactory $courseContentFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        CourseContentFactory $courseContentFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->courseContentFactory = $courseContentFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Delete course content data
     */
    public function execute()
    {
        $error = false;
        $contentModel = $this->courseContentFactory->create();
        $id = $this->getRequest()->getParam('id');
        try {
            $contentModel->load($id);
            $contentModel->delete();
            $message = __('Content data delete successfully.');
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
