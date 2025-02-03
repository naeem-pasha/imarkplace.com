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

class MpCourseSectionActions extends Column
{

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
     * {@inheritDoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name]['reply'] = [
                        'callback' => [
                            [
                                'provider' =>
                                    'mp_course_section_listing.mp_course_section_listing.'.
                                    'course_section_modal.course_section_form_loader',
                                'target' => 'destroyInserted',
                            ],
                            [
                                'provider' =>
                                    'mp_course_section_listing.mp_course_section_listing.course_section_modal',
                                'target' => 'openModal',
                            ],
                            [
                                'provider' =>
                                    'mp_course_section_listing.mp_course_section_listing.'.
                                    'course_section_modal.course_section_form_loader',
                                'target' => 'render',
                                'params' => [
                                    'entity_id' => $item['entity_id'],
                                ],
                            ]
                        ],
                        'href' => '#',
                        'label' => __('Edit Section'),
                        'hidden' => false,
                    ];

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'mplms/section/delete',
                            ['id' => $item['entity_id']]
                        ),
                        'label' => __('Delete'),
                        'isAjax' => true,
                        'confirm' => [
                            'title' => __('Delete Section'),
                            'message' => __('Are you sure you want to delete this Section?')
                        ],
                    ];
                }
            }
        }
        
        return $dataSource;
    }
}
