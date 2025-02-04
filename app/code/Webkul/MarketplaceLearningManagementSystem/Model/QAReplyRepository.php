<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Webkul_MarketplaceLearningManagementSystem
 * @author Webkul
 * @copyright Copyright (c) WebkulSoftware Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 *
 */
namespace Webkul\MarketplaceLearningManagementSystem\Model;

use Webkul\MarketplaceLearningManagementSystem\Api\QAReplyRepositoryInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QAReply\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QAReply;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * QA Reply
 */
class QAReplyRepository implements QAReplyRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var QAReplyFactory
     */
    protected $qaReplyFactory;

    /**
     * @var QAReply
     */
    protected $qaReply;

    /**
     * @var Contents[]
     */
    protected $instancesById = [];

    /**
     * @param QAReply $qaReply
     * @param QAReplyFactory $qaReplyFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        QAReply $qaReply,
        QAReplyFactory $qaReplyFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->qaReply = $qaReply;
        $this->qaReplyFactory = $qaReplyFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $qaReplyData = $this->qaReplyFactory->create();
        $qaReplyData->load($id);
        if (!$qaReplyData->getId()) {
            $this->instancesById[$id] = $qaReplyData;
        }
        $this->instancesById[$id] = $qaReplyData;

        return $this->instancesById[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $collection = $this->collectionFactory->create();
        $collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\QAReplyInterface $qaReply
    ) {
        try {
            $this->qaReply->delete($qaReply);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the qaReply: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
