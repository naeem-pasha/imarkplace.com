<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 8/21/20
 * Time: 2:40 PM
 */

namespace Webworks\SmartLane\Api\RequestValidator\SuggestedCouriers;


use Webworks\SmartLane\Api\RequestValidator\RequestValidatorInterface;

class SuggestedCouriersRequestValidator implements RequestValidatorInterface
{
    protected $productFactory;
    protected $smartIntegratorHelper;

    public function __construct()
    {
    }

    public function validate($data)
    {

        if (!isset($data["mode"]) || !\Zend_Validate::is($data["mode"], 'NotEmpty')) {
            $errors["data.mode"] = 'Mode should be hard/soft';
        }else{
            $valid  = new \Zend\Validator\InArray();
            $valid->setHaystack(['hard', 'soft']);
            $result = $valid->isValid($data["mode"]);
            if(!$result){
                $errors["data.mode"] = 'Mode should be hard/soft';
            }
        }

        if (!isset($data["couriers"]) || !is_array($data["couriers"])) {
            $errors["data.mode"] = 'Courier Should be an array';
        }


        if(empty($errors)){
            return true;
        }

        return $errors;

    }

}