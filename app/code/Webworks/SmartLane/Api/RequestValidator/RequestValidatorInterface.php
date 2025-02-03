<?php
/**
 * Created by PhpStorm.
 * User: waqas
 * Date: 8/21/20
 * Time: 2:38 PM
 */

namespace Webworks\SmartLane\Api\RequestValidator;


interface RequestValidatorInterface
{
    public function validate($data);

}