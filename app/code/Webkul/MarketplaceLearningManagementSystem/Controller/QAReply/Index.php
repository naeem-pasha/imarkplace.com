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
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
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
     * @var CourseDataProvider
     */
    protected $courseDataProvider;

    /**
     * @param Data $helper
     * @param Context $context
     * @param CourseDataProvider $courseDataProvider
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Data $helper,
        Context $context,
        CourseDataProvider $courseDataProvider,
        JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->courseDataProvider = $courseDataProvider;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Get QA Record Data
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $qaId = $this->getRequest()->getParam('qaid');
        $recordData = $this->courseDataProvider->getQaReply($qaId);
        foreach ($recordData as $index => $data) {
            $recordData[$index]['sname'] = strtoupper($this->helper->getUserSortName($data['replied_by']));
            $recordData[$index]['created_at'] = explode(" ", $recordData[$index]['created_at'])[0];
        }
        $resultJson->setData($recordData);

        return $resultJson;
    }
}
