<?php
namespace AALogics\Vendorsearch\Helper;
use Magento\Search\Model\Query as SearchQuery;
use Magento\Search\Model\QueryFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
    }


    public function getVendorSearchUrl($query = null)
    {
        return $this->_getUrl(
            'searchby/vendor/',
            ['_query' => [QueryFactory::QUERY_VAR_NAME => $query], '_secure' => $this->_request->isSecure()]
        );
    }
}