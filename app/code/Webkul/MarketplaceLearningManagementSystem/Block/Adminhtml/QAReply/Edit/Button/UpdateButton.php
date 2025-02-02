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
namespace Webkul\MarketplaceLearningManagementSystem\Block\Adminhtml\QAReply\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * Save QA Reply Button
 */
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
            'label' => __('Save'),
            'class' => 'action save primary',
            'id' => 'save-qa-reply',
            'on_click' => '',
            'sort_order' => 80,
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'qa_reply_edit_form.qa_reply_edit_form',
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
