<?php

namespace AALogics\LMS\Controller\Adminhtml\Content;

use Webkul\MarketplaceLearningManagementSystem\Model\CourseContentFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Save Course Content Data
 */
class Save extends \Magento\Backend\App\Action
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
     * @param \Magento\Backend\App\Action\Context $context
     * @param CourseContentFactory $courseContentFactory
     * @param Validator $formKeyValidator
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CourseContentFactory $courseContentFactory,
        Validator $formKeyValidator,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->courseContentFactory = $courseContentFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Course Content Data
     */

    private function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function execute()
    {
        $ffmpeg = FFMpeg::create();
        $error = false;
        $contentModel = $this->courseContentFactory->create();
        $formData = $this->getRequest()->getParams();
        $formData['import_file'] = json_decode($formData['import_file'], true);
        $resultJson = $this->resultJsonFactory->create();
        if (!empty($formData['back'])) {
            $resultJson->setData(
                [
                    'message' => '',
                    'error' => false,
                ]
            );
        
            return $resultJson;
        }

        if ($this->formKeyValidator->validate($this->getRequest())) {
            try {
                if (!empty($formData['entity_id'])) {
                    $contentModel->load($formData['entity_id']);
                }
                if ($this->getRequest()->getParam('product_id') != null) {
                    $contentModel->setCourseId($this->getRequest()->getParam('product_id'));
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
                    if ($formData['type'] == 1) {
                        if (isset($formData['import_file']['name'])) {
                            $contentModel->setFileName($formData['import_file']['name']);
                        }

                        if (isset($formData['import_file']['file'])) {
                            $contentModel->setFilePath($this->getBaseTmpMediaUrl() .'MarketplaceLearningManagementSystem/'.$formData['import_file']['name']);
                        }
                    } else {
                        if (isset($formData['import_file2'][0]['name'])) {
                            $contentModel->setFileName($formData['import_file2'][0]['name']);
                        }

                        if (isset($formData['import_file2'][0]['file'])) {
                            $contentModel->setFilePath($this->getBaseTmpMediaUrl() .'MarketplaceLearningManagementSystem/'.$formData['import_file2'][0]['name']);
                        }
                    }
                }
                if (isset($formData['description'])) {
                    $contentModel->setDescription($formData['description']);
                }
                if (isset($formData['preview'])) {
                    if ($formData['type'] == 1) {
                        $contentModel->setPreview($formData['preview']);
                    } else {
                        $contentModel->setPreview(0);
                    }
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
        
        return ceil($duration);
    }
}
