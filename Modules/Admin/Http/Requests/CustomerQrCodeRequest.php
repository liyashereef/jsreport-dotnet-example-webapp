<?php

namespace Modules\Admin\Http\Requests;

class CustomerQrCodeRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('qrcodeid');
        $customer_id = request('customerid');
        return $rules = [
            'qrcode' => "bail|required|max:100|unique:customer_qrcode_locations,qrcode,{$id},id,customer_id,{$customer_id},deleted_at,NULL",
            'location' => 'bail|required|max:100',
            'no_of_attempts' => 'bail|required|integer|min:1|max:100',
            'no_of_attempts_week_ends' => 'bail|required|integer|min:1|max:100',
            'tot_no_of_attempts_week_day' => 'bail|required|integer|greater_than_field:no_of_attempts|min:1|max:100',
            'tot_no_of_attempts_week_ends' => 'bail|required|integer|greater_than_field:no_of_attempts_week_ends|min:1|max:100',
            'qrcode_active' => 'bail|required',
            'picture_enable_disable' => 'bail|required',
            'location_enable_disable' => 'bail|required',
            'picture_mandatory' => 'bail|required_if:picture_enable_disable,==,1',

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
            'qrcode.required' => 'QR code is required.',
            'qrcode.max' => 'QR code should not exceed 100 characters.',
            'qrcode.unique' => 'QR code should be unique.',
            'location.required' => 'Checkpoint is required',
            'location.max' => 'The Checkpoint should not exceed 100 characters.',
            'no_of_attempts.required' => 'Number of attempts is required.',
            'no_of_attempts.integer' => 'Number of attempts should be integer.',
            'no_of_attempts.min' => 'Minimum number of attempts should be 1.',
            'no_of_attempts.max' => 'Maximum number of attempts should be 100.',
            'no_of_attempts_week_ends.required' => 'Number of attempts is required.',
            'no_of_attempts_week_ends.integer' => 'Number of attempts should be integer.',
            'no_of_attempts_week_ends.min' => 'Minimum number of attempts should be 1.',
            'no_of_attempts_week_ends.max' => 'Maximum number of attempts should be 100.',
            'tot_no_of_attempts_week_day.required' => 'Total no of attempts is required.',
            'tot_no_of_attempts_week_day.integer' => 'Total no of attempts should be integer.',
            'tot_no_of_attempts_week_day.greater_than_field' => 'Total no of attempts should be greater than or equal to weekday attempts.',
            'tot_no_of_attempts_week_day.min' => 'Minimum Total no of attempts should be 1.',
            'tot_no_of_attempts_week_day.max' => 'Maximum Total no of attempts should be 100.',
            'tot_no_of_attempts_week_ends.required' => 'Total no of attempts is required.',
            'tot_no_of_attempts_week_ends.integer' => 'Total no of attempts should be integer.',
            'tot_no_of_attempts_week_ends.greater_than_field' => 'Total no of attempts should be greater than or equal to weekend attempts.',
            'tot_no_of_attempts_week_ends.min' => 'Minimum Total no of attempts should be 1.',
            'tot_no_of_attempts_week_ends.max' => 'Maximum Total no of attempts should be 100.',
            'qrcode_active.required' => 'Please select any option.',
            'picture_enable_disable.required' => 'Please select any option.',
            'location_enable_disable.required' => 'Please select any option.',
            'picture_mandatory.required_if' => 'Please select any option.',
        ];
    }

}
