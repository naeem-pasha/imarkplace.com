<?php

namespace Webkul\Odoomarketplaceconnect\Model\ResourceModel;

/**
 * Webkul Odoomarketplaceconnect Transaction
 * @category  Webkul
 * @package   Webkul_Odoomarketplaceconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

use Webkul\Odoomagentoconnect\Helper\Connection;

/**
 * Blog post mysql resource
 */
class Transaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Webkul\Marketplace\Model\Sellertransaction $transactionModel,
        \Webkul\Marketplace\Model\Saleslist $orderModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Connection $connection,
        \Psr\Log\LoggerInterface $logger,
        $resourcePrefix = null
    ) {
        $this->_objectManager = $objectManager;
        $this->_logger = $logger;
        $this->_connection = $connection;
        $this->_orderModel = $orderModel;
        $this->_transactionModel = $transactionModel;
        parent::__construct($context, $resourcePrefix);
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomarketplaceconnect_transaction', 'entity_id');
    }

    public function mappingerp($data)
    {
        $createdBy = 'Odoo';
        if (!isset($data['created_by'])) {
            $data['created_by'] = $createdBy;
        }
        $transactionMap = $this->_objectManager->create('Webkul\Odoomarketplaceconnect\Model\Transaction');
        $transactionMap->setData($data);
        $transactionMap->save();
        return true;
    }

    public function exportSpecificTransaction($mpTransactionId)
    {
        $response = [];
        $this->_connection->updateMpSetting();
        $odooSellerId = 0;
        $odooOrderId = 0;
        $odooId = 0;
        $transactionObj = $this->_transactionModel->load($mpTransactionId);

        $amount = $transactionObj->getTransactionAmount();
        $sellerId = $transactionObj->getSellerId();
        $description = $transactionObj->getCustomNote();
        $transactionCollection = $this->_orderModel->getCollection()
            ->addFieldToFilter('trans_id', ['eq'=>$mpTransactionId]);

        foreach ($transactionCollection as $transaction) {
            $mpOrderId = $transaction->getEntityId();
            $orderCollection = $this->_objectManager->create('Webkul\Odoomarketplaceconnect\Model\Order')->getCollection()
                ->addFieldToFilter('mp_sale_id', ['eq'=>$mpOrderId]);

            foreach ($orderCollection as $order) {
                $orderLineId = $order->getOdooLineId();
                $orderId = $order->getOdooId();
                $magentoOrderId = $order->getMagentoId();
                $odooId = $this->createSellerPayment(
                    $mpTransactionId,
                    $amount,
                    $sellerId,
                    $orderLineId,
                    $description,
                    $orderId
                );

                if ($odooId) {
                    $data = [];
                    $data['odoo_id'] = $odooId;
                    $data['magento_id'] = $mpTransactionId;
                    $data['order_id'] = $magentoOrderId;
                    $data['seller_id'] = $sellerId;
                    $data['created_by'] = $helper::$mageUser;
                    $this->mappingerp($data);
                }
            }
        }
        $response['odoo_id'] = $odooId;
        return $response;
    }

    public function createSellerPayment(
        $mpTransactionId,
        $amount,
        $sellerId,
        $orderLineId,
        $description,
        $orderId
    ) {
        $odooId = 0;
        $helper = $this->_connection;
        $context = $helper->getOdooContext();
        $mapArray = [
            'odooOrderId'=>(int)$orderId,
            'odooOrderLineId'=>(int)$orderLineId,
            'sellerId'=>(int)$sellerId,
            'mpTransactionId'=>(int)$mpTransactionId,
            'amount'=>(double)$amount,
            'description'=>$description,
            'instance_id'=>$context['instance_id'],
        ];
        $resp = $helper->callOdooMethod("mp.transaction.mapping", "createOrderPayment", [$mapArray]);
        if ($resp && $resp[0]) {
            $odooId = $resp[1];
        }
        return $odooId;
    }
}
