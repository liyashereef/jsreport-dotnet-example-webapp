<?php

namespace Modules\Osgc\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OsgcTestSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          //  'exam_name' => 'bail|required|max:50|unique:osgc_test_course_masters,osgc_course_section_id,' . request('id') . ',id,osgc_course_section_id,' . request('osgc_course_section_id'),
            'exam_name' => "bail|required|max:50",
           // 'number_of_question'=>'required',
            'osgc_course_section_id'=>'required',
            'pass_percentage'=>'required',
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
