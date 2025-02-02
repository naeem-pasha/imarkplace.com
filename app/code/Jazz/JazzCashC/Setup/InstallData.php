<?php
/* File: app/code/Atwix/OrderFlow/Setup/InstallData.php */

namespace Jazz\JazzCash\Setup;

use Exception;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Setup\SalesSetupFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    public function __construct(SalesSetupFactory $salesSetupFactory)
    {
        $this->salesSetupFactory = $salesSetupFactory;
       
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Prepare database for install
         */
        $setup->startSetup();
      

        $data = [];
        $statuses = [
            '000' => __('Transaction Successfully Posted'),
            '121' => __('Transaction has been marked confirmed by Merchant'),
           // '124'  => __('Order is placed and waiting for financials to be received over the counter'),
            '124'  => __('Payment Pending / shipment pending'),
			'200' => __('Transaction approved – Post authorization'),
            '210' => __('Authorization pending'),
            '134'  => __('Transaction Time Out’'),
			'jazzcash_failed' => __('JazzCash Failed'),
            'migs_failure' => __('CARD Failure'),
            'migs_success'  => __('Card Payment Success/ Ready for Shipment'),
			'mw_failed' => __('MWALLET Failure'),
            'mw_success' => __('MWALLET Payment Success/ Ready for Shipment'),
            'otc_failure'  => __('OTC Failure'),
			'otc_success' => __('Voucher Payment Success / Ready for Shipment'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $setup->getConnection()
            ->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);
        
     /****** sales_order_status_state *******/

        $data1 = [];
        $statusesState = [
        		'000' => __('000'),
        		'121' => __('121'),
        		'124'  => __('124'),
				'200' => __('200'),
	            '210' => __('210'),
	            '134'  => __('134'),
				'jazzcash_failed' => __('jazzcash_failed'),
	            'migs_failure' => __('migs_failure'),
	            'migs_success'  => __('migs_success'),
				'mw_failed' => __('mw_failed'),
	            'mw_success' => __('mw_success'),
	            'otc_failure'  => __('otc_failure'),
				'otc_success' => __('otc_success'),
        ];
        foreach ($statusesState as $code => $info) {
        	$data1[] = ['status' => $code, 'state' => $info  ,'is_default' => 1 , 'visible_on_front'=> 1];
        }
        $setup->getConnection()
        ->insertArray($setup->getTable('sales_order_status_state'), ['status', 'state','is_default' ,'visible_on_front' ], $data1);

        /**
         * Prepare database after install
         */
        $setup->endSetup();
    }
}