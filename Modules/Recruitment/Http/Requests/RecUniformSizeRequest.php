<?php

namespace Modules\Recruitment\Http\Requests;

class RecUniformSizeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return [
            'size_name' => "bail|required|max:255|unique:mysql_rec.rec_uniform_sizes,size_name,{$id},id,deleted_at,NULL",
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
            'size_name.required' => 'Size name is required.',
            'size_name.unique' => 'This size name is already added.',
            'size_name.max' => 'This size name should not exceed 255 characters.',
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
