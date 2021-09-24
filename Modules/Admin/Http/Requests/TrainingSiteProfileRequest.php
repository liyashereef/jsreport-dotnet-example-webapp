<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Rules\MandatoryCourseValidation;
use Modules\Admin\Rules\MandatoryUniqueValidation;
use Modules\Admin\Rules\RecommendedCourseValidation;
use Modules\Admin\Rules\RecommendedUniqueValidation;

class TrainingSiteProfileRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $mandatory_course = request('mandatory_course');
        $recommended_course = request('recommended_course');
        return $rules = [
            'customer_id' => "bail|required|unique:training_profile_sites,customer_id,{$id},id,deleted_at,NULL",
            'profile_name' => "bail|required|max:255|unique:training_profile_sites,profile_name,{$id},id,deleted_at,NULL",
            'mandatory_course.*' => ['bail', 'required', new MandatoryUniqueValidation($mandatory_course), new MandatoryCourseValidation($recommended_course)],
            'recommended_course.*' => ['bail', 'required', new RecommendedUniqueValidation($recommended_course), new RecommendedCourseValidation($mandatory_course)],
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
            'customer_id.required' => 'Please choose customer',
            'customer_id.unique' => 'This customer already have a site profile',
            'profile_name.required' => 'Profile Name is required.',
            'profile_name.unique' => 'This Profile Name is already added.',
            'profile_name.max' => 'The Profile Name should not exceed 255 characters.',
            'mandatory_course.*.required' => 'Please Select Mandatory Course',
            'recommended_course.*.required' => 'Please Select Recommended Course',
        ];
    }

}
