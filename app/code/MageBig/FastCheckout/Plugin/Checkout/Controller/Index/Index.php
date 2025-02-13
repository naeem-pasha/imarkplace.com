<?php
/**
 * Copyright © www.magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageBig\FastCheckout\Plugin\Checkout\Controller\Index;

use MageBig\FastCheckout\Helper\Data;
use Magento\Checkout\Controller\Index\Index as IndexCheckout;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory as ResultFactory;
use Magento\Framework\View\Result\Page;

class Index
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param ResultFactory $resultFactory
     * @param Data $helper
     */
    public function __construct(
        ResultFactory $resultFactory,
        Data $helper
    ) {
        $this->resultFactory = $resultFactory;
        $this->helper = $helper;
    }

    /**
     * Index
     *
     * @param IndexCheckout $subject
     * @param Page|mixed $result
     * @return Page|mixed
     * @throws NoSuchEntityException
     */
    public function afterExecute(IndexCheckout $subject, $result)
    {
        if (!$this->helper->getChecked()) {
            return $result;
        }

        if ($result instanceof Page) {
            $resultPage = $this->resultFactory->create();
            $resultPage->addHandle('fastcheckout_index_index');

            if ($this->helper->getPageLayout() == 1) {
                $resultPage->addHandle('fastcheckout_one_column');
            }

            if ($this->helper->getPageLayout() == 2) {
                $resultPage->addHandle('fastcheckout_two_columns_sidebar');
            }
        }

        return $result;
    }
}
