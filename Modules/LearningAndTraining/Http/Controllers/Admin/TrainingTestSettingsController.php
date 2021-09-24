<?php

namespace Modules\LearningAndTraining\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\LearningAndTraining\Repositories\TrainingTestSettingsRepository;
use Modules\LearningAndTraining\Http\Requests\TestSettingsRequest;
use Modules\LearningAndTraining\Repositories\TrainingTestQuestionsRepository;

class TrainingTestSettingsController extends Controller
{
    protected $repository;
    protected $helperService;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(HelperService $helperService,TrainingTestSettingsRepository $trainingTestSettingsRepository,TrainingTestQuestionsRepository $trainingTestQuestionsRepository)
    {
        $this->repository=$trainingTestSettingsRepository;
        $this->helperService = $helperService;
        $this->trainingTestQuestionsRepository=$trainingTestQuestionsRepository;
 
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id=null)
    {
        return view('learningandtraining::exam.training-test-settings-list',compact('id'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList($id)
    {
        return datatables()->of($this->repository->getExamSettings($id))->addIndexColumn()->toJson();
    }
 

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function showQuestions($id=null)
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
    public function storeSettings(TestSettingsRequest $request)
    {	
        try {
            \DB::beginTransaction();
            $settings = $this->repository->save($request->all());
             $set_status=$this->repository->updateStatus($settings);
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
    public function getSingleSetting($id)
    {
        return response()->json($this->repository->getSettings($id));
    }
    

     /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteSettings($id)
    {   
        try {
            \DB::beginTransaction();
            $settings = $this->repository->destroySetting($id);
            $questions=$this->trainingTestQuestionsRepository->deleteQuestions($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }



 }