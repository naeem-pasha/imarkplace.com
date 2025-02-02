<?php

namespace Webworks\SmartLane\Ui\Component\MassAction\Courier;

use Magento\Framework\UrlInterface;
use Webworks\SmartLane\Helper\Data;
use Zend\Stdlib\JsonSerializable;
//use Webworks\SmartLane\Model\ResourceModel\Badge\CollectionFactory;

/**
 * Class Options
 */
class Assignoptions implements JsonSerializable
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];
    protected $smartlaneSuggestedCouriersFactoryFactory;
    protected $smartlaneSuggestedCouriersResource;
    protected $smartlaneHelper;


    /**
     * Assignoptions constructor.
     * @param \Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers\CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers\CollectionFactory $collectionFactory,
        \Webworks\SmartLane\Model\SmartlaneSuggestedCouriersFactory $smartlaneSuggestedCouriersFactoryFactory,
        \Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers $smartlaneSuggestedCouriersResource,
        UrlInterface $urlBuilder,
        Data $smartlaneHelper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->smartlaneSuggestedCouriersFactoryFactory = $smartlaneSuggestedCouriersFactoryFactory;
        $this->smartlaneSuggestedCouriersResource = $smartlaneSuggestedCouriersResource;
        $this->smartlaneHelper = $smartlaneHelper;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {

        $i=0;
        $defaultOption =[
            [
                "courier_slug" => Null,
                "action_label" => "Book Now"
            ]
        ];
        $optionsList = [];
        if ($this->options === null) {
            // get the massaction data from the database table
            $collection = $this->collectionFactory->create();

            $couriers = $collection->addFieldToFilter('status',['eq'=>1])->toArray();
            if(count($couriers['items']) > 0){
                foreach ($couriers['items'] as $courier){
                    $optionsList[] = [
                        "courier_slug" => $courier['courier_slug'],
                        "action_label" => 'Book '.ucwords($this->smartlaneHelper->cleanStringSpecialCharactersWithSpace($courier['courier_slug']))
                    ];
                }
            }

            $bulkActions = array_merge($defaultOption, $optionsList);

            if(!count($bulkActions)){
                return $this->options;
            }
            $options = [];
            //make a array of massaction
            foreach ($bulkActions as $key => $action) {
                $options[$i]['value']=$action['courier_slug'];
                $options[$i]['label']=$action['action_label'];
                $i++;
            }
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'courier_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }

            // return the massaction data
            $this->options = array_values($this->options);
        }
        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {

        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}