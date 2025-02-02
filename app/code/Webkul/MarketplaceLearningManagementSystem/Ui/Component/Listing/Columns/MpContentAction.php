<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceLearningManagementSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceLearningManagementSystem\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class MpContentAction extends Column
{
    const COURSE_CONTENT_DELETE = 'mplms/content/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name]['edit'] = [
                        'callback' => [
                            [
                                'provider' =>
                                    'mp_course_content_listing.mp_course_content_listing.'.
                                    'course_content_update_modal.update_course_content_form_loader',
                                'target' => 'destroyInserted',
                            ],
                            [
                                'provider' =>
                                    'mp_course_content_listing.mp_course_content_listing.course_content_update_modal',
                                'target' => 'openModal',
                            ],
                            [
                                'provider' =>
                                    'mp_course_content_listing.mp_course_content_listing.'.
                                    'course_content_update_modal.update_course_content_form_loader',
                                'target' => 'render',
                                'params' => [
                                    'entity_id' => $item['entity_id'],
                                ],
                            ]
                        ],
                        'href' => '#',
                        'label' => __('Edit Content'),
                        'hidden' => false,
                    ];

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::COURSE_CONTENT_DELETE,
                            ['id' => $item['entity_id']]
                        ),
                        'label' => __('Delete'),
                        'isAjax' => true,
                        'confirm' => [
                            'title' => __('Delete content'),
                            'message' => __('Are you sure you want to delete the content?')
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
