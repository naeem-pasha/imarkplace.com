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
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * Save course section data
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * @var Validator
     */
    protected $formKeyValidator;

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
     * @param Validator $formKeyValidator
     * @param SessionManagerInterface $session
     * @param CourseSectionFactory $courseSectionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        SessionManagerInterface $session,
        CourseSectionFactory $courseSectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->courseSectionFactory = $courseSectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Save Course Section Data
     */
    public function execute()
    {
        $error = false;
        $courseSectionModel = $this->courseSectionFactory->create();
        $formData = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();

        $productId = $this->session->getProductId(); 
        $sections = $courseSectionModel->getCollection()->addFieldToFilter('course_id', $productId)->getData();  
        
        $isDuplicateSection =  false; 
        foreach($sections as $section) {
            if(trim($section['title']) == trim($formData['title']) && trim($section['sort_order']) == trim($formData['sort_order'])){
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

        if ($this->formKeyValidator->validate($this->getRequest())) {
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
                } else {
                    $message = __('Section save failed.');
                    $error = true;
                }
                
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
