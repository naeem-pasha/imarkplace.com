<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Block\Account\Dashboard;

class CategoryChart extends \Magento\Framework\View\Element\Template
{
    /**
     * GOOGLE_API_URL Google Api URL.
     */
    const GOOGLE_API_URL = 'http://chart.apis.google.com/chart';

    /**
     * Seller statistics graph width.
     *
     * @var string
     */
    protected $_width = '350';

    /**
     * Seller statistics graph height.
     *
     * @var string
     */
    protected $_height = '169';

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Webkul\Marketplace\Block\Account\Dashboard
     */
    protected $dashboard;

    /**
     * @var \Webkul\Marketplace\Helper\Dashboard\Data
     */
    protected $dashboardHelper;

    /**
     * @param Context                                   $context
     * @param array                                     $data
     * @param \Magento\Customer\Model\Session           $customerSession
     * @param \Webkul\Marketplace\Block\Account\Dashboard $dashboard
     * @param \Webkul\Marketplace\Helper\Dashboard\Data $dashboardHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Block\Account\Dashboard $dashboard,
        \Webkul\Marketplace\Helper\Dashboard\Data $dashboardHelper,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->dashboard = $dashboard;
        $this->dashboardHelper = $dashboardHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get seller statistics graph image url.
     *
     * @return string
     */
    public function getSellerStatisticsGraphUrl()
    {
        $params = [
            'cht' => 'p',
        ];
        $getTopSaleCategories = $this->dashboard->getTopSaleCategories();
        $params['chl'] = implode('|', $getTopSaleCategories['category_arr']);
        $chcoArr = [];
        $count = count($getTopSaleCategories['category_arr']);
        for ($i = 1; $i <= $count; ++$i) {
            array_push($chcoArr, $this->randString());
        }

        $params['chco'] = implode('|', $chcoArr);
        $params['chd'] = 't:'.implode(',', $getTopSaleCategories['percentage_arr']);
        $params['chdl'] = implode('%|', $getTopSaleCategories['percentage_arr']);
        $params['chdl'] = $params['chdl'].'%';

        $valueBuffer = [];

        // seller statistics graph size
        $params['chs'] = $this->_width.'x'.$this->_height;

        // return the encoded graph image url
        $_sellerDashboardHelperData = $this->dashboardHelper;
        $getParamData = urlencode(base64_encode(json_encode($params)));
        $getEncryptedHashData =
        $_sellerDashboardHelperData->getChartEncryptedHashData($getParamData);
        $params = [
            'param_data' => $getParamData,
            'encrypted_data' => $getEncryptedHashData,
        ];

        return $this->getUrl(
            '*/*/dashboard_tunnel',
            ['_query' => $params, '_secure' => $this->getRequest()->isSecure()]
        );
    }

    public function randString(
        $charset = 'ABC0123456789'
    ) {
        $length = 6;
        $str = '';
        $count = strlen($charset);
        while ($length--) {
            $str .= $charset[random_int(0, $count - 1)];
        }

        return $str;
    }
}
