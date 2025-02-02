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
namespace Webkul\MpAffiliate\Ui\Component\MassAction\Status;

class Options implements \JsonSerializable
{
    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

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
    protected $additionalOptionData = [];

    /**
     * Constructor
     *
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = [
               [
                   "value" => "1",
                   "label" => ('Enable'),
               ],
               [
                   "value" => "2",
                   "label" => ('Disable'),
               ]
            ];
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                   'type' => 'status_' . $optionCode['value'],
                   'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlInterface->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalOptionData
                );
            }
            $this->options = array_values($this->options);
        }
        return $this->options;
    }

    /**
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $k => $v) {
            switch ($k) {
                case 'urlPath':
                    $this->urlPath = $v;
                    break;
                case 'paramName':
                    $this->paramName = $v;
                    break;
                default:
                    $this->additionalOptionData[$k] = $v;
                    break;
            }
        }
    }
}
