<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Ui\DataProvider\Product;

/**
 * Class CrossSellDataProvider
 */
class CrossSellDataProvider extends AbstractDataProvider
{
    /**
     * {@inheritdoc
     */
    protected function getLinkType()
    {
        return 'cross_sell';
    }
}
