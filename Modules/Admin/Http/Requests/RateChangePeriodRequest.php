<?php

namespace Modules\Admin\Http\Requests;

class RateChangePeriodRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $ratechangetitile = request('ratechangetitile');
        return $rules = [
            'ratechangetitile' => "required",       
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'ratechangetitile.required' => 'Rate Change Title is required.',
        ];
    }

}
