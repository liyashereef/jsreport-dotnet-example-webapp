<?php


namespace Modules\LearningAndTraining\Repositories;

use Modules\LearningAndTraining\Models\CourseContent;
use Modules\LearningAndTraining\Models\Team;
use Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation;
use Modules\LearningAndTraining\Models\TrainingUserContent;

class TrainingUserContentRepository
{

    protected $user_content;

    /**
     * Create a new Training User Content instance.
     *
     * @param  Modules\LearningAndTraining\Models\TrainingUserContent $user_content
     */
    public function __construct()
    {
        $this->model = new TrainingUserContent();
        $this->course_content = new CourseContent();
        $this->course_allocation_repo = new TrainingUserCourseAllocationRepository();
    }

    public function store($inputs)
    {
        $insert = 0;
        $user_contents = 0;
        $contents = $this->course_content->where('course_id', $inputs['course_id'])->get();
        if (isset($contents) && sizeof($contents) >= 1) {
            foreach ($contents as $content) {
                if (isset($inputs['user_id'])) {
                    $column='user_id';
                } else {
                    $column='training_user_id';
                }
                $user_contents = $this->getByUserIdAndContentId($inputs[$column], $content->id);
                if ($user_contents == 0) {
                    $inputs['course_content_id'] = $content->id;
                    $insert = $this->model->create($inputs);
                }
            }
        }

        return $insert;
    }

    public function restoreDeletedContent($inputs)
    {
        $insert = 0;
        $user_contents = 0;
        $contents = $this->course_content->where('course_id', $inputs['course_id'])->get();
        if (isset($contents) && sizeof($contents) >= 1) {
            foreach ($contents as $content) {
                if (isset($inputs['user_id'])) {
                    $column='user_id';
                } else {
                    $column='training_user_id';
                }
                $user_contents = $this->getByUserIdAndContentIdWithTrashed($inputs[$column], $content->id);
                if (empty($user_contents)) {
                    $inputs['course_content_id'] = $content->id;
                    $insert = $this->model->create($inputs);
                }else{
                    $insert =$this->model->withTrashed()
                    ->where('course_content_id', $user_contents->course_content_id)
                    ->where($column, $inputs[$column])
                    ->restore();
                }
            }
        }

        return $insert;
    }

    public function storeSingleContent($content_id)
    {
        $return = 0;
        $content = $this->course_content->find($content_id);
        $user_contents = $this->course_allocation_repo->userAllAllocationByCourseId($content->course_id);
        if (isset($user_contents) && sizeof($user_contents) >= 1) {
            foreach ($user_contents as $user_content) {
                $inputs['user_id'] = $user_content->user_id;
                $inputs['course_content_id'] = $content_id;
                $return = $this->contentAllocation($inputs);
            }
        }

        return $return;
    }

    public function contentAllocation($inputs)
    {
        return $this->model->create($inputs);
    }

    public function getByUserId($user_id)
    {
        return $this->model->where('user_id', $user_id)->get();
    }

    public function getByUserIdAndContentId($user_id, $course_content_id)
    {
        return $this->model->where('user_id', $user_id)->where('course_content_id', $course_content_id)->count();
    }

    public function getDataByUserIdAndContentId($user_id, $course_content_id)
    {
        return $this->model->where('user_id', $user_id)->where('course_content_id', $course_content_id)->first();
    }

    public function getByUserIdAndContentIdWithTrashed($user_id, $course_content_id)
    {
        return $this->model->where('user_id', $user_id)
        ->where('course_content_id', $course_content_id)
        ->orderBy('completed','DESC')
        ->withTrashed()
        ->first();
    }

    public function removeAllByUserId($user_id)
    {
        return $this->model->where('user_id', $user_id)->delete();
    }

    public function removeByCourseIdAndUserId($inputs)
    {
        return $this->model->where('user_id', $inputs['user_id'])
//            ->where('course_id',$inputs['course_id'])
            ->whereHas('course_content', function ($query) use ($inputs) {
                $query->where('course_id', $inputs['course_id']);
            })->delete();
    }

    public function removeByCourseId($course_id)
    {
        return $this->model->whereHas('course_content', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
        })->delete();
    }
    public function removeById($content_id)
    {
        return $this->model->where('course_content_id', $content_id)->delete();
    }
    public function save($data, $training_user_id = null)
    {
        if ($training_user_id==null) {
            $column='user_id';
            $user_id= \Auth::User()->id;
        } else {
            $column='training_user_id';
            $user_id= $training_user_id;
        }
        $data[$column] = $user_id;
        $data['completed_percentage'] = 100;
         $data['completed_date'] = \Carbon::now();
         $result = $this->model->updateOrCreate(array('course_content_id' => $data['content_id'],$column => $user_id), $data);
        $created = $result->wasRecentlyCreated;
        $result = $result->fresh();
        $result['created'] = $created;
        return $result;
    }
    public function contentCompletion($contentId, $request)
    {
        $employee_id=$request->employee_array;
        $column=isset($is_training_user)?'training_user_id':'user_id';
        foreach ($contentId as $key => $each_content) {
            foreach ($employee_id as $key => $each_employee) {
                $result=$this->model->where('course_content_id', $each_content)->where($column, $each_employee)->update(['completed'=>1,'completed_percentage'=>100,'completed_date'=>\Carbon::now()]);
            }
        }
        return $result;
    }
}
