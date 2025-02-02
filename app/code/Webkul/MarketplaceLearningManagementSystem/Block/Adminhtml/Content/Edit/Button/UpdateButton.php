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
namespace Webkul\MarketplaceLearningManagementSystem\Block\Adminhtml\Content\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

class UpdateButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Update'),
            'class' => 'action save primary',
            'id' => 'update-course-content',
            'on_click' => '',
            'sort_order' => 80,
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'course_content_edit_form.course_content_edit_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'product_id' => $this->request
                                                    ->getParam('product_id'),
                                        'finder_tab' => 'variation'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],

            ]
        ];
    }
}
