<?php

namespace Modules\LearningAndTraining\Repositories;

use App\Services\HelperService;
use Modules\LearningAndTraining\Models\TestUserResult;
use Modules\LearningAndTraining\Models\TestCourseMaster;
use Modules\LearningAndTraining\Repositories\TestUserAttemptedQuestionRepository;

class TestUserResultRepository
{
   /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$testCourseMaster, $testUserAttemptRepo;
    protected $helperService;

    /**
     * Create a new TrainingCourseLookupRepository instance.
     *
     * @param  \App\Models\TrainingCourse $trainingCourse
     */
    public function __construct(
        TestUserResult $model,
        TestCourseMaster $testCourseMaster,
        TestUserAttemptedQuestionRepository $testUserAttemptRepo,
        HelperService $helperService
    ) {
        $this->model = $model;
        $this->testCourseMaster = $testCourseMaster;
        $this->testUserAttemptRepo = $testUserAttemptRepo;
        $this->helperService = $helperService;
    }

    /**
     * Get all attempt count by user_id
     *
     * @param empty
     * @return count
     */
    public function getAllAttemptCountByUserId($userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->count();
    }


    /**
     * Get data by id
     *
     * @param id
     * @return object
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

     /**
     * When question listing store user exam details and questions
     *
     * @param $inputs['test_course_master_id'] , $inputs['test_course_question_ids'] = array;
     * @return
     */

    public function storeTestAttempt($inputs)
    {
        $test_user_result_id = '';
        try {
            \DB::beginTransaction();
                $userResult = [];
                $courseQuestion = [];
            if (!empty($inputs)) {
                $testCourseMaster = $this->testCourseMaster->find($inputs['test_course_master_id']);

                if (!empty($testCourseMaster)) {
                    $userResult['test_course_master_id'] = $inputs['test_course_master_id'];
                    $userResult['course_pass_percentage'] = $testCourseMaster->pass_percentage;
                    if((isset($inputs['training_user_id'])) && ($inputs['training_user_id'] != null )){
                        $userResult['training_user_id'] = $inputs['training_user_id'];
                    }else{
                        $userResult['user_id'] = \Auth::user()->id;
                    }
                    $userResult['training_course_id'] = $testCourseMaster->training_course_id;
                    // $userResult['total_questions'] = $testCourseMaster->number_of_question;
                    $userResult['total_questions'] = sizeof($inputs['test_course_question_ids']);

                    $result = $this->model->create($userResult);

                    if (!empty($result)) {
                        $test_user_result_id = $result->id;
                        $courseQuestion['test_user_result_id'] = $result->id;

                        foreach ($inputs['test_course_question_ids'] as $value) {
                            $courseQuestion['test_course_question_id'] =  $value;
                            $this->testUserAttemptRepo->store($courseQuestion);
                        }
                    }
                    $message = 'Exam attempt successfully created';
                } else {
                    $message = 'Course master not found';
                }
            } else {
                $message = 'Params not found';
            }
            \DB::commit();
            return response()->json(array('success' => true, 'message' => $message,'test_user_result_id'=>$test_user_result_id));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false,'test_user_result_id'=>$test_user_result_id, 'message' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * User exam draft availability checking.
     * Exam set has only 1 day validity. After expiry status will change to close(3)
     *
     * @param $inputs['test_course_master_id'];
     * @return exam_id, message
     */

    public function isDraftExists($inputs)
    {

        $message = 'Draft Not Fount';
        $return = '';
        try {
            \DB::beginTransaction();

                $isExists = $this->model
                ->where('test_course_master_id', $inputs['test_course_master_id'])
                ->where('user_id', \Auth::user()->id)
                ->where('status', 0)
                ->orderBy('id', 'DESC')
                ->first();

                $inputs['exclude_id']='';

            if (!empty($isExists)) {
                $validityUpTo = \Carbon::parse($isExists->created_at)->addDay();
                if ($validityUpTo >= \Carbon::now()) {
                    $message = 'Draft  Fount';
                    $return = $isExists;

                    //Removing this IDs while the change to close status
                    $inputs['exclude_id']=$isExists->id;
                }
            }
                //Update status from Draft to Close
                $this->updateStatusToClose($inputs);

            \DB::commit();
            return response()->json(array('success' => true,'data'=>$return, 'message' => $message));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false,'data'=>'', 'message' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

     /**
     * Update exam set status to close from draft.
     * Question set has only 1 day validity. After expiry status will change to close(3)
     *
     * @param $inputs['test_course_master_id','exclude_id'];
     * exclude_id = These IDs will remove from status change.
     *
     * @return
     */

    public function updateStatusToClose($inputs)
    {
        return $this->model
        ->where('test_course_master_id', $inputs['test_course_master_id'])
        ->where('user_id', \Auth::user()->id)
        ->where('status', 0)
        ->when($inputs['exclude_id'], function ($q) use ($inputs) {
            return $q->where('id', '!=', $inputs['exclude_id']);
        })
        ->update(['status'=>2]);
    }

    /**
     * Fetch all data based on Filters.
     *
     * @param $inputs['test_course_master_id','status','ids','user_id','last_one'];
     *
     * @return
     */

    public function getAllBasedOnFilters($inputs)
    {
        $query = $this->model;

        $query=$query->when(isset($inputs['test_course_master_id']), function ($query) use ($inputs) {
             $query->where('test_course_master_id', $inputs['test_course_master_id']);
        });

        $query=$query->when(isset($inputs['status']), function ($query) use ($inputs) {
             $query->where('status', $inputs['status']);
        });

        $query=$query->when(isset($inputs['ids']), function ($query) use ($inputs) {
            if (is_array($inputs['ids'])) {
                 $query->whereIn('id', $inputs['ids']);
            } else {
                 $query->where('id', $inputs['ids']);
            }
        });

         $query=$query->when(isset($inputs['training_user_id']), function ($query) use ($inputs) {
             $query->where('training_user_id', $inputs['training_user_id']);
         });

         $query=$query->when(isset($inputs['user_id']), function ($query) use ($inputs) {
            $query->where('user_id', $inputs['user_id']);
        });

        $query=$query->when(isset($inputs['training_course_id']), function ($query) use ($inputs) {
             $query->where('training_course_id', $inputs['training_course_id']);
        });

        $query=$query->when(isset($inputs['is_exam_pass']), function ($query) use ($inputs) {
             $query->where('is_exam_pass', $inputs['is_exam_pass']);
        });

        $query=$query->when(isset($inputs['status']), function ($query) use ($inputs) {
             $query->where('status', $inputs['status']);
        });

        if (!empty($inputs) && isset($inputs['last_one']) && $inputs['last_one'] == true) {
            return $query->orderBy('id', 'DESC')->first();
        } else {
            return $query->get();
        }
    }



    public function getStatus($course_id, $master_id)
    {
        return $this->model
           ->where('user_id', \Auth::user()->id)
           ->where('training_course_id', $course_id)
           ->where('test_course_master_id', $master_id)
           ->where('status', '!=', 2)
           ->first();
    }


    public function submitExam($id, $inputs)
    {
        return $this->model->where('id', $id)->update($inputs);
    }

    public function getResultByCourseId($courseId, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = \Auth::id();
            $column = 'user_id';
        }
        return $this->model->where('training_course_id', $courseId)
        ->where('is_exam_pass', true)
        ->where($column, $user_id)
        ->first();
    }

    public function previousAttemptExist($courseId, $training_user_id = null)
    {
        if ($training_user_id!=null) {
            $user_id = $training_user_id;
            $column = 'training_user_id';
        } else {
            $user_id = \Auth::id();
            $column = 'user_id';
        }
        return $this->model->where('training_course_id', $courseId)
        ->where('status', '1')
        ->where($column, $user_id)
        ->exists();
    }
}
