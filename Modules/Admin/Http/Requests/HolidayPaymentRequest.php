<?php

namespace Modules\Admin\Http\Requests;

class HolidayPaymentRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
            'paymentstatus' => "bail|required|max:255",
            
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
            'paymentstatus.required' => 'Payment Status is required.',
            'paymentstatus.unique' => 'This Payment Status is already added.',
            //'segmenttitle.max' => 'The Business Segment should not exceed 255 characters.',
        ];
    }

}
