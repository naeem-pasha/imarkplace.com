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
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseSection\CollectionFactory;
use Magento\Framework\Session\SessionManagerInterface;

class CourseSectionDataProvider extends AbstractDataProvider
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
        if ($this->request->getParam('id')) {
            $this->session->setProductId($this->request->getParam('id'));
        } elseif ($this->request->getParam('product_id')) {
            $this->session->setProductId($this->request->getParam('product_id'));
        }
        $this->collection = $this->getCollection()->addFieldToFilter('course_id', $this->session->getProductId());
        
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
