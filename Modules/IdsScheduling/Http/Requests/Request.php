<?php

namespace Modules\IdsScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * Authorization
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }
}
