<?php

namespace Modules\LearningAndTraining\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\LearningAndTraining\Http\Requests\CourseContentRequest;
use Modules\LearningAndTraining\Repositories\CourseContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\CourseContentTypeRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;


class CourseContentController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $trainingCourseRepository;
    protected $courseContentRepository;
    protected $courseContentTypeRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(CourseContentRepository $courseContentRepository, TrainingCourseRepository $trainingCourseRepository,
                                CourseContentTypeRepository $courseContentTypeRepository, HelperService $helperService,
                                 TrainingUserContentRepository $user_content_repo)
    {
        $this->repository = $courseContentRepository;
        $this->trainingCourseRepository = $trainingCourseRepository;
        $this->courseContentTypeRepository = $courseContentTypeRepository;
        $this->helperService = $helperService;
        $this->user_content_repo = $user_content_repo;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($id)
    {
        $course = $this->trainingCourseRepository->get($id);
        $courseList = $this->trainingCourseRepository->getList();
        $courseContentList = $this->courseContentTypeRepository->getList();
        return view('learningandtraining::admin.course.course-content', compact('courseList', 'courseContentList','course','id'));
    }

    /**
    * Display a listing of resources.
    *
    * @return \Illuminate\Http\Response
    */
    public function getListByCourse($course_id)
    {
        return datatables()->of($this->repository->getAllByCourseId($course_id))->addIndexColumn()->toJson();
    }

    /**
    * Display a listing of resources.
    *
    * @return \Illuminate\Http\Response
    */
    public function getList()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }


    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->get($id));
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('learningandtraining::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(CourseContentRequest $request)
    {
        try {
            \DB::beginTransaction();
            if (!$request->has('fast_forward')) {
                $request->merge(['fast_forward' => 0]);
            }
            $course = $this->repository->save($request->all());
            if($course){
                $this->user_content_repo->storeSingleContent($course->id);
            }
            if ($request->hasFile('course_file')) {
                $course = $this->repository->uploadFile($request->all(), $course);
            }
            \DB::commit();
            return response()->json(array('success' => 'true', 'data' => $course));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('learningandtraining::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('learningandtraining::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $course_delete = $this->repository->delete($id);
            if($course_delete != false){
                $this->user_content_repo->removeById($id);
            }
            \DB::commit();
            if ($course_delete == false) {
                return response()->json($this->helperService->returnFalseResponse());
            } else {
                return response()->json($this->helperService->returnTrueResponse());
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function getContentDetails($id){
        $content = $this->repository->get($id);
        return view('learningandtraining::admin.course.show-content',compact('content'));
    }
}
