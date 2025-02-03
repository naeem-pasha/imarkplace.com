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

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\QARecord\CollectionFactory;
use Webkul\MarketplaceLearningManagementSystem\Model\QARecord;
use Magento\Framework\Session\SessionManagerInterface;

class QADataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        SessionManagerInterface $session,
        $name,
        $primaryFieldName,
        $requestFieldName,
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
        if ($this->request->getParam('product_id')) {
            $this->session->setcourseId($this->request->getParam('product_id'));
        } elseif ($this->request->getParam('current_product_id')) {
            $this->session->setcourseId($this->request->getParam('current_product_id'));
        }

        $this->getCollection()->addFieldToFilter(
            'course_id',
            ['eq'=> $this->session->getcourseId()]
        );

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }
}
