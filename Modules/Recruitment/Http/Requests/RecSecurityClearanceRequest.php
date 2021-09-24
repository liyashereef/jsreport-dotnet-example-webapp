<?php

namespace Modules\Recruitment\Http\Requests;

class RecSecurityClearanceRequest extends Request
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
            'security_clearance' => "bail|required|max:255|unique:mysql_rec.rec_security_clearance_lookups,security_clearance,{$id},id,deleted_at,NULL",
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
            'security_clearance.required' => 'Security Clearance is required.',
            'security_clearance.unique' => 'This security clearance is already added.',
            'security_clearance.max' => 'Security clearance should not exceed 255 characters.',
        ];
    }
}
