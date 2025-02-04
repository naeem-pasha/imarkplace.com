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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\Course;

use Magento\Framework\App\Action\Context;
use Webkul\MarketplaceLearningManagementSystem\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Magento\Customer\Controller\AbstractAccount;

/**
 * Course View Page controller
 */
class View extends AbstractAccount
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var UrlInterface
     */
    protected $url;

    protected $messageManager;

    /**
     * @param Data $helper
     * @param UrlInterface $url
     * @param Context $context
     */
    public function __construct(
        Data $helper,
        UrlInterface $url,
        Context $context
    ) {
        $this->helper = $helper;
        $this->url = $url;
        $this->messageManager = $context->getMessageManager();  
        parent::__construct($context);
    }

    /**
     * Validate request
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $id = $this->getRequest()->getParam('id');
        $cid = $this->getRequest()->getParam('cid');

         
        $isValidCourse = $this->helper->isValidCourse();
        if (!$isValidCourse) {   
            $this->messageManager->addSuccess("Course not listed in coures list.");  
            $url =  $this->helper->getBaseUrl()."mplms/mycourse/list/";
            $resultRedirect->setUrl($url);
            return $resultRedirect; 
        }
        $isDesShow  = $this->helper->getFieldValue('display_settings', 'short_description');
        $courseData = $this->helper->courseContentData();

        $error = true;
        if (!empty($courseData)) {
            foreach ($courseData as $key => $sectionData) {
                foreach ($sectionData['content'] as $key2 => $data) {

                    if ($isDesShow=="0") {
                        $courseData[$key]['content'][$key2]['description'] = "";
                    }

                    if ($data['content_id'] == $cid) {
                        $error = false;
                        break;
                    }

                }
            }
        }

        if ($error) {
            $norouteUrl = $this->url->getUrl('noroute');
            $resultRedirect->setUrl($norouteUrl);
            return $resultRedirect;
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
