<?php

namespace Modules\Admin\Http\Exceptions;

use Exception;

class CglLambdaException extends Exception
{
    public $awsExceptionObject;

    public function __construct($awsExceptionObject, $message = null)
    {
        if(empty($message)) {
            $message = "Error in Lambda code";
        }
        parent::__construct($message.": ".json_encode($awsExceptionObject));
        $this->awsExceptionObject = json_encode($awsExceptionObject);
    }

}

