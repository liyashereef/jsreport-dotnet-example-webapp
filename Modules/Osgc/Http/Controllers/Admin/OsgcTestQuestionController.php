<?php

namespace Modules\Osgc\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Osgc\Repositories\OsgcTestQuestionsRepository;
use Modules\Osgc\Http\Requests\OsgcQuestionsRequest;
class OsgcTestQuestionController extends Controller
{
    protected $repository;
    protected $helperService;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\OsgcTestQuestionsRepository $trainingCourseRepository
     
     * @return void
     */
    public function __construct(HelperService $helperService,OsgcTestQuestionsRepository $osgcTestQuestionsRepository)
    {
        $this->repository=$osgcTestQuestionsRepository;
        $this->helperService = $helperService;
 
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id=null)
    {
        return view('osgc::admin.exam.exam-question-list',compact('id'));
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
    public function store(OsgcQuestionsRequest $request)
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
