<?php

namespace Webworks\SmartLane\Controller\Adminhtml\Integrationkeys;

use Magento\Framework\App\ResourceConnection;
use Webworks\SmartLane\Helper\Data;

class Generatekeys extends \Magento\Backend\App\Action
{
    /**
     * @var LoggerInterface
    */
    protected $_logger;

    /**
    * @var resultJsonFactory
    */
    protected $resultJsonFactory;

    /**
    * @var integrationmethod
    */
    protected $integrationmethod;

    /**
    * @var oathservice
    */
    protected $oathservice;

    /**
    * @var authorizeservice
    */
    protected $authorizeservice;

    /**
    * @var oathtoken
    */
    protected $oathtoken;

    /**
    * @var Data
    */
    protected $smarthelper;
    protected $transactionFactory;
    protected $resource;
    protected $dbConnection;
    protected $random;



    /**
     * Generatekeys constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Integration\Model\IntegrationFactory $integrationmethod
     * @param \Magento\Integration\Model\OauthService $oathservice
     * @param \Magento\Integration\Model\AuthorizationService $authorizeservice
     * @param \Magento\Integration\Model\Oauth\Token $oathtoken
     * @param Data $smarthelper
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Integration\Model\IntegrationFactory $integrationmethod,
        \Magento\Integration\Model\OauthService $oathservice,
        \Magento\Integration\Model\AuthorizationService $authorizeservice,
        \Magento\Integration\Model\Oauth\Token $oathtoken,
        Data $smarthelper,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Math\Random $random
    ) {
        $this->_logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->integrationmethod = $integrationmethod;
        $this->oathservice = $oathservice;
        $this->authorizeservice = $authorizeservice;
        $this->oathtoken= $oathtoken;
        $this->smarthelper = $smarthelper;
        $this->transactionFactory = $transactionFactory;
        $this->resource = $resource;
        $this->random = $random;
        $this->dbConnection = $this->resource->getConnection();
        parent::__construct($context);
    }

    /**
    *
    * Generate Integration Keys for SmartLane and send over API
    ***/
    public function execute()
    {
        // $this->_logger->debug('Sync Starts!!');
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $resultJson = $this->resultJsonFactory->create();

        //Set your Data
        $name = 'MagentoSmartLane';
        $email = '';
        $endpoint = '';
         $return  = '';
        $tokenId = null;
        $consumerId = null;
        $integrationId = null;


        $integrationExists = $this->integrationmethod->create()->load($name,'name')->getData();
       
        if(!($integrationExists)){
          
            $integrationData = array(
                'name' => $name,
                'email' => $email,
                'status' => '1',
                'endpoint' => $endpoint,
                'setup_type' => '0',
            );


            try{
                $this->dbConnection->beginTransaction();
                // Code to create consumer
                $oauthService = $this->oathservice;
                $token = $this->oathtoken;
                $consumerName = $name.'_'.$this->random->getRandomString('5');
                $consumer = $oauthService->createConsumer(['name' => $consumerName]);
                $consumerId = $consumer->getId();

                if($consumerId!=null || !empty($consumerId)) {
                    // Code to Activate and Authorize
                    $uri = $token->createVerifierToken($consumerId);
                    $token->setType('access');
                    $token->save();
                    $tokenId = $token->getId();
                }else{
                    $return = 'Something went wrong!';
                }

                if(($consumerId!=null || !empty($consumerId)) && ($tokenId!=null || !empty($tokenId))){
                // Code to create Integration
                $integrationFactory = $this->integrationmethod->create();
                $integrationData['consumer_id'] = $consumerId;
                $integration = $integrationFactory->setData($integrationData);
                $integration->save();
                $integrationId = $integration->getId();

                // Code to grant permission
                $authrizeService = $this->authorizeservice;
                $authrizeService->grantAllPermissions($integrationId);
                }else{
                    $return = 'Something went wrong!';
                }

                $this->dbConnection->commit();

                if(($consumerId!=null || !empty($consumerId)) && ($tokenId!=null || !empty($tokenId)) && ($integrationId!=null || !empty($integrationId))){
                    // Prepare Data for SmartLane
                    $consumerkey=$consumer->getKey();
                    $consumersecret= $consumer->getSecret();
                    $tokenoath= $token->getToken();
                    $tokensecret= $token->getSecret();

                    $consumerData= array(
                        'consumer_key'=>$consumerkey,
                        'consumer_secret'=>$consumersecret,
                        'access_token'=>$tokenoath,
                        'access_token_secret'=>$tokensecret
                    );
                    $jsonConsumerData = json_encode($consumerData);

                    // Call SmartLane API to Send Consumer data
                    $sendkeys= $this->smarthelper->sendAuthKeysToSL($jsonConsumerData);
                    $resultdata= json_decode($sendkeys);

                    $return = $resultdata->message;
                }else{
                    $return = 'Something went wrong!';
                }

            }catch(\Exception $e){
                $return = $e->getMessage();
                $this->dbConnection->rollBack();

         }
     }else{
        /** Update consumer key **/
       $consumerId = $integrationExists['consumer_id'];
       if($consumerId){
           $oauthService = $this->oathservice;
           $consumer = $oauthService->loadConsumer($integrationExists['consumer_id']);

           // Code to Activate and Authorize
           $token = $this->oathtoken;
           $uri = $token->createVerifierToken($consumerId);
           $token->setType('access');
           $token->save();

           // Prepare Data for SmartLane
           $consumerkey=$consumer->getKey();
           $consumersecret= $consumer->getSecret();
           $tokenoath= $token->getToken();
           $tokensecret= $token->getSecret();

           $consumerData= array(
               'consumer_key'=>$consumerkey,
               'consumer_secret'=>$consumersecret,
               'access_token'=>$tokenoath,
               'access_token_secret'=>$tokensecret
           );
           $jsonConsumerData = json_encode($consumerData);

           // Call SmartLane API to Send Consumer data
           $sendkeys= $this->smarthelper->sendAuthKeysToSL($jsonConsumerData);
           $resultdata= json_decode($sendkeys);


           $return = $resultdata->message;
       }else{
           $return = 'something went wrong';
       }

     }
     return $resultJson->setData($return);
 }


}