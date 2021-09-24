<?php

namespace Modules\Admin\Http\Requests;

class DivisionLookupRequest extends Request
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
            'division_name' => "bail|required|max:255",
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
            'division_name.required' => 'Business Segment is required.',
            'division_name.unique' => 'This Business Segment is already added.',
            //'segmenttitle.max' => 'The Business Segment should not exceed 255 characters.',
        ];
    }

}
