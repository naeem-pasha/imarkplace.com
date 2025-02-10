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
namespace Webkul\MarketplaceLearningManagementSystem\Ui\DataProvider\Product;

use Magento\Framework\App\Request\Http;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QAReply\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\QAReply;
use Magento\Framework\Session\SessionManagerInterface;

class QAReplyDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Http $request
     * @param SessionManagerInterface $session
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Http $request,
        SessionManagerInterface $session,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if ($this->request->getParam('entity_id')) {
            $this->session->setQaId($this->request->getParam('entity_id'));
        }
        $this->collection = $this->getCollection()->addFieldToFilter('qa_id', $this->session->getQaId());
        
        $arrItems = [
            'totalRecords' => $this->collection->getSize(),
            'items' => [],
        ];

        foreach ($this->collection as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }
        return $arrItems;
    }
}
