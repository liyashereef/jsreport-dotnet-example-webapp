<?php

namespace Modules\LearningAndTraining\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\LearningAndTraining\Repositories\TrainingTestQuestionsRepository;
use Modules\LearningAndTraining\Http\Requests\TestQuestionsRequest;

class TrainingTestQuestionController extends Controller
{
    protected $repository;
    protected $helperService;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(HelperService $helperService,TrainingTestQuestionsRepository $trainingTestQuestionsRepository)
    {
        $this->repository=$trainingTestQuestionsRepository;
        $this->helperService = $helperService;
 
    }
 

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id=null)
    {
        return view('learningandtraining::exam.training-exam-question-list',compact('id'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getListQuestions($id)
    {

        return datatables()->of($this->repository->getExamQuestions($id))->addIndexColumn()->toJson();
    }

      /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TestQuestionsRequest $request)
    {
        try {
            \DB::beginTransaction();
            $questions_id = $this->repository->save($request->all());
            $saveOptions=$this->repository->optionSave($questions_id,$request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

     /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
     
    public function getSingleQuestion($id)
    {
        return response()->json($this->repository->getQuestion($id));
    }
    

     /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteQuestion($id)
    {   
        try {
            \DB::beginTransaction();
            $question = $this->repository->destroyQuestion($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }



 }