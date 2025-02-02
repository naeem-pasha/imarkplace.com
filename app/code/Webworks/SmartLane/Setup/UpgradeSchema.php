<?php
namespace Webworks\SmartLane\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $installer;
    protected $moduleContext;

    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $this->installer = $setup;
        $this->moduleContext = $context;
        $this->createSmartlaneSuggestedCouriersTable();
    }

    /**
     * Create Smartlane courier suggestions table
     * this is only if some one has already installed plugin with previous version
     */
    protected function createSmartlaneSuggestedCouriersTable(){

        $this->installer->startSetup();
        if(version_compare($this->moduleContext->getVersion(), '1.0.1', '<')) {
            $table = $this->installer->getConnection()
                ->newTable($this->installer->getTable('smartlane_suggested_couriers'))
                ->addColumn(
                    'id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true]
                )->addColumn(
                    'courier_slug',
                    \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true]
                )->addColumn(
                    'status',
                    \Magento\Framework\Db\Ddl\Table::TYPE_SMALLINT,
                    ['default' => 1]
                )->addColumn(
                    'created_at',
                    \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT],
                    'Created At'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\Db\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\Db\Ddl\Table::TIMESTAMP_INIT],
                    'Updated At'
                );

            $this->installer->getConnection()->createTable($table);
        }

        $this->installer->endSetup();

    }


}