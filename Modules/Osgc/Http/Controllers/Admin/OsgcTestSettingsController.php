<?php

namespace Modules\Osgc\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\Osgc\Repositories\OsgcTestSettingsRepository;
use Modules\Osgc\Http\Requests\OsgcTestSettingsRequest;
use Modules\Osgc\Repositories\OsgcTestQuestionsRepository;
use Modules\Osgc\Repositories\OsgcCourseLookupRepository;
class OsgcTestSettingsController extends Controller
{
    protected $repository;
    protected $helperService;
    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(HelperService $helperService,OsgcTestSettingsRepository $osgcTestSettingsRepository,OsgcTestQuestionsRepository $osgcTestQuestionsRepository,OsgcCourseLookupRepository $osgcCourseLookupRepository)
    {
        $this->repository=$osgcTestSettingsRepository;
        $this->helperService = $helperService;
        $this->osgcTestQuestionsRepository=$osgcTestQuestionsRepository;
        $this->osgcCourseLookupRepository=$osgcCourseLookupRepository;
 
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id=null)
    {
        $result=$this->osgcCourseLookupRepository->get($id);
        $courseHeadings=$this->osgcCourseLookupRepository->getCourseHeadingList($id);
        $sectionContents=$this->osgcCourseLookupRepository->getSectionList($id);
        return view('osgc::admin.exam.index',compact('id','sectionContents','courseHeadings','result'));
    }

    /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList($courseId)
    {
        return datatables()->of($this->repository->getExamSettings($courseId))->addIndexColumn()->toJson();
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
    public function storeSettings(OsgcTestSettingsRequest $request)
    {	
        try {
            \DB::beginTransaction();
            $settings = $this->repository->save($request->all());
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
            $questions=$this->osgcTestQuestionsRepository->deleteQuestions($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

}
