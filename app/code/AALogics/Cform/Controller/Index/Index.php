<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AALogics\Cform\Controller\Index;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;


class Index extends Action
{
    private $dataPersistor;
    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */

    protected $context;
    private $fileUploaderFactory;


    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */

     public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    { 

        $post = $this->getRequest()->getPostValue();

        $txt='<table>';

        if($post['name']){         
            $txt.='<tr><td><strong>Client Name</strong>:'.$post['name'].'</td></tr>';          
        }
        if($post['address']){           
            $txt.='<tr><td><strong>Address</strong>:'.$post['address'].'</td></tr>';            
        }
        if($post['city']){
            $txt.='<tr><td><strong>City</strong>:'.$post['city'].'</td></tr>';
        }
        if($post['service']){
            $txt.='<tr><td><strong>Service</strong>:'.$post['service'].'</td></tr>';
        }
        if($post['phone']){
            $txt.='<tr><td><strong>Phone</strong>:'.$post['phone'].'</td></tr>';
        }
        $txt.='</table>';
        //echo $txt;

 
        $fromEmail= 'info@imarkplace.com';
        $fromName = 'IMARK';

        $templateVars = array(
            'name'   => $post['name'],
            'phone'  => $post['phone'],
            'service'  => $post['service'],
            'city'  => $post['city'],
            'address'  => $post['address'],
        );

        if(isset($post['cnic'])){
            $templateVars['cnic'] =  $post['cnic'];
        }

        $from = ['email' => $fromEmail, 'name' => $fromName];
        $this->inlineTranslation->suspend();

        $to = 'info@imarkplace.com';     

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

         $templateOptions = [
          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
          'store' => 1
        ];

        $transport = $this->_transportBuilder->setTemplateIdentifier('cform_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to)          
                ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

        $this->messageManager->addSuccess(__('Form successfully submitted'));

        $this->_redirect('form');
    }

}