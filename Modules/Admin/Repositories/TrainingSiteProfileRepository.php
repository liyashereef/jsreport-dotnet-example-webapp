<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\TrainingProfile;
use Modules\Admin\Models\TrainingProfileCourse;
use Modules\Admin\Models\TrainingProfileSite;

class TrainingSiteProfileRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $trainingProfileCourse;
    protected $trainingProfile;
    protected $trainingProfileSite;

    /**
     * Create a new TrainingProfileSite instance.
     *
     * @param  \App\Models\TrainingProfile $trainingProfile
     * @param  \App\Models\TrainingProfileCourse $trainingProfileCourse
     * @param  \App\Models\TrainingProfileSite $trainingProfileSite
     */
    public function __construct(TrainingProfileCourse $trainingProfileCourse, TrainingProfile $trainingProfile, TrainingProfileSite $trainingProfileSite)
    {
        $this->trainingProfileCourse = $trainingProfileCourse;
        $this->trainingProfile = $trainingProfile;
        $this->trainingProfileSite = $trainingProfileSite;
    }

    /**
     * Store a newly created Training Site Profile in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($request)
    {
        $id = $request->id;
        $profile['profile_name'] = $request->profile_name;
        $profile['customer_id'] = $request->customer_id;
        $profile = $this->trainingProfileSite->updateOrCreate(array('id' => $id), $profile);
        $mandatory_course = $request->mandatory_course;
        foreach ($mandatory_course as $key => $mandatory) {
            $course['training_profile_id'] = $profile->id;
            $course['course_id'] = $mandatory;
            $course['course_type'] = 'Mandatory';
            $course['profile_type'] = 'Site';
            $id_mandatory[] = TrainingProfileCourse::updateOrCreate(array('training_profile_id' => $profile->id, 'course_id' => $mandatory, 'profile_type' => 'Site'), $course);
        }
        foreach ($id_mandatory as $key => $value) {
            $values[] = $value->id;
        }
        TrainingProfileCourse::where('training_profile_id', $profile->id)->where('course_type', "Mandatory")->where('profile_type', "Site")->whereNotIn('id', $values)->delete();
        $recommended_course = $request->recommended_course;
        foreach ($recommended_course as $key => $recommended) {
            $course['training_profile_id'] = $profile->id;
            $course['course_id'] = $recommended;
            $course['course_type'] = 'Recommended';
            $course['profile_type'] = 'Site';
            $id_recommended[] = TrainingProfileCourse::updateOrCreate(array('training_profile_id' => $profile->id, 'course_id' => $recommended, 'profile_type' => 'Site'), $course);
        }
        foreach ($id_recommended as $key => $value) {
            $values[] = $value->id;
        }
        TrainingProfileCourse::where('training_profile_id', $profile->id)->where('course_type', "Recommended")->where('profile_type', "Site")->whereNotIn('id', $values)->delete();
        return true;
    }

    /**
     * Get Training Profile Site list
     *
     * @param empty
     * @return array
     */
    public function getAllTrainingSiteProfile()
    {
        return $this->trainingProfileSite->orderby('profile_name', 'asc')->select(['id', 'profile_name', 'customer_id', 'created_at', 'updated_at'])->with('customer')->whereHas('customer')->get();
    }

    /**
     * Display details of single Training Site Profile
     *
     * @param $id
     * @return object
     */
    public function getTrainingSiteProfile($id)
    {
        $trainingProfileSite = $this->trainingProfileSite->with('customer')->find($id);
        $trainingProfileCourse = $this->trainingProfileCourse->where('training_profile_id', $id)->where('profile_type', 'Site')->with('training_course')->get();
        $data['id'] = $trainingProfileSite->id;
        $data['profile_name'] = $trainingProfileSite->profile_name;
        $data['customer_id'] = $trainingProfileSite->customer_id;
        foreach ($trainingProfileCourse as $key => $value) {
            $data[$key]['course_id'] = $value->course_id;
            $data[$key]['course_type'] = $value->course_type;
            $data[$key]['course_title'] = $value->training_course->course_title;
        }
        return $data;
    }

    /**
     * Remove the specified Training Site Profile from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteTrainingSiteProfile($id)
    {

        return $this->trainingProfileSite->destroy($id);
    }

}
