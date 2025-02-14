<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_B2BMarketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\B2BMarketplace\Model\Attachment;

class File
{
    /**
     * Filesystem driver
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * File factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * Media directory
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * DownloadProvider constructor
     *
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->fileDriver = $fileDriver;
        $this->fileFactory = $fileFactory;
        $this->mediaDirectory = $filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
    }

    /**
     * Get contents
     *
     * @param \Webkul\B2BMarketplace\Api\Data\QuoteAttachmentInterface $attachment
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function downloadContents(\Webkul\B2BMarketplace\Api\Data\QuoteAttachmentInterface $attachment)
    {
        $fileName = $attachment->getFileName();
        $attachmentPath = $this->mediaDirectory
                ->getAbsolutePath(\Webkul\B2BMarketplace\Model\QuoteManagement::ATTACHMENTS_FOLDER)
            . $attachment->getFilePath();
        $fileSize = isset($this->fileDriver->stat($attachmentPath)['size'])
            ? $this->fileDriver->stat($attachmentPath)['size']
            : 0;

        $this->fileFactory->create(
            $fileName,
            $this->fileDriver->fileGetContents($attachmentPath),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
    }

    /**
     * Get contents
     *
     * @param \Webkul\B2BMarketplace\Api\Data\ItemAttachmentInterface $attachment
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function downloadItemContents(\Webkul\B2BMarketplace\Api\Data\ItemAttachmentInterface $attachment)
    {
        $fileName = $attachment->getFileName();
        $attachmentPath = $this->mediaDirectory
                ->getAbsolutePath(\Webkul\B2BMarketplace\Model\QuoteManagement::ITEM_ATTACHMENTS_FOLDER)
            . $attachment->getFilePath();
        $fileSize = isset($this->fileDriver->stat($attachmentPath)['size'])
            ? $this->fileDriver->stat($attachmentPath)['size']
            : 0;

        $this->fileFactory->create(
            $fileName,
            $this->fileDriver->fileGetContents($attachmentPath),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
    }

    /**
     * Get contents
     *
     * @param \Webkul\B2BMarketplace\Api\Data\AttachmentInterface $attachment
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function downloadAttachmentContents(\Webkul\B2BMarketplace\Api\Data\AttachmentInterface $attachment)
    {
        $fileName = $attachment->getFileName();
        $attachmentPath = $this->mediaDirectory
                ->getAbsolutePath(\Webkul\B2BMarketplace\Model\QuoteManagement::ATTACHMENTS_FOLDER)
            . $attachment->getValue();
        $fileSize = isset($this->fileDriver->stat($attachmentPath)['size'])
            ? $this->fileDriver->stat($attachmentPath)['size']
            : 0;

        $this->fileFactory->create(
            $fileName,
            $this->fileDriver->fileGetContents($attachmentPath),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
    }
}
