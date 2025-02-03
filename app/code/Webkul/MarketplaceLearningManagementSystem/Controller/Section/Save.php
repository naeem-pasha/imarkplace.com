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
namespace Webkul\MarketplaceLearningManagementSystem\Controller\Section;

use Webkul\MarketplaceLearningManagementSystem\Model\CourseSectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Action\Context;


/**
 * Save course section data
 */
class Save extends \Magento\Customer\Controller\AbstractAccount
{

    /**
     * @var SessionManagerInterface
     */
    protected $session;

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
     * @param SessionManagerInterface $session
     * @param CourseSectionFactory $courseSectionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        CourseSectionFactory $courseSectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->courseSectionFactory = $courseSectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Save Course Section Data
     */
    public function execute()
    {
        $error = true;
        $courseSectionModel = $this->courseSectionFactory->create();
        $formData = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();
        $message = __('Section save failed.');
     
        
        
        $productId = $this->session->getProductId(); 
        $sections = $courseSectionModel->getCollection()->addFieldToFilter('course_id', $productId)->getData();  
        $isDuplicateSection =  false; 
        foreach($sections as $section) {
            if($section['title']==$formData['title'] && $section['sort_order']==$formData['sort_order']){
                $isDuplicateSection = true;
                break; 
            }
        }  
        if($isDuplicateSection){
            $resultJson->setData(
                [
                    'message' => "Section name and sort order already exist.",
                    'error' => true,
                ]
            );
            return $resultJson;
        }  

        try {
            if (!empty($formData['entity_id'])) {
                $courseSectionModel->load($formData['entity_id']);
            }
            if ($this->session->getProductId() != null && empty($formData['entity_id'])) {
                $courseSectionModel->setCourseId($this->session->getProductId());
            }           
            if (!empty($formData['sort_order'])) {
                $courseSectionModel->setSortOrder($formData['sort_order']);
            }
            if (!empty($formData['title'])) {
                $courseSectionModel->setTitle($formData['title']);
                $courseSectionModel->save();
                $message = __('Section save successfully.');
                $error = false;
            }
            
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
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
