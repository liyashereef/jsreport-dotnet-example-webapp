<?php

namespace Modules\Admin\Http\Requests;

class ImportRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'import_file' => 'bail|required|max:3072',
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
            'import_file.required' => 'Please choose a file',
            'import_file.max' => 'File upload size should not exceed 3MB',
            'import_file.mime' => 'Upload excel file of extension .xlsx or .xls',
        ];
    }

}
