<?php

namespace Modules\Admin\Http\Requests;

class CpidRequest extends Request
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
            'cpid'           => "bail|required|max:15|unique:cpid_lookups,cpid,{$id},id,deleted_at,NULL",
            // 'position_id'    => "bail|required_without:id|unique:cpid_lookups,position_id,{$id},id,deleted_at,NULL",
            'position_id'    => "bail|required_without:id",
            'cpid_function_id' => "required",
            
            'effective_from' => "bail|required|date",
            'p_standard'     => "bail|required|regex:/^\d+(\.\d{1,2})?$/",
            'p_overtime'     => "bail|required|regex:/^\d+(\.\d{1,2})?$/",
            'p_holiday'      => "bail|required|regex:/^\d+(\.\d{1,2})?$/",
            'b_standard'     => "bail|required|regex:/^\d+(\.\d{1,2})?$/",
            'b_overtime'     => "bail|required|regex:/^\d+(\.\d{1,2})?$/",
            'b_holiday'      => "bail|required|regex:/^\d+(\.\d{1,2})?$/",
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
            'cpid.required'                => 'CPID is required.',
            'position_id.required_without' => 'Positions required.',
            'effective_from.required'      => 'Effective Date is required.',
            'p_standard.required'          => 'Pay Standard is required.',
            'p_standard.regex'             => 'Pay Standard format is invalid.',
            'p_overtime.required'          => 'Pay Overtime is required.',
            'p_overtime.regex'             => 'Pay Overtime format is invalid.',
            'p_holiday.required'           => 'Pay Stat is required.',
            'p_holiday.regex'              => 'Pay Stat format is invalid.',
            'b_standard.required'          => 'Bill Standard is required.',
            'b_standard.regex'             => 'Bill Standard format is invalid.',
            'b_overtime.required'          => 'Bill Overtime is required.',
            'b_overtime.regex'             => 'Bill Overtime format is invalid.',
            'b_holiday.required'           => 'Bill Stat is required.',
            'b_holiday.regex'              => 'Bill Stat format is invalid.',
            'cpid.unique'                  => 'CP ID is already added.',
            'cpid.max'                     => 'CP ID should not exceed 15 characters.',
        ];
    }
}
