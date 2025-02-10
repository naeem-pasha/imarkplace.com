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
use Magento\Framework\Session\SessionManagerInterface;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * Save Course Content Data
 */
class Save extends \Magento\Customer\Controller\AbstractAccount
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
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param CourseContentFactory $courseContentFactory
     * @param SessionManagerInterface $session
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CourseContentFactory $courseContentFactory,
        SessionManagerInterface $session,
        JsonFactory $resultJsonFactory,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->courseContentFactory = $courseContentFactory;
        $this->session = $session;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Save Course Content Data
     */
    public function execute()
    {
        $ffmpeg = FFMpeg::create();
        $error = false;
        $contentModel = $this->courseContentFactory->create();
        $formData = $this->getRequest()->getParams();

        $formData['import_file'] = json_decode($formData['import_file'], true);
        $resultJson = $this->resultJsonFactory->create();
        try {
            if (!empty($formData['entity_id'])) {
                $contentModel->load($formData['entity_id']);
            }
            if ($this->session->getProductId() != null) {
                $contentModel->setCourseId($this->session->getProductId());
            }
            if (isset($formData['title'])) {
                
                $contentModel->setTitle($formData['title']);
            }
            if (isset($formData['type'])) {
                $contentModel->setType($formData['type']);
                if ($formData['type'] == '1' &&
                    array_key_exists('file', $formData['import_file'])
                ) {
                    $contentModel->setDuration($this->getDuration($formData));
                }
                if (isset($formData['import_file']['name'])) {
                    $contentModel->setFileName($formData['import_file']['name']);
                }

                if (isset($formData['import_file']['file'])) {
                    $contentModel->setFilePath($formData['import_file']['file']);
                }
            }
            if (isset($formData['description'])) {
                $contentModel->setDescription($formData['description']);
            }
            if (isset($formData['preview'])) {
                $contentModel->setPreview($formData['preview']);
            }
            if (isset($formData['section'])) {
                $contentModel->setSection($formData['section']);
            }
            
            $contentModel->save();
            $message = __('Content data save successfully.');
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

    /**
     * Get Duration
     *
     * @param array $formData
     * @return string
     */
    protected function getDuration($formData)
    {
        $ffprobe = FFProbe::create();
        $duration = $ffprobe->format($formData['import_file']['url'])->get('duration');
        
        return $duration;
    }
}
