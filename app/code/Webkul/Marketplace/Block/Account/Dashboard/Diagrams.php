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

use Webkul\Marketplace\Model\SaleslistFactory;

class Diagrams extends \Magento\Framework\View\Element\Template
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
    protected $_width = '800';

    /**
     * Seller statistics graph height.
     *
     * @var string
     */
    protected $_height = '375';

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var SaleslistFactory
     */
    protected $saleslistFactory;

    /**
     * @var \Webkul\Marketplace\Helper\Dashboard\Data
     */
    protected $dashboardHelper;

    /**
     * @param Context                                   $context
     * @param array                                     $data
     * @param \Magento\Customer\Model\Session           $customerSession
     * @param SaleslistFactory                          $saleslistFactory
     * @param \Webkul\Marketplace\Helper\Dashboard\Data $dashboardHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        SaleslistFactory $saleslistFactory,
        \Webkul\Marketplace\Helper\Dashboard\Data $dashboardHelper,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->saleslistFactory = $saleslistFactory;
        $this->dashboardHelper = $dashboardHelper;
        parent::__construct($context, $data);
    }

    public function getSale($dateType = 'year')
    {
        $sellerId = $this->_customerSession->getCustomerId();
        $data = [];
        if ($dateType == 'year') {
            $data = $this->getYearlySale($sellerId);
        } elseif ($dateType == 'month') {
            $data = $this->getMonthlySale($sellerId);
        } elseif ($dateType == 'week') {
            $data = $this->getWeeklySale($sellerId);
        } elseif ($dateType == 'day') {
            $data = $this->getDailySale($sellerId);
        }

        return $data;
    }

    public function getYearlySale($sellerId)
    {
        $data = [];
        $data['values'] = [];
        $data['chxl'] = '0:|';
        $curryear = date('Y');
        $currMonth = date('m');
        $monthsArr = [
            '',
            __('January'),
            __('February'),
            __('March'),
            __('April'),
            __('May'),
            __('June'),
            __('July'),
            __('August'),
            __('September'),
            __('October'),
            __('November'),
            __('December'),
        ];
        for ($i = 1; $i <= $currMonth; ++$i) {
            $date1 = $curryear.'-'.$i.'-01 00:00:00';
            $date2 = $curryear.'-'.$i.'-31 23:59:59';
            $collection = $this->saleslistFactory->create()
                          ->getCollection()
                          ->addFieldToFilter(
                              'main_table.seller_id',
                              ['eq' => $sellerId]
                          )
                          ->addFieldToFilter(
                              'main_table.order_id',
                              ['neq' => 0]
                          )->addFieldToFilter(
                              'main_table.created_at',
                              ['datetime' => true, 'from' => $date1, 'to' => $date2]
                          )->getPricebyorderData();
            $temp = 0;
            foreach ($collection as $record) {
                // calculate order actual_seller_amount in base currency
                $appliedCouponAmount = $record['applied_coupon_amount']*1;
                $shippingAmount = $record['shipping_charges']*1;
                $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                $totalshipping = $shippingAmount - $refundedShippingAmount;
                if ($record['tax_to_seller']) {
                    $vendorTaxAmount = $record['total_tax']*1;
                } else {
                    $vendorTaxAmount = 0;
                }
                if ($record['actual_seller_amount'] * 1) {
                    $taxShippingTotal = $vendorTaxAmount + $totalshipping - $appliedCouponAmount;
                    $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                } else {
                    if ($totalshipping * 1) {
                        $temp += $totalshipping - $appliedCouponAmount;
                    }
                }
            }
            $price = $temp;
            $data['values'][$i] = $price;
            if ($i != $currMonth) {
                $data['chxl'] = $data['chxl'].$monthsArr[$i].'|';
            } else {
                $data['chxl'] = $data['chxl'].$monthsArr[$i];
            }
        }

        return $data;
    }

    public function getMonthlySale($sellerId)
    {
        $data = [];
        $data['values'] = [];
        $data['chxl'] = '0:|';
        $curryear = date('Y');
        $currMonth = date('m');
        $currDays = date('d');
        for ($i = 1; $i <= $currDays; ++$i) {
            $date1 = $curryear.'-'.$currMonth.'-'.$i.' 00:00:00';
            $date2 = $curryear.'-'.$currMonth.'-'.$i.' 23:59:59';
            $collection = $this->saleslistFactory->create()
                          ->getCollection()
                          ->addFieldToFilter(
                              'main_table.seller_id',
                              ['eq' => $sellerId]
                          )
                          ->addFieldToFilter(
                              'main_table.order_id',
                              ['neq' => 0]
                          )->addFieldToFilter(
                              'main_table.created_at',
                              ['datetime' => true, 'from' => $date1, 'to' => $date2]
                          )->getPricebyorderData();
            $temp = 0;
            foreach ($collection as $record) {
                // calculate order actual_seller_amount in base currency
                $appliedCouponAmount = $record['applied_coupon_amount']*1;
                $shippingAmount = $record['shipping_charges']*1;
                $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                $totalshipping = $shippingAmount - $refundedShippingAmount;
                if ($record['tax_to_seller']) {
                    $vendorTaxAmount = $record['total_tax']*1;
                } else {
                    $vendorTaxAmount = 0;
                }
                if ($record['actual_seller_amount'] * 1) {
                    $taxShippingTotal = $vendorTaxAmount + $totalshipping - $appliedCouponAmount;
                    $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                } else {
                    if ($totalshipping * 1) {
                        $temp += $totalshipping - $appliedCouponAmount;
                    }
                }
            }
            $price = $temp;
            if ($price * 1 && $i != $currDays) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$currMonth.'/'.$i.'/'.$curryear.'|';
            } elseif ($i < 5 && $price * 1 == 0 && $i != $currDays) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$currMonth.'/'.$i.'/'.$curryear.'|';
            }
            if ($i == $currDays) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$currMonth.'/'.$i.'/'.$curryear;
            }
        }

        return $data;
    }

    public function getWeeklySale($sellerId)
    {
        $data = [];
        $data['values'] = [];
        $data['chxl'] = '0:|';
        $curryear = date('Y');
        $currMonth = date('m');
        $currDays = date('d');
        $currWeekDay = date('N');
        $currWeekStartDay = $currDays - $currWeekDay;
        $currWeekEndDay = $currWeekStartDay + 7;
        $currentDayOfMonth=date('j');
        if ($currWeekEndDay>$currentDayOfMonth) {
            $currWeekEndDay = $currentDayOfMonth;
        }
        for ($i = $currWeekStartDay + 1; $i <= $currWeekEndDay; ++$i) {
            $date1 = $curryear.'-'.$currMonth.'-'.$i.' 00:00:00';
            $date2 = $curryear.'-'.$currMonth.'-'.$i.' 23:59:59';
            $collection = $this->saleslistFactory->create()
                          ->getCollection()
                          ->addFieldToFilter(
                              'main_table.seller_id',
                              ['eq' => $sellerId]
                          )
                          ->addFieldToFilter(
                              'main_table.order_id',
                              ['neq' => 0]
                          )->addFieldToFilter(
                              'main_table.created_at',
                              ['datetime' => true, 'from' => $date1, 'to' => $date2]
                          )->getPricebyorderData();
            $temp = 0;
            foreach ($collection as $record) {
                // calculate order actual_seller_amount in base currency
                $appliedCouponAmount = $record['applied_coupon_amount']*1;
                $shippingAmount = $record['shipping_charges']*1;
                $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                $totalshipping = $shippingAmount - $refundedShippingAmount;
                if ($record['tax_to_seller']) {
                    $vendorTaxAmount = $record['total_tax']*1;
                } else {
                    $vendorTaxAmount = 0;
                }
                if ($record['actual_seller_amount'] * 1) {
                    $taxShippingTotal = $vendorTaxAmount + $totalshipping - $appliedCouponAmount;
                    $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                } else {
                    if ($totalshipping * 1) {
                        $temp += $totalshipping - $appliedCouponAmount;
                    }
                }
            }
            $price = $temp;
            if ($i != $currWeekEndDay) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$currMonth.'/'.$i.'/'.$curryear.'|';
            }
            if ($i == $currWeekEndDay) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$currMonth.'/'.$i.'/'.$curryear;
            }
        }

        return $data;
    }

    public function getDailySale($sellerId)
    {
        $data = [];
        $data['values'] = [];
        $data['chxl'] = '0:|';
        $curryear = date('Y');
        $currMonth = date('m');
        $currDays = date('d');
        $currTime = date('G');
        $arr = [];
        for ($i = 0; $i <= 23; ++$i) {
            $time = $i;
            if ($i < 10) {
                $time  = '0'.$i;
            }
            $date1 = $curryear.'-'.$currMonth.'-'.$currDays.' '.$time.':00:00';
            $date2 = $curryear.'-'.$currMonth.'-'.$currDays.' '.$time.':59:59';
            $collection = $this->saleslistFactory->create()
                          ->getCollection()
                          ->addFieldToFilter(
                              'main_table.seller_id',
                              ['eq' => $sellerId]
                          )
                          ->addFieldToFilter(
                              'main_table.order_id',
                              ['neq' => 0]
                          )->addFieldToFilter(
                              'main_table.created_at',
                              ['datetime' => true, 'from' => $date1, 'to' => $date2]
                          )->getPricebyorderData();
            $temp = 0;
            foreach ($collection as $record) {
                // calculate order actual_seller_amount in base currency
                $appliedCouponAmount = $record['applied_coupon_amount']*1;
                $shippingAmount = $record['shipping_charges']*1;
                $refundedShippingAmount = $record['refunded_shipping_charges']*1;
                $totalshipping = $shippingAmount - $refundedShippingAmount;
                if ($record['tax_to_seller']) {
                    $vendorTaxAmount = $record['total_tax']*1;
                } else {
                    $vendorTaxAmount = 0;
                }
                if ($record['actual_seller_amount'] * 1) {
                    $taxShippingTotal = $vendorTaxAmount + $totalshipping - $appliedCouponAmount;
                    $temp += $record['actual_seller_amount'] + $taxShippingTotal;
                } else {
                    if ($totalshipping * 1) {
                        $temp += $totalshipping - $appliedCouponAmount;
                    }
                }
            }
            $price = $temp;
            if ($i != 23) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$i.'|';
            }
            if ($i == 23) {
                $data['values'][$i] = $price;
                $data['chxl'] = $data['chxl'].$i;
            }
        }
        $newdata['values'] = [];
        if ($currTime >= 2) {
            $arr[0] = '2:00 AM';
            $newdata['values'][0] = $data['values'][0]+$data['values'][1]+$data['values'][2];
        }
        if ($currTime >= 5) {
            $arr[1] = '5:00 AM';
            $newdata['values'][1] = $data['values'][3]+$data['values'][4]+$data['values'][5];
        }
        if ($currTime >= 8) {
            $arr[2] = '8:00 AM';
            $newdata['values'][2] = $data['values'][6]+$data['values'][7]+$data['values'][8];
        }
        if ($currTime >= 11) {
            $arr[3] = '11:00 AM';
            $newdata['values'][3] = $data['values'][9]+$data['values'][10]+$data['values'][11];
        }
        if ($currTime >= 14) {
            $arr[4] = '2:00 PM';
            $newdata['values'][4] = $data['values'][12]+$data['values'][13]+$data['values'][14];
        }
        if ($currTime >= 17) {
            $arr[5] = '5:00 PM';
            $newdata['values'][5] = $data['values'][15]+$data['values'][16]+$data['values'][17];
        }
        if ($currTime >= 20) {
            $arr[6] = '8:00 PM';
            $newdata['values'][6] = $data['values'][18]+$data['values'][19]+$data['values'][20];
        }
        if ($currTime >= 23) {
            $arr[7] = '11:00 PM';
            $newdata['values'][7] = $data['values'][21]+$data['values'][22]+$data['values'][23];
        }
        unset($data['values']);

        $data['values'] = $newdata['values'];

        $data['arr'] = $arr;

        return $data;
    }

    /**
     * Get seller statistics graph image url.
     *
     * @return string
     */
    public function getSellerStatisticsGraphUrl($dateType)
    {
        $params = [
            'cht' => 'bvs',
            'chm' => 'N,000000,0,-1,11',
            'chf' => 'bg,s,ffffff',
            'chxt' => 'x,y',
            'chds' => 'a',
            'chbh' => '55',
            'chco' => 'ef672f',
        ];
        $getData = $this->getSale($dateType);
        $getSale = $getData['values'];

        if (isset($getData['arr'])) {
            $arr = $getData['arr'];
            $totalChb = count($arr);
            $indexid = 0;
            $tmpstring = implode('|', $arr);
            $valueBuffer[] = $indexid.':|'.$tmpstring;
            $valueBuffer = implode('|', $valueBuffer);
            $params['chxl'] = $valueBuffer;
        } else {
            $params['chxl'] = $getData['chxl'];
        }

        if (count($getSale)) {
            $totalSale = max($getSale);
        } else {
            $totalSale = 0;
        }

        if ($totalSale) {
            $countMonths = count($getSale)+1;
            if ($countMonths > 7) {
                $totalChb = (int) (800 / $countMonths);
                $params['chbh'] = $totalChb;
            } else {
                $params['chbh'] = 100;
            }
            $a = $totalSale / 10;
            $axisYArr = [];
            for ($i = 1; $i <= 10; ++$i) {
                array_push($axisYArr, $a * $i);
            }
            $axisY = implode('|', $axisYArr);
        } else {
            $axisY = '10|20|30|40|50|60|70|80|90|100';
        }

        $minvalue = 0;
        $maxvalue = $totalSale;

        $params['chd'] = 't:'.implode(',', $getSale);
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
}
