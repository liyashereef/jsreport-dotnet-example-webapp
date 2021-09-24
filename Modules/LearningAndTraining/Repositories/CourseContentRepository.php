<?php

namespace Modules\LearningAndTraining\Repositories;

use App\Services\HelperService;
use Config;
use Modules\LearningAndTraining\Models\CourseContent;
use Modules\LearningAndTraining\Models\TrainingCourse;
//use Modules\LearningAndTraining\Models\TrainingProfileCourse;
use Illuminate\Support\Facades\Storage;

class CourseContentRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $fileUploadPath;
    protected $trainingCourse;
    protected $helperService;

    /**
     * Create a new TrainingCourseLookupRepository instance.
     *
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(CourseContent $courseContentModel, TrainingCourse $trainingCourse, HelperService $helperService)
    {
        $this->model = $courseContentModel;
        $this->trainingCourse = $trainingCourse;
        $this->helperService = $helperService;
        $this->fileUploadPath = Config::get('globals.courseFilePath');
    }

    /**
     * Get training course lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('id', 'asc')->select(['id', 'content_type_id', 'value','content_title','course_id', 'created_at', 'updated_at'])->with('training_courses')->with('course_content_types')->get();
    }

    /**
     * Get training course lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllByCourseId($course_id)
    {
        return $this->model->orderby('id', 'asc')->select(['id', 'content_type_id', 'value','content_title','course_id', 'created_at', 'updated_at'])
            ->where('course_id', $course_id)
            ->with('training_courses')
            ->with('course_content_types')
            ->get();
    }

    public function getCountByCourseId($course_id)
    {
        return $this->model->where('course_id', $course_id)
             ->get()
             ->count();
    }

    public function getCompletedContentCountByCourseId($course_id, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_contents.training_user_id';
        } else {
            $user_id = \Auth::User()->id;
            $column = 'training_user_contents.user_id';
        }
        return $this->model->join('training_user_contents', 'course_contents.id', '=', 'training_user_contents.course_content_id')
             ->where('course_contents.course_id', $course_id)
             ->where($column, $user_id)
             ->where('training_user_contents.completed', 1)
             ->get()
             ->count();
    }

    /**
     * Get training course lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('course_id', 'asc')->pluck('course_id', 'id')->toArray();
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
     * Display list of Course Content by course_id
     *
     * @param $course_id
     * @return object
     */
    public function getByCourseId($course_id)
    {
        return $this->model->where('course_id', $course_id)->get();
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
            /*$reference_code = $this->helperService->getUniqueReferenceCode($result->id, [$result->course_title, $result->training_category->course_category]);
            $this->model->where('id', '=', $result->id)->update(['reference_code' => $reference_code]);*/
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
        ini_set('max_execution_time', 30000);
        $this->model = $model;
        $courseDet=$this->trainingCourse->find($this->model->course_id);
        $fileName = $this->helperService->sanitiseString($courseDet['course_title']) . '-' . time() . '.' . $data['course_file']->getClientOriginalExtension();
        $path = public_path() . $this->fileUploadPath;
        \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
        $file = $data['course_file'];
        if ($data['content_type_id']==1) {
            $filePath = 'images/' . $fileName;
        }
        if ($data['content_type_id']==2) {
            $filePath = 'pdf/' . $fileName;
        }
        if ($data['content_type_id']==3) {
            $filePath = 'video/' . $fileName;
        }
        Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
        //$data['course_file']->move(public_path('course_files'), $fileName);

        $this->model->where('id', '=', $this->model->id)->update(['value' => $fileName]);
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
        //$training_course_id = $this->trainingProfileCourse->pluck('course_id')->toArray();
        $path = public_path() . $this->fileUploadPath;

        $this->model = $this->model->find($id);

        if ($this->model->content_type_id == 1) {
            $path .= 'images/';
        }
        if ($this->model->content_type_id ==2) {
            $path .= 'pdf/';
        }
        if ($this->model->content_type_id==3) {
            $path .= 'video/';
        }

        $file_path = $path.$this->model->value;
        if (Storage::disk('s3')->exists($file_path)) {
            Storage::disk('s3')->delete($file_path);
        }

//        \File::delete($path . $this->model->value);
        return $this->model->destroy($id);
    }
    /**
     * Remove the specified training course from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteByCourseId($id)
    {
        $delete = 0;
        //$training_course_id = $this->trainingProfileCourse->pluck('course_id')->toArray();
        $path = public_path() . $this->fileUploadPath;

        $course_contents = $this->model->where('course_id', $id)->get();
        foreach ($course_contents as $course_content) {
            if ($course_content->content_type_id == 1) {
                $path .= 'images/';
            }
            if ($course_content->content_type_id ==2) {
                $path .= 'pdf/';
            }
            if ($course_content->content_type_id==3) {
                $path .= 'video/';
            }

            $file_path = $path.$course_content->value;
            if (Storage::disk('s3')->exists($file_path)) {
                Storage::disk('s3')->delete($file_path);
            }
//            \File::delete($path . $course_content->value);
             $delete = $this->model->where('id', $course_content->id)->delete();
        }
        return $delete;
    }
    /**
     * get content by course
     *
     * @param
     * @return object
     */
    public function getContentByCourse($id)
    {
        $course_content = $this->model->where('course_id', $id)->select('id', 'value', 'fast_forward', 'content_title', 'content_type_id')->orderBy('id', 'ASC')->get();
        return $course_content;
    }

    /**
     * get content by course
     *
     * @param
     * @return object
     */
    public function getNextContentByCourse($course_id, $id)
    {
        $course_content = $this->model->where('course_id', $course_id)->where('id', '>', $id)->select('id', 'value', 'fast_forward', 'content_title', 'content_type_id')->orderBy('id', 'ASC')->take(1)->get();
        return $course_content;
    }
    /**
     * Display Content list
     *
     * @param
     * @return object
     */
    public function getContentById($id)
    {
        $course_content = $this->model->where('course_id', $id)->select('id', 'value', 'fast_forward', 'content_title', 'content_type_id')->get();
        return $course_content;
    }
}
