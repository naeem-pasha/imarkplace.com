<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 7/17/20
 * Time: 7:25 PM
 */

namespace Webworks\SmartLane\Model;

use Webworks\SmartLane\Api\RequestValidator\SuggestedCouriers\SuggestedCouriersRequestValidator;
use Webworks\SmartLane\Api\SuggestedCouriersRepositoryInterface;

class SuggestedCouriersApiRepository implements SuggestedCouriersRepositoryInterface {


    protected $validator;
    protected $response;
    protected $smartlaneSuggestedCouriersFactory;
    protected $smartlaneSuggestedCouriersResource;
    protected $collectionFactory;


     public function __construct(
         SuggestedCouriersRequestValidator $validator,
         \Magento\Framework\Webapi\Rest\Response $response,
         \Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers\CollectionFactory $collectionFactory,
         \Webworks\SmartLane\Model\SmartlaneSuggestedCouriersFactory $smartlaneSuggestedCouriersFactory,
         \Webworks\SmartLane\Model\ResourceModel\SmartlaneSuggestedCouriers $smartlaneSuggestedCouriersResource

    ) {
         $this->validator = $validator;
         $this->response = $response;
         $this->smartlaneSuggestedCouriersFactory = $smartlaneSuggestedCouriersFactory;
         $this->smartlaneSuggestedCouriersResource = $smartlaneSuggestedCouriersResource;
         $this->collectionFactory = $collectionFactory;

    }


    public function createSuggestedCouriers($data)
    {
        $validate = $this->validator->validate($data);
        if($validate === true){
            try{
                $collection = $this->collectionFactory->create();
                //update all couriers with status zero
                $collection->setTableRecords('1=1', ['status' => 0]);

                //only if mode is hard
                if(isset($data['mode']) && $data['mode'] == 'hard' && count($data['couriers']) > 0){
                    foreach ($data['couriers'] as $item){
                        $smartlaneSuggestedCouriersModel = $this->smartlaneSuggestedCouriersFactory->create();
                        $this->smartlaneSuggestedCouriersResource->load($smartlaneSuggestedCouriersModel, $item, 'courier_slug');
                        $smartlaneSuggestedCouriersModel->setData('status', 1);
                        $smartlaneSuggestedCouriersModel->setData('courier_slug', $item);
                        $this->smartlaneSuggestedCouriersResource->save($smartlaneSuggestedCouriersModel);
                    }
                }

                $this->response->setHttpResponseCode(200);
                $response = [
                    "code"=> 200,
                    "status"=> "Success",
                    "message"=> "Couriers Processed Successfully!",
                ];
                $this->response->setContent(json_encode($response));
                $this->response->sendResponse();
                return;

            }catch (\Error $error){
                $this->response->setHttpResponseCode(500);
                $response = [
                    "code"=> 500,
                    "status"=> "Internal server error",
                    "message"=> $error->getMessage(),
                    "Stacktrace" => [
                        "File" => __FILE__,
                        "Function" => __FUNCTION__,
                        "Line" => __LINE__
                    ]
                ];
                $this->response->setContent(json_encode($response));
                $this->response->sendResponse();
                return;
            }

        }else{
            $this->response->setHttpResponseCode(422);
            $response = [
                "code"=> 422,
                "status"=> "Validation Failed",
                "message"=> "The given data was invalid.",
                "validation_params_error"=> $validate
            ];
            $this->response->setContent(json_encode($response));
            $this->response->sendResponse();
            return;
        }
    }

}