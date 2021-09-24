<?php

namespace Modules\Recruitment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecSecurityAwarenessRequest extends FormRequest
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
            'answer' => "bail|required|max:255|unique:mysql_rec.rec_security_awareness,answer,{$id},id,deleted_at,NULL"
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
            'answer.required' => 'Awareness is required.',
            'answer.unique' => 'This Awareness is already added.',
            'answer.max' => 'The Awareness should not exceed 255 characters.',
        ];
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
