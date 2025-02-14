<?php
/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\MbFrame\Setup\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class Page
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\ResourceModel\Page\Collection $pageCollection,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->pageFactory = $pageFactory;
        $this->pageCollection = $pageCollection;
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    public function export()
    {
        $path = dirname(dirname(__DIR__)) . '/datacms';
        $list = [
            [
                'title',
                'page_layout',
                'meta_keywords',
                'meta_description',
                'identifier',
                'content_heading',
                'content',
                'is_active',
                'sort_order',
                'layout_update_xml',
                'custom_theme',
                'custom_root_template',
                'custom_layout_update_xml',
                'custom_theme_from',
                'custom_theme_to'
            ],
        ];

        $this->pageCollection->addFieldToSelect('*');
        foreach ($this->pageCollection as $page) {
            $data = [];
            foreach ($list[0] as $attribute) {
                $data[] = $page->getData($attribute);
                if ($attribute == 'identifier') {
                    echo 'id: ' . $page->getData($attribute) . '<br/>';
                }
            }
            $list[] = $data;
        }

        $fp = fopen($path . '/pages.csv', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
        echo 'export Page finish' . '<br/><br/>';
    }

    /**
     * @param bool $override
     */
    public function install($override = false)
    {
        //$logger = \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface');
        try {
            $fileName = dirname(dirname(__DIR__)) . '/datacms/pages.csv';
            //$fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                return;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;

                $pageCollection = $this->pageCollectionFactory->create();
                $oldPages = $pageCollection->addFilter('identifier', $row['identifier'])->load();

                if ($override && $oldPages) {
                    foreach ($oldPages as $oldPage) {
                        $oldPage->delete();
                    }
                }

                $this->pageFactory->create()
                    ->load($row['identifier'], 'identifier')
                    ->addData($row)
                    ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->save();
            }

            //$logger->info('Page Imported');
        } catch (\Exception $e) {
            //$logger->critical($e->getMessage());
        }
    }
}
