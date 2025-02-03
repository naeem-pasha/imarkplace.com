<?php
/**
 * Webkul Odoomagentoconnect Product SyncProduct Controller
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Webkul Odoomagentoconnect Product SyncProduct Controller class
 */
class SyncProduct extends \Magento\Catalog\Controller\Adminhtml\Product
{
        /**
         * @param Action\Context                                         $context
         * @param Builder                                                $productBuilder
         * @param Filter                                                 $filter
         * @param CollectionFactory                                      $collectionFactory
         */
    public function __construct(
        Filter $filter,
        Product\Builder $productBuilder,
        CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomagentoconnect\Model\Product $productMapping,
        \Webkul\Odoomagentoconnect\Model\Template $templateMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Product $productMapResource,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Template $templateMapResource,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogConfigProduct
    ) {
        $this->_filter = $filter;
        $this->_connection = $connection;
        $this->_productMapping = $productMapping;
        $this->_collectionFactory = $collectionFactory;
        $this->_templateMapping = $templateMapping;
        $this->_productMapResource = $productMapResource;
        $this->_templateMapResource = $templateMapResource;
        $this->_catalogConfigProduct = $catalogConfigProduct;
        parent::__construct($context, $productBuilder);
    }

    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId) {
            $errorMessage = '';
            $countSyncProduct = 0;
            $countChildProducts = 0;
            $alreadySyncProduct = 0;
            $countUpdateProduct = 0;
            $errorUpdateMessage = '';
            $countNonSyncProduct = 0;
            $countNonUpdateProduct = 0;
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());

            foreach ($collection as $product) {
                $productId = $product->getId();
                if ($product->getTypeID() == "configurable") {
                    $mappingCollection = $this->_templateMapping->getCollection()->addFieldToFilter('magento_id', $productId);

                    if (!count($mappingCollection)) {
                        $response = $this->_templateMapResource->exportSpecificConfigurable($productId);
                        if ($response['odoo_id'] > 0) {
                            $odooTemplateId = $response['odoo_id'];
                            $this->_templateMapResource->syncConfigChildProducts($productId, $odooTemplateId);
                            $countSyncProduct++;
                        } else {
                            $countNonSyncProduct++;
                            $errorMessage = $response['error'];
                        }
                    } else {
                        foreach ($mappingCollection as $mappingObj) {
                            if ($mappingObj->getNeedSync() == "yes") {
                                $response = $this->_templateMapResource->updateConfigurableProduct($mappingObj);
                                if ($response['odoo_id'] > 0) {
                                    $countUpdateProduct++;
                                } else {
                                    $countNonUpdateProduct++;
                                    $errorUpdateMessage = $response['error'];
                                }
                            } else {
                                $alreadySyncProduct++;
                            }
                        }
                    }
                } else {
                    $parentId = $this->_catalogConfigProduct->getParentIdsByChild($productId);
                    $mappingCollection = $this->_productMapping->getCollection()->addFieldToFilter('magento_id', $productId);

                    if (isset($parentId[0]) && !count($mappingCollection)) {
                        $countChildProducts++;
                    } elseif (!count($mappingCollection)) {
                        $response = $this->_productMapResource->createSpecificProduct($productId);
                        if ($response['odoo_id'] > 0) {
                            $countSyncProduct++;
                        } else {
                            $countNonSyncProduct++;
                            $errorMessage = $response['error'];
                        }
                    } else {
                        foreach ($mappingCollection as $mappingObj) {
                            if ($mappingObj->getNeedSync() == "yes") {
                                $response = $this->_productMapResource->updateNormalProduct($mappingObj);
                                if ($response['odoo_id'] > 0) {
                                    $countUpdateProduct++;
                                } else {
                                    $countNonUpdateProduct++;
                                    $errorUpdateMessage = $response['error'];
                                }
                            } else {
                                $alreadySyncProduct++;
                            }
                        }
                    }
                }
            }

            if ($countNonSyncProduct) {
                $this->messageManager->addError(
                    __(
                        'Total of %1 Catalog Product(s) have not been Synchronized to Odoo. Reason : '.$errorMessage,
                        $countNonSyncProduct
                    )
                );
            }
            if ($countSyncProduct) {
                $this->messageManager
                    ->addSuccess(
                        __(
                            'Total of %1 Catalog Product(s) have been successfully Exported to Odoo.',
                            $countSyncProduct
                        )
                    );
            }
            if ($countUpdateProduct) {
                $this->messageManager
                    ->addSuccess(
                        __(
                            'Total of %1 Catalog Product(s) have been successfully Updated to Odoo.',
                            $countUpdateProduct
                        )
                    );
            }
            if ($countNonUpdateProduct) {
                $this->messageManager->addError(
                    __(
                        'Total of %1 Catalog Product(s) have not been Updated to Odoo. Reason : '.$errorUpdateMessage,
                        $countNonUpdateProduct
                    )
                );
            }
            if ($alreadySyncProduct) {
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 Catalog Product(s) have already been Synchronized to Odoo.',
                        $alreadySyncProduct
                    )
                );
            }
            if ($countChildProducts) {
                $this->messageManager->addWarning(
                    __(
                        'Total of %1 Catalog Product(s) have not been Synchronized to Odoo. Reason : 
                        Child Product(s) can be Synchronized via Parent Configurable Products(s) only!!',
                        $countChildProducts
                    )
                );
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "Selected Catalog Product(s) have not been Synchronized to Odoo. !! Reason : ".$errorMessage
                    )
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return  $resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
    }
}
