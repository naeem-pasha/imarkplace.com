<?php

namespace AALogics\BlogCategoryWidget\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\DB\Ddl\TriggerFactory
     */
    protected $triggerFactory;

    public function __construct(
        \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
    )
    {
        $this->triggerFactory = $triggerFactory;
    }


    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
       $this->create($setup, $context);
       $this->update($setup, $context);
       $this->blog_store($setup, $context);
    //    $this->delete($setup, $context);

    }
    
    public function create($setup, $context)
    {
        //Your code for upgrade data base
        $installer = $setup;
        $installer->startSetup();

        $triggerName = 'trg_blog_category_create';
        $event = 'INSERT';

        /** @var \Magento\Framework\DB\Ddl\Trigger $trigger */
        $trigger = $this->triggerFactory->create()
            ->setName($triggerName)
            ->setTime(\Magento\Framework\DB\Ddl\Trigger::TIME_AFTER)
            ->setEvent($event)
            ->setTable($setup->getTable('catalog_category_entity_varchar'));

        $triggerSql = "INSERT INTO `magefan_blog_category_store` (`category_id`,`store_id`) VALUES (NEW.`category_id`,0);";
        $triggerSql = "IF (NEW.`attribute_id` = 45) THEN INSERT INTO `magefan_blog_category` (`title`,`identifier`) VALUES (NEW.`value`, REPLACE(NEW.`value`, ' ', '') ); END IF;";           
        $trigger->addStatement($triggerSql);
        
        // $trigger->addStatement($this->buildStatement($event));

        $setup->getConnection()->dropTrigger($trigger->getName());
        $setup->getConnection()->createTrigger($trigger);

        $installer->endSetup();

    }

    public function update(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        //Your code for upgrade data base
        $installer = $setup;
        $installer->startSetup();

        $triggerName = 'trg_blog_category_update';
        $event = 'UPDATE';

        /** @var \Magento\Framework\DB\Ddl\Trigger $trigger */
        $trigger = $this->triggerFactory->create()
            ->setName($triggerName)
            ->setTime(\Magento\Framework\DB\Ddl\Trigger::TIME_AFTER)
            ->setEvent($event)
            ->setTable($setup->getTable('catalog_category_entity_varchar'));

        $triggerSql = "IF (NEW.`attribute_id` = 45) THEN UPDATE `magefan_blog_category` SET title = NEW.value, identifier = REPLACE(NEW.`value`, ' ', '') WHERE title = OLD.value ; END IF;";
        $trigger->addStatement($triggerSql);

        $setup->getConnection()->dropTrigger($trigger->getName());
        $setup->getConnection()->createTrigger($trigger);

        $installer->endSetup();

    }

    public function delete(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        //Your code for upgrade data base
        $installer = $setup;
        $installer->startSetup();

        $triggerName = 'trg_blog_category_delete';
        $event = 'DELETE';

        /** @var \Magento\Framework\DB\Ddl\Trigger $trigger */
        $trigger = $this->triggerFactory->create()
            ->setName($triggerName)
            ->setTime(\Magento\Framework\DB\Ddl\Trigger::TIME_BEFORE)
            ->setEvent($event)
            ->setTable($setup->getTable('catalog_category_entity_varchar'));

        $triggerSql = "DELETE FROM `magefan_blog_category` WHERE `title` = OLD.`value`";
        $trigger->addStatement($triggerSql);

        $setup->getConnection()->dropTrigger($trigger->getName());
        $setup->getConnection()->createTrigger($trigger);

        $installer->endSetup();

    }

    public function blog_store($setup, $context)
    {
        //Your code for upgrade data base
        $installer = $setup;
        $installer->startSetup();

        $triggerName = 'trg_blog_category_store';
        $event = 'INSERT';

        /** @var \Magento\Framework\DB\Ddl\Trigger $trigger */
        $trigger = $this->triggerFactory->create()
            ->setName($triggerName)
            ->setTime(\Magento\Framework\DB\Ddl\Trigger::TIME_AFTER)
            ->setEvent($event)
            ->setTable($setup->getTable('magefan_blog_category'));

        $triggerSql = "INSERT INTO `magefan_blog_category_store` (`category_id`,`store_id`) VALUES (NEW.`category_id`,0);";
        $trigger->addStatement($triggerSql);
        
        // $trigger->addStatement($this->buildStatement($event));

        $setup->getConnection()->dropTrigger($trigger->getName());
        $setup->getConnection()->createTrigger($trigger);

        $installer->endSetup();

    }

}