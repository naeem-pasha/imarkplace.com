<?php

namespace MageBig\FastCheckout\Plugin\Checkout\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Paypal\Model\ExpressConfigProvider;

class FastCheckoutExpressConfigProvider
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @param RequestInterface $request
     * @param Repository $assetRepo
     */
    public function __construct(
        RequestInterface $request,
        Repository $assetRepo
    ) {
        $this->_request = $request;
        $this->assetRepo = $assetRepo;
    }

    /**
     * After get config
     *
     * @param ExpressConfigProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(
        ExpressConfigProvider $subject,
        array $result
    ) {
        if ($this->_request->getRouteName() === 'checkout') {
            $img = $this->assetRepo->getUrl('MageBig_FastCheckout::images/paypal-mark-color.svg');
            $result['payment']['paypalExpress']['paymentAcceptanceMarkSrc'] = $img;
        }

        return $result;
    }
}
