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
namespace Webkul\MarketplaceLearningManagementSystem\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Media;
use Magento\Framework\UrlInterface;
use Magento\Backend\Model\UrlInterface as BackendUrl;

class CourseProductAttribute extends AbstractModifier
{

    const QUESTION_ANSWERE = 'question_answere';
    const COURSE_CONTENT = 'course_content';
    const COURSE_CONTENT_SCOPE = 'course.content';
    const CONTAINER_HEADER_NAME = 'container_course_content';

    const BUTTON_ADD = 'button_add';
    const IMPORT_COURSE_CONTENT_GRID_MODAL = 'import_course_content_grid_modal';
    const IMPORT_COURSE_SECTION_GRID_MODAL = 'import_course_section_grid_modal';
    const IMPORT_COURSE_CONTENT_FORM_MODAL = 'import_course_content_form_modal';
    const IMPORT_COURSE_SECTION_FORM_MODAL = 'import_course_section_form_modal';
    const COURSE_CONTENT_FORM = "course_content_form";
    /**#@-*/

    /**#@+
     * Grid values
     */
    const GRID_OPTIONS_NAME = 'contents';

    const FIELD_ENABLE = 'affect_course_content';
    const FIELD_CONTENT_ID_NAME = 'entity_id';
    const FIELD_IS_NEW_ID_NAME = 'is_new';
    const FIELD_CONTENT_FILE = 'file_name';
    const FIELD_FILE_PATH_NAME = 'file_path';
    const FIELD_SUBJECT_ID = 'subject_id';
    const FIELD_IS_REQUIRE_NAME = 'is_require';
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const FIELD_TITLE = 'title';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_TOPICNAME = 'topic_name';
    const FIELD_PREVIEW = 'preview';
    const FIELD_IS_DELETE = 'is_delete';

    const COURSE_CONTENT_LISTING = 'course_content_listing';
    const COURSE_SECTION_LISTING = 'course_section_listing';
    const COURSE_SECTION_FORM = 'course_section_form';

    /**
     * @var LocatorInterface
     */
    protected $locator;
 
    /**
     * @var RequestInterface
     */
    protected $request;
 
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    protected $meta;

    protected $formName;
    protected $dataScopeName;
    protected $dataSourceName;
    protected $associatedListingPrefix;
 
    /**
     * @param LocatorInterface $locator
     * @param RequestInterface $request
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param BackendUrl $backendUrl
     * @param string $formName
     * @param string $dataScopeName
     * @param string $dataSourceName
     * @param string $associatedListingPrefix
     */
    public function __construct(
        LocatorInterface $locator,
        RequestInterface $request,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        BackendUrl $backendUrl,
        $formName,
        $dataScopeName,
        $dataSourceName,
        $associatedListingPrefix = ''
    ) {
        $this->locator = $locator;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->backendUrl = $backendUrl;
        $this->formName = $formName;
        $this->dataScopeName = $dataScopeName;
        $this->dataSourceName = $dataSourceName;
        $this->associatedListingPrefix = $associatedListingPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $attribute = 'course-details';
        if ($this->getProductType() != "course") {
            $path = $this->arrayManager->findPath($attribute, $meta, null, 'children');
            $meta = $this->arrayManager->set(
                $path."/arguments/data/config/visible",
                $meta,
                false
            );
        } else {
            $path = $this->arrayManager->findPath($attribute, $meta, null, 'children');
            $meta = $this->arrayManager->set(
                $path."/arguments/data/config/sortOrder",
                $meta,
                '10'
            );

        }
        $this->meta = $meta;
        if ($this->locator->getProduct()->getTypeId() == 'course' && $this->request->getParam('id') != null) {
            $this->meta = $this->questionAnswerTab($meta);
            $this->courseContentTab();
        }
        return $this->meta;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Course Content Tab
     *
     * @return object
     */
    protected function courseContentTab()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::COURSE_CONTENT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Course Content'),
                                'componentType' => Form\Fieldset::NAME,
                                'dataScope' => static::COURSE_CONTENT_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => '10',
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_HEADER_NAME => $this->getCourseContentGrid(10),
                        static::FIELD_ENABLE => $this->getEnableFieldConfig(20),
                    ]
                ]
            ]
        );

        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::IMPORT_COURSE_CONTENT_GRID_MODAL => $this->getImportOptionsModalConfig()
            ]
        );
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::IMPORT_COURSE_SECTION_GRID_MODAL => $this->getImportSectionModalConfig()
            ]
        );

        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::IMPORT_COURSE_CONTENT_FORM_MODAL => $this->getImportFormModalConfig()
            ]
        );

        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::IMPORT_COURSE_SECTION_FORM_MODAL => $this->getSectionFormModalConfig()
            ]
        );

        return $this;
    }

    /**
     * Get course content grid
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getCourseContentGrid($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::BUTTON_ADD => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'title' => __('Add course content'),
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => 'ns=' . static::FORM_NAME . ', index=options',
                                        'actionName' => 'clearDataProvider'
                                    ],
                                    [
                                        'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                            . static::IMPORT_COURSE_CONTENT_GRID_MODAL,
                                        'actionName' => 'openModal',
                                    ],
                                    [
                                        'targetName' => 'ns=' . static::COURSE_CONTENT_LISTING
                                            . ', index=' . static::COURSE_CONTENT_LISTING,
                                        'actionName' => 'render',
                                    ],
                                ],
                                'sortOrder' => 10,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get Section Form Modal config
     *
     * @return array
     */
    protected function getSectionFormModalConfig()
    {
        $formjs = 'Webkul_MarketplaceLearningManagementSystem/js/form/components/insert-form';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.course_section_data_source',
                        'options' => [
                            'title' => __('New Course Section'),
                        ],
                    ],
                ],
            ],
            'children' => [
                static::COURSE_SECTION_FORM => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => '',
                                'componentType' => 'container',
                                'component' => $formjs,
                                'listingProvider' => 'ns = course_section_listing, index = course_section_listing',
                                'modalProvider' => '${ $.parentName }',
                                'dataScope' => '',
                                'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render_handle',
                                    [
                                        'handle' => 'course_section_import_form',
                                        'buttons' => 1,
                                        'product_id' => $this->request->getParam('id')
                                    ]
                                ),
                                'autoRender' => false,
                                'ns' => 'course_section_form',
                                'actionsContainerClass' => '',
                                'externalProvider' => 'course_section_form.course_section_form_data_source',
                                'toolbarContainer' => '${ $.parentName }',
                                'formSubmitType' => 'ajax',
                            ],
                        ],
                    ]
                ],
            ],
        ];
    }

    /**
     * Get Import Form Modal config
     *
     * @return array
     */
    protected function getImportFormModalConfig()
    {
        $formjs = 'Webkul_MarketplaceLearningManagementSystem/js/form/components/insert-form';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.course_content_data_source',
                        'options' => [
                            'title' => __('New Course Content'),
                        ],
                    ],
                ],
            ],
            'children' => [
                static::COURSE_CONTENT_FORM => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => '',
                                'componentType' => 'container',
                                'component' => $formjs,
                                'listingProvider' => 'ns = course_content_listing, index = course_content_listing',
                                'modalProvider' => '${ $.parentName }',
                                'dataScope' => '',
                                'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render_handle',
                                    [
                                        'handle' => 'course_content_import_form',
                                        'buttons' => 1,
                                        'product_id' => $this->request->getParam('id')
                                    ]
                                ),
                                'autoRender' => false,
                                'ns' => 'course_content_form',
                                'actionsContainerClass' => '',
                                'externalProvider' => 'course_content_form.course_content_form_data_source',
                                'toolbarContainer' => '${ $.parentName }',
                                'formSubmitType' => 'ajax',
                            ],
                        ],
                    ]
                ],
            ],
        ];
    }

    /**
     * Get Import Section Modal config
     *
     * @return array
     */
    protected function getImportSectionModalConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.course_section_data_source',
                        'options' => [
                            'title' => __('Course Sections'),
                            'buttons' => [
                                [
                                    'text' => __('Add new section'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'ns=' . static::FORM_NAME . ', index=options',
                                            'actionName' => 'clearDataProvider'
                                        ],
                                        [
                                            'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                                . static::IMPORT_COURSE_SECTION_FORM_MODAL,
                                            'actionName' => 'openModal',
                                        ],
                                        [
                                            'targetName' => 'ns=' . static::COURSE_SECTION_FORM
                                                . ', index=' . static::COURSE_SECTION_FORM,
                                            'actionName' => 'render',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                static::COURSE_SECTION_LISTING => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => static::COURSE_SECTION_LISTING,
                                'externalProvider' => static::COURSE_SECTION_LISTING . '.'
                                    . static::COURSE_SECTION_LISTING . '_data_source',
                                'selectionsProvider' => static::COURSE_SECTION_LISTING . '.'
                                    . static::COURSE_SECTION_LISTING . '.product_columns.ids',
                                'ns' => static::COURSE_SECTION_LISTING,
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render',
                                    [
                                        'product_id' => $this->request->getParam('id')
                                    ]
                                ),
                                'realTimeLink' => true,
                                'externalFilterMode' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Get Import Options Modal Config
     *
     * @return array
     */
    protected function getImportOptionsModalConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.course_content_data_source',
                        'options' => [
                            'title' => __('Course Content'),
                            'buttons' => [
                                [
                                    'text' => __('Add new content'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'ns=' . static::FORM_NAME . ', index=options',
                                            'actionName' => 'clearDataProvider'
                                        ],
                                        [
                                            'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                                . static::IMPORT_COURSE_CONTENT_FORM_MODAL,
                                            'actionName' => 'openModal',
                                        ],
                                        [
                                            'targetName' => 'ns=' . static::COURSE_CONTENT_FORM
                                                . ', index=' . static::COURSE_CONTENT_FORM,
                                            'actionName' => 'render',
                                        ],
                                    ],
                                ],
                                [
                                    'text' => __('Content Sections'),
                                    'class' => 'action secondary',
                                    'actions' => [
                                        [
                                            'targetName' => 'ns=' . static::FORM_NAME . ', index=options',
                                            'actionName' => 'clearDataProvider'
                                        ],
                                        [
                                            'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                                . static::IMPORT_COURSE_SECTION_GRID_MODAL,
                                            'actionName' => 'openModal',
                                        ],
                                        [
                                            'targetName' => 'ns=' . static::COURSE_SECTION_LISTING
                                                . ', index=' . static::COURSE_SECTION_LISTING,
                                            'actionName' => 'render',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                static::COURSE_CONTENT_LISTING => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => static::COURSE_CONTENT_LISTING,
                                'externalProvider' => static::COURSE_CONTENT_LISTING . '.'
                                    . static::COURSE_CONTENT_LISTING . '_data_source',
                                'selectionsProvider' => static::COURSE_CONTENT_LISTING . '.'
                                    . static::COURSE_CONTENT_LISTING . '.product_columns.ids',
                                'ns' => static::COURSE_CONTENT_LISTING,
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render',
                                    [
                                        'product_id' => $this->request->getParam('id')
                                    ]
                                ),
                                'realTimeLink' => true,
                                'externalFilterMode' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get enable field config
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getEnableFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Field::NAME,
                        'componentType' => Input::NAME,
                        'dataScope' => static::FIELD_ENABLE,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get product type
     *
     * @return null|string
     */
    protected function getProductType()
    {
        return (string)$this->request->getParam('type', $this->locator->getProduct()->getTypeId());
    }

    /**
     * Get Question AnswerTab
     *
     * @param array $meta
     * @return array
     */
    protected function questionAnswerTab($meta)
    {
        $meta[static::QUESTION_ANSWERE] = [
            'children' => [
                'qa_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'qa_listing',
                                'externalProvider' => 'qa_listing.qa_listing_data_source',
                                'selectionsProvider' => 'qa_listing.qa_listing.product_columns.ids',
                                'ns' => 'qa_listing',
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render',
                                    [
                                        'product_id' => $this->request->getParam('id')
                                    ]
                                ),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id'
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Question & Answer'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' => 100
                    ],
                ],
            ],
        ];

        return $meta;
    }
}
