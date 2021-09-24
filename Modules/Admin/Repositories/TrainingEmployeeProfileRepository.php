<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\TrainingProfile;
use Modules\Admin\Models\TrainingProfileCourse;
use Modules\Admin\Models\TrainingProfileRole;

class TrainingEmployeeProfileRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $trainingProfileCourse;
    protected $trainingProfile;

    /**
     * Create a new EmployeeRatingLookup instance.
     *
     * @param  \App\Models\TrainingProfile $trainingProfile
     * @param  \App\Models\TrainingProfileCourse $trainingProfileCourse
     */
    public function __construct(TrainingProfileCourse $trainingProfileCourse, TrainingProfile $trainingProfile)
    {
        $this->trainingProfileCourse = $trainingProfileCourse;
        $this->trainingProfile = $trainingProfile;
    }

    /**
     * Store a newly created Training Employee Profile in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request)
    {

        $id = $request->id;
        $profile['profile_name'] = $request->profile_name;
        $role['role_id'] = $request->role_id;
        $profile = TrainingProfile::updateOrCreate(array('id' => $id), $profile);
        $role['training_profile_id'] = $profile->id;
        $roles = TrainingProfileRole::updateOrCreate(array('training_profile_id' => $profile->id), $role);
        $mandatory_course = $request->mandatory_course;
        foreach ($mandatory_course as $key => $mandatory) {
            $course['training_profile_id'] = $profile->id;
            $course['course_id'] = $mandatory;
            $course['course_type'] = 'Mandatory';

            $id_mandatory[] = TrainingProfileCourse::updateOrCreate(array('training_profile_id' => $profile->id, 'course_id' => $mandatory, 'profile_type' => 'employee'), $course);

        }
        foreach ($id_mandatory as $key => $value) {
            $values[] = $value->id;
        }
        TrainingProfileCourse::where('training_profile_id', $profile->id)->where('course_type', "Mandatory")->where('profile_type', 'employee')->whereNotIn('id', $values)->delete();
        $recommended_course = $request->recommended_course;
        foreach ($recommended_course as $key => $recommended) {
            $course['training_profile_id'] = $profile->id;
            $course['course_id'] = $recommended;
            $course['course_type'] = 'Recommended';
            $id_recommended[] = TrainingProfileCourse::updateOrCreate(array('training_profile_id' => $profile->id, 'course_id' => $recommended, 'profile_type' => 'employee'), $course);

        }
        foreach ($id_recommended as $key => $value) {
            $values[] = $value->id;
        }
        TrainingProfileCourse::where('training_profile_id', $profile->id)->where('course_type', "Recommended")->where('profile_type', 'employee')->whereNotIn('id', $values)->delete();
        return true;
    }

    /**
     * Get Training Profile list
     *
     * @param empty
     * @return array
     */
    public function getAllTrainingProfile()
    {
        return $this->trainingProfile->orderby('profile_name', 'asc')->select(['id', 'profile_name', 'created_at', 'updated_at'])->get();
    }

    /**
     * Display details of single Security Clearance
     *
     * @param $id
     * @return object
     */
    public function getTrainingProfile($id)
    {

        $employee_profile_data = $this->trainingProfileCourse->where('training_profile_id', $id)->where('profile_type', '=', 'employee')->with('training_course', 'training_profile', 'training_profile.training_profile_role')->get();
        $data['id'] = $employee_profile_data->first()->training_profile->id;
        $data['profile_name'] = $employee_profile_data->first()->training_profile->profile_name;
        $data['role_id'] = $employee_profile_data->first()->training_profile->training_profile_role->role_id;
        foreach ($employee_profile_data as $key => $each_employee_profile) {
            $data[$key]['course_id'] = $each_employee_profile->course_id;
            $data[$key]['course_type'] = $each_employee_profile->course_type;
            $data[$key]['course_title'] = $each_employee_profile->training_course->course_title;
        }

        return $data;

    }

    /**
     * Remove the specified Security Clearance from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteTrainingProfile($id)
    {
        return $this->trainingProfile->destroy($id);
    }

}
