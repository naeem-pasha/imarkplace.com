<?php
namespace AALogics\SupplierCategoryBlock\Setup;
use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
class UpgradeSchema implements UpgradeSchemaInterface
{
  private $eavSetupFactory;
  public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
  {
    $this->eavSetupFactory = $eavSetupFactory;
    $this->eavConfig       = $eavConfig;
  }
  public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    
    // Add Mapping url field
    $eavSetup = $this->eavSetupFactory->create();
    $eavSetup->addAttribute(Customer::ENTITY, 'mapping_url', [
        'type'             => 'text',
        'input'            => 'text',
        'label'            => 'Mapping URL',
        'visible'          => true,
        'required'         => false,
        'system'           => false,
        'group'            => 'General',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
        'visible_on_front' => false,
    ]);
    $customAttribute = $this->eavConfig->getAttribute(Customer::ENTITY,'mapping_url');
    $customAttribute->setData(
      'used_in_forms',
      ['adminhtml_customer']
    );
    $customAttribute->save();
   
  }
}