<?php

namespace Modules\Timetracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DispatchRequestForRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'subject' => 'required|string',
                        'dispatch_request_type_id' => 'required|exists:dispatch_request_types,id',
                        // 'customer_id' => 'required|exists:customers,id',
                        'site_address' => 'required|string',
                        'site_postalcode' => 'required|string',
                        'rate'  => 'required|numeric',
                        'description'=> '',
                    ];
                }
            default: return [];
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
