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
namespace Webkul\MarketplaceLearningManagementSystem\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Webkul\MarketplaceLearningManagementSystem\Model\ResourceModel\CourseSection\CollectionFactory;
use Magento\Framework\Session\SessionManagerInterface;

class CourseSection extends AbstractSource
{
    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param SessionManagerInterface $session
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        SessionManagerInterface $session,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
    }

    /**
     * To Options Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Get Section Option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->getAllOptions() as $option) {
            $options[$option['value']] = (string)$option['label'];
        }
        return $options;
    }

    /**
     * Get All Options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $sectionCollection = $this->collectionFactory->create();
        if ($this->session->getProductId()) {
            $sectionCollection->addFieldToFilter('course_id', $this->session->getProductId());
        }
        $this->_options = [
            [
                'label' => __('Select Title'),
                'value' => ''
            ]
        ];
        foreach ($sectionCollection->getData() as $data) {
            array_push(
                $this->_options,
                [
                    'label' => $data['title'],
                    'value' => $data['entity_id']
                ]
            );
        }
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
