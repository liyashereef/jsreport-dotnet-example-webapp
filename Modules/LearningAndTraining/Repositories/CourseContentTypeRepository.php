<?php

namespace Modules\LearningAndTraining\Repositories;

use Modules\LearningAndTraining\Models\TrainingCategory;
use Modules\LearningAndTraining\Models\CourseContentType;

class CourseContentTypeRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $trainingCategoryModel;

    /**
     * Create a new TrainingCategoryLookupRepository instance.
     *
     * @param  \App\Models\TrainingCategory $trainingCategory
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(TrainingCategory $trainingCategoryModel, CourseContentType $courseContentType)
    {
        $this->model = $courseContentType;
        $this->trainingCategoryModel = $trainingCategoryModel;
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderby('course_category', 'asc')->select(['id', 'course_category', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get training category list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderby('type', 'asc')->pluck('type', 'id')->toArray();
    }

    /**
     * Display details of single training category
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created training category in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified training category from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $training_category_id = $this->trainingCourseModel->pluck('training_category_id')->toArray();
        if (in_array($id, $training_category_id)) {
            return false;
        } else {
            return $this->model->destroy($id);
        }

    }
}
