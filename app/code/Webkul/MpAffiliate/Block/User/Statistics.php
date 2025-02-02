<?php
/**
 * Webkul MpAffiliate Statistics.
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpAffiliate\Block\User;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Webkul\MpAffiliate\Helper\Data as AffDataHelper;
use Webkul\MpAffiliate\Model\ResourceModel\Clicks\CollectionFactory;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;

class Statistics extends \Webkul\MpAffiliate\Block\User\UserAbstract
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    private $help;
    /**
     * @var Magento\Framework\App\DeploymentConfig
     */
    private $deploymentConfigDate;

    /**
     * Affiliate User statistics graph width
     *
     * @var string
     */
    private $width = '800';
    
    /**
     * Affiliate User statistics graph height
     *
     * @var string
     */
    private $height = '375';

    /**
     * @param Context                $context
     * @param Session                $customerSession,
     * @param AffDataHelper          $affDataHelper,
     * @param CollectionFactory      $collectionFactory,
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AffDataHelper $affDataHelper,
        CollectionFactory $collectionFactory,
        DeploymentConfig $deploymentConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->help=$affDataHelper;
        $this->collectionFactory = $collectionFactory;
        $this->deploymentConfigDate = $deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_INSTALL_DATE);
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $customerSession, $affDataHelper, $customerFactory, $data);
    }

    /**
     * Get Affiliate statistics graph image url
     *
     * @return string
     */
    public function getAffilaiteUserStatisticsGraph()
    {
        $view=$this->getRequest()->getParam('view');
        if ($view=="daily") {
            //daily
            // $params = [
            //     'cht' => 'lc',
            //     'chm' => 'N,000000,0,-1,11',
            //     'chf' => 'bg,s,ffffff',
            //     'chxt' => 'x,y',
            //     'chds' =>'a'
            // ];
            $monthlyClicks = $this->getDailyClicks();
            return $monthlyClicks;
            // $maxClicks = max($monthlyClicks);

            // if ($maxClicks) {
            //     $a = $maxClicks/10;
            //     $axisYArr = [];
            //     for ($i=1; $i<=10; $i++) {
            //         array_push($axisYArr, $a*$i);
            //     }
            //     $axisY = implode('|', $axisYArr);
            // } else {
            //     $axisY = '10|20|30|40|50|60|70|80|90|100';
            // }

            // $params['chxl'] = '0:||'.
            // __('01').'|'.
            // __('02').'|'.
            // __('03').'|'.
            // __('04').'|'.
            // __('05').'|'.
            // __('06').'|'.
            // __('07').'|'.
            // __('08').'|'.
            // __('09').'|'.
            // __('10').'|'.
            // __('11').'|'.
            // __('12').'|'.
            // __('13').'|'.
            // __('14').'|'.
            // __('15').'|'.
            // __('16').'|'.
            // __('17').'|'.
            // __('18').'|'.
            // __('19').'|'.
            // __('20').'|'.
            // __('21').'|'.
            // __('22').'|'.
            // __('23').'|'.
            // __('24').'|'.
            // __('25').'|'.
            // __('26').'|'.
            // __('27').'|'.
            // __('28').'|'.
            // __('29').'|'.
            // __('30').'|'.
            // __('31');
                
            // $minvalue = 0;
            // $maxvalue = $maxClicks;
            // $params['chd'] = 't:'.implode(',', $monthlyClicks);
            // $valueBuffer = [];

            // // seller statistics graph size
            // $params['chs'] = $this->width . 'x' . $this->height;
            // $paramDataEnocded = urlencode(base64_encode(json_encode($params)));
            // $encryptedHashData = $this->getChartEncryptedHashData($paramDataEnocded);
            // $params = ['param_data' => $paramDataEnocded, 'encrypted_data' => $encryptedHashData];
        } else {
            // $params = [
            //     'cht' => 'lc',
            //     'chm' => 'N,000000,0,-1,11',
            //     'chf' => 'bg,s,ffffff',
            //     'chxt' => 'x,y',
            //     'chds' =>'a'
            // ];
            $monthlyClicks = $this->getMonthlyClicks();
            return $monthlyClicks;
            // $maxClicks = max($monthlyClicks);

            // if ($maxClicks) {
            //     $a = $maxClicks/10;
            //     $axisYArr = [];
            //     for ($i=1; $i<=10; $i++) {
            //         array_push($axisYArr, $a*$i);
            //     }
            //     $axisY = implode('|', $axisYArr);
            // } else {
            //     $axisY = '10|20|30|40|50|60|70|80|90|100';
            // }

            // $params['chxl'] = '0:||'.
            // __('January').'|'.
            // __('February').'|'.
            // __('March').'|'.
            // __('April').'|'.
            // __('May').'|'.
            // __('June').'|'.
            // __('July').'|'.
            // __('August').'|'.
            // __('September').'|'.
            // __('October').'|'.
            // __('November').'|'.
            // __('December');
                
            // $minvalue = 0;
            // $maxvalue = $maxClicks;
            // $params['chd'] = 't:'.implode(',', $monthlyClicks);
            // $valueBuffer = [];

            // // seller statistics graph size
            // $params['chs'] = $this->width . 'x' . $this->height;
            // $paramDataEnocded = urlencode(base64_encode(json_encode($params)));
            // $encryptedHashData = $this->getChartEncryptedHashData($paramDataEnocded);
            // $params = ['param_data' => $paramDataEnocded, 'encrypted_data' => $encryptedHashData];
        }

        // return $this->getUrl('*/*/graph', ['_query' => $params, '_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * getMonthlyClicks
     */
    public function getMonthlyClicks()
    {
        $affiUserId = $this->getCustomerSession()->getCustomerId();
        $data=[];
        $curryear = date('Y');
        for ($month=1; $month<=12; $month++) {
            $dateFrom = $curryear."-".$month."-01 00:00:00";
            $dateTo = $curryear."-".$month."-31 23:59:59";
            $data[$month] = $this->collectionFactory->create()
                                    ->addFieldToFilter('aff_customer_id', ['eq' => $affiUserId])
                                    ->addFieldToFilter(
                                        'created_at',
                                        ['datetime' => true,'from' =>  $dateFrom,'to' =>  $dateTo]
                                    )->getSize();
        }
        return $data;
    }

    /**
     * Get Affiliate User Chart Encrypted Hash Data
     *
     * @param string $data
     * @return string
     */
    // public function getChartEncryptedHashData($data)
    // {
    //     return md5($data . $this->deploymentConfigDate);
    // }
    
    public function getDailyClicks()
    {
        $affiUserId = $this->getCustomerSession()->getCustomerId();
        $data=[];
        $curryear = date('Y');
        $month = date('m');
        for ($day=1; $day<=31; $day++) {
            $dateFrom = $curryear."-".$month."-".$day." 00:00:00";
            $dateTo = $curryear."-".$month."-".$day." 23:59:59";
            $data[$day] = $this->collectionFactory->create()
                                    ->addFieldToFilter('aff_customer_id', ['eq' => $affiUserId])
                                    ->addFieldToFilter(
                                        'created_at',
                                        ['datetime' => true,'from' =>  $dateFrom,'to' =>  $dateTo]
                                    )->getSize();
        }
        return $data;
    }
}
