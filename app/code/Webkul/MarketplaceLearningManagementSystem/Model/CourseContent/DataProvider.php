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
namespace Webkul\MarketplaceLearningManagementSystem\Model\CourseContent;

use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseContent\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $content) {
            $this->_loadedData[$content->getId()] = $content->getData();
            if ($content->getType() == 1) {
                $info = [];
                $info['name'] = $content->getFileName();
                $info['url'] = $content->getFilePath();
                $m['import_file'] = json_encode($info);
            } else {
                $m['import_file2'][0]['name'] = $content->getFileName();
                $m['import_file2'][0]['url'] = $content->getFilePath();
            }
            $fullData = $this->_loadedData;
            $this->_loadedData[$content->getId()] = $this->mergeArray($fullData[$content->getId()], $m);
        }
        
        return $this->_loadedData;
    }

    /**
     * Merge Array
     *
     * @param array $firstArray
     * @param array $secondArray
     * @return array
     */
    protected function mergeArray($firstArray, $secondArray)
    {
        return array_merge($firstArray, $secondArray);
    }
}
