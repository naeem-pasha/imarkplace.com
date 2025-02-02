<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpAffiliate
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpAffiliate\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Webkul\Marketplace\Model\ControllersRepository;

class UpgradeData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @var ControllersRepository
     */
    private $controllersRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ControllersRepository $controllersRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ControllersRepository $controllersRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->controllersRepository = $controllersRepository;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $data = [];
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/affiliaterequest')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/affiliaterequest',
                'label' => 'Affiliate Request',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/affiliateconfiguration')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/affiliateconfiguration',
                'label' => 'Affiliate Configuration',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/mailtoaffiliate')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/mailtoaffiliate',
                'label' => 'Mail to Affiliate',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/textbanner')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/textbanner',
                'label' => 'Affiliate Text Banner',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/summary')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/summary',
                'label' => 'Affiliate Summary',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/payment')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/payment',
                'label' => 'Affiliate Payment',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (!$this->controllersRepository->getByPath('mpaffiliate/marketplace/categorycommission')->getSize()) {
            $data[] = [
                'module_name' => 'Webkul_MpAffiliate',
                'controller_path' => 'mpaffiliate/marketplace/categorycommission',
                'label' => 'Category Commission',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }

        $connection->insertMultiple($this->moduleDataSetup->getTable('marketplace_controller_list'), $data);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
