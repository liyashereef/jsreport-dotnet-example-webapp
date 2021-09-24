<?php

namespace Modules\Admin\Http\Requests;

class BusinessSegmentRequest extends Request
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
            'segmenttitle' => "bail|required|max:255",
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
            'segmenttitle.required' => 'Business Segment is required.',
            'segmenttitle.unique' => 'This Business Segment is already added.',
            //'segmenttitle.max' => 'The Business Segment should not exceed 255 characters.',
        ];
    }

}
