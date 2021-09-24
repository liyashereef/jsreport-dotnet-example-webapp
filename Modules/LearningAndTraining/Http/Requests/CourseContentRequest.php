<?php

namespace Modules\LearningAndTraining\Http\Requests;

class CourseContentRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $content_type_id = request('content_type_id');
        //echo request('course_file')->getMimeType();
        //exit;
        $rules = [
        //    'content_title'=>"required|unique:course_contents,content_title,{$id},id,deleted_at,NULL",
           'content_title'=>"required",
           'course_id' => "bail|required",
            'content_type_id' => "required",
        ];
        if ($id == null) {
            $file_val_img="bail|required|image";
            $file_val_pdf="bail|required|mimes:pdf";
            $file_val_vedeo="bail|required|mimes:mp4,mov,ogg,qt";
        } else {
            $file_val_img="bail|image";
            $file_val_pdf="bail|mimes:pdf";
            $file_val_vedeo="bail|mimes:mp4,mov,ogg,qt";
        }
        if ($content_type_id == 1) {
            $other_rules = [
                'course_file' =>$file_val_img, //mimetype:audio/mpeg,ppt,video/mp4
            ];
            $rules = array_merge($rules, $other_rules);
        }
        if ($content_type_id == 2) {
            $other_rules = [
                'course_file' => $file_val_pdf, //mimetype:audio/mpeg,ppt,video/mp4
            ];
            $rules = array_merge($rules, $other_rules);
        }
        if ($content_type_id == 3) {
            $other_rules = [
                'course_file' =>$file_val_vedeo, //mimetype:audio/mpeg,ppt,video/mp4
            ];
            $rules = array_merge($rules, $other_rules);
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'content_title.required' => 'Content title is required.',
            'content_title.unique' => 'This content title is already added.',
            'course_id.required' => 'Course is required.',
            'course_id.max' => 'The training_course_id should not exceed 255 characters.',
            'content_type_id.required' => 'Content type is required.',
        ];
    }
}
