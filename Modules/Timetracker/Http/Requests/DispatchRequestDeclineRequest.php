<?php

namespace Modules\Timetracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DispatchRequestDeclineRequest extends FormRequest
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
                {
                    return [
                        'dispatch_request_id' => 'required|exists:dispatch_requests,id',
                        'user_id'  => '',
                        'comment'=> 'required',
                    ];
                }
            case 'PUT':
            case 'PATCH':

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
