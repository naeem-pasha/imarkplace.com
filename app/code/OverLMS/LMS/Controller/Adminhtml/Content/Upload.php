<?php

namespace OverLMS\LMS\Controller\Adminhtml\Content;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends Action
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param Context $context
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'importContent']);
            $uploader->setAllowRenameFiles(true);
            
            $uploader->setFilesDispersion(false);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            $root = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);
            $result = $uploader->save($mediaDirectory->getAbsolutePath('MarketplaceLearningManagementSystem'));
            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     * Get base temporary media URL
     *
     * @return string
     */
    private function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get temporary media URL
     *
     * @param string $file
     * @return string
     */
    protected function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() .'MarketplaceLearningManagementSystem/'. $this->_prepareFile($file);
    }

    /**
     * Return file after escaping characters.
     *
     * @param string $file
     * @return string
     */
    private function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
