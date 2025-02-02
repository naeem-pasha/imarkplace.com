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
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseContentFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\CourseDataProvider;

class Index extends Action
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
     * @var CourseContentFactory
     */
    protected $courseContentFactory;

    /**
     * @var CourseDataProvider
     */
    protected $courseDataProvider;

    /**
     * @param Data $helper
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CourseDataProvider $courseDataProvider
     * @param CourseContentFactory $courseContentFactory
     */
    public function __construct(
        Data $helper,
        Context $context,
        JsonFactory $resultJsonFactory,
        CourseDataProvider $courseDataProvider,
        CourseContentFactory $courseContentFactory
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->courseDataProvider = $courseDataProvider;
        $this->courseContentFactory = $courseContentFactory;
        parent::__construct($context);
    }

    /**
     * Get QA Record Data
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $courseId = $this->getRequest()->getParam('cid');
        $recordData = $this->courseDataProvider->getQaRecord($courseId);
        foreach ($recordData as $index => $data) {
            $recordData[$index]['sname'] = strtoupper($this->helper->getUserSortName($data['student_name']));
            $recordData[$index]['created_date'] = explode(" ", $recordData[$index]['created_date'])[0];
            $recordData[$index]['chapter'] = $this->getChapterTitle($recordData[$index]['chapter']);
        }
        $resultJson->setData($recordData);

        return $resultJson;
    }

    /**
     * Get Chapter Title
     *
     * @param int $id
     * @return string
     */
    public function getChapterTitle($id)
    {
        $courseContent = $this->courseContentFactory->create()
                            ->load($id);
        return (empty($courseContent->getTitle()))? '' : $courseContent->getTitle();
    }
}
