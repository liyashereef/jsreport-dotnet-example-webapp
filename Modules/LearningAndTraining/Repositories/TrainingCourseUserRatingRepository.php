<?php


namespace Modules\LearningAndTraining\Repositories;

use Modules\LearningAndTraining\Models\TrainingCourseUserRating;
use Modules\LearningAndTraining\Models\TrainingUserContent;

class TrainingCourseUserRatingRepository
{

    protected $user_content;

    /**
     * Create a new Training User Content instance.
     *
     * @param  Modules\LearningAndTraining\Models\TrainingUserContent $user_content
     */
    public function __construct()
    {
        $this->model = new TrainingCourseUserRating();
    }

    public function save($data, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = \Auth::User()->id;
            $column = 'user_id';
        }
        $data[$column] = $user_id;
        $result = $this->model->updateOrCreate(array($column => $data[$column],'course_id' => $data['course_id']), $data);
        $created = $result->wasRecentlyCreated;
        $result = $result->fresh();
        $result['created'] = $created;
        return $result;
    }

    public function getRatingByCourseId($course_id)
    {
        return $this->model->where('course_id', $course_id)
            ->avg('rating');
    }
}
