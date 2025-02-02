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

use Webkul\MarketplaceLearningManagementSystem\Api\QARecordRepositoryInterface;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QARecord\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QARecord;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * QA Record
 */
class QARecordRepository implements QARecordRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var QARecordFactory
     */
    protected $qaRecordFactory;

    /**
     * @var QARecord
     */
    protected $qaRecord;

    /**
     * @var qaRecord[]
     */
    protected $instancesById = [];

    /**
     * @param QARecord $qaRecord
     * @param QARecordFactory $qaRecordFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        QARecord $qaRecord,
        QARecordFactory $qaRecordFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->qaRecord = $qaRecord;
        $this->qaRecordFactory = $qaRecordFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $qaRecordData = $this->qaRecordFactory->create();
        $qaRecordData->load($id);
        if (!$qaRecordData->getId()) {
            $this->instancesById[$id] = $qaRecordData;
        }
        $this->instancesById[$id] = $qaRecordData;

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
        \Webkul\MarketplaceLearningManagementSystem\Api\Data\QARecordInterface $qaRecord
    ) {
        try {
            $this->qaRecord->delete($qaRecord);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the qaRecord: %1',
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
