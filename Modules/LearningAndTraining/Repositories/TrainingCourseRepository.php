<?php

namespace Modules\LearningAndTraining\Repositories;

use App\Services\HelperService;
use Config;
use Illuminate\Support\Facades\DB;
use Modules\LearningAndTraining\Models\TrainingCourse;
use Modules\LearningAndTraining\Models\TrainingProfileCourse;

class TrainingCourseRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $fileUploadPath, $trainingProfileCourse, $helperService;

    /**
     * Create a new TrainingCourseLookupRepository instance.
     *
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(TrainingCourse $trainingCourseModel, TrainingProfileCourse $trainingProfileCourse, HelperService $helperService)
    {
        $this->model = $trainingCourseModel;
        $this->trainingProfileCourse = $trainingProfileCourse;
        $this->helperService = $helperService;
        $this->fileUploadPath = Config::get('globals.courseFilePath');
        $this->courseImagePath = Config::get('globals.courseImagePath');
    }

    /**
     * Get training course lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'reference_code', 'training_category_id',
        'course_title', 'course_description', 'course_objectives', 'course_image',
        'course_due_date', 'course_external_url', 'status','course_duration'])->orderby('id', 'DESC')->with('training_category')->get();
    }

    /**
     * Get training course lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('course_title', 'asc')->pluck('course_title', 'id')->toArray();
    }

    /**
     * Display details of single training course
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created training course in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $result = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        if ($data['id'] === null) {
            $reference_code = $this->helperService->getUniqueReferenceCode($result->id, [$result->course_title, $result->training_category->course_category]);
            $this->model->where('id', '=', $result->id)->update(['reference_code' => $reference_code]);
        }
        $created = $result->wasRecentlyCreated;
        $result = $result->fresh();
        $result['created'] = $created;
        return $result;
    }

    /**
     * To upload a file
     *
     * @param [type] $data
     * @param [type] $model
     * @return void
     */
    public function uploadFile($data, $model)
    {
        $this->model = $model;
        $fileName = $this->helperService->sanitiseString($this->model->course_title) . '-' . time() . '.' . $data['course_image']->getClientOriginalExtension();
        $path = public_path() . $this->courseImagePath;
        \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
        $data['course_image']->move($path, $fileName);
        $this->model->where('id', '=', $this->model->id)->update(['course_image' => $fileName]);
        $this->model = $this->model->fresh();
        return $this->model;
    }

    /**
     * Remove the specified training course from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $training_course_id = $this->trainingProfileCourse->pluck('course_id')->toArray();
        $path = public_path() . $this->courseImagePath;
        if (in_array($id, $training_course_id)) {
            return false;
        } else {
            $this->model = $this->model->find($id);
            \File::delete($path . '/' . $this->model->course_file);
            return $this->model->destroy($id);
        }
    }

    /**
     * Get training course for Team creation form listing
     *
     * @param empty
     * @return array
     */
    public function getAllForTeamListing()
    {
        return $this->model->orderby('course_title')->select(['id', 'course_title'])
            ->get();
    }

    public function getTotalCount()
    {
        return $this->model->count();
    }

    public function getListByAllicationDetails()
    {
        return $this->model->orderby('course_title', 'asc')
            ->select('id', 'course_title', 'course_due_date','course_duration')
            ->get()
            ->load('CourseAllocationCount', 'CourseAllocationCompletedCount')
            ->toArray();
    }

    public function getCourseDetails($id)
    {
        return $this->model->find($id)
            ->load('CourseAllocationCount', 'CourseAllocationCompletedCount', 'training_category');
    }
    public function getUserCourseDetails($id)
    {
        return $this->model->find($id)
           ->load('CourseUserAllocationCount', 'CourseUserAllocationCompletedCount', 'training_category');
    }
    public function getTrainingUserCourseDetails($id)
    {
        return $this->model->find($id)
            ->load('CourseTrainingUserAllocationCount', 'CourseTrainingUserAllocationCompletedCount', 'training_category');
    }

    /**
     *
     */

    public function getAllocationAndCompletedCount($inputs)
    {
         return $this->model->where('id', $inputs['course_id'])
        ->with('CourseAllocationCount', 'CourseAllocationCompletedCount')
        ->first();
    }
}
