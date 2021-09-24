<?php

namespace Modules\Recruitment\Http\Requests;

class RecBrandAwarenessRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //$id = request('id');
        return $rules = [
            'answer' => "bail|required",
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
         'answer.required' => 'Brand awareness is required.',
        ];
    }
}
