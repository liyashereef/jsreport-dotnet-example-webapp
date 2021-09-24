<?php

namespace Modules\LearningAndTraining\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\LearningAndTraining\Http\Requests\TrainingCourseRequest;
use Modules\LearningAndTraining\Repositories\CourseContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingCategoryRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\TrainingTeamCourseAllocationRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;

class TrainingCourseController extends Controller
{
    protected $repository;
    protected $helperService;
    protected $trainingCategoryRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(TrainingCourseRepository $trainingCourseRepository, TrainingCategoryRepository $trainingCategoryRepository,
                                HelperService $helperService,TrainingTeamCourseAllocationRepository $team_course_repo,
                                TrainingUserContentRepository $user_content_repo, CourseContentRepository $content_repo)
    {
        $this->repository = $trainingCourseRepository;
        $this->trainingCategoryRepository = $trainingCategoryRepository;
        $this->helperService = $helperService;
        $this->team_course_repo = $team_course_repo;
        $this->user_content_repo = $user_content_repo;
        $this->content_repo = $content_repo;
        $this->user_course_allocation_repo = new TrainingUserCourseAllocationRepository();
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryList = $this->trainingCategoryRepository->getList();
        return view('learningandtraining::admin.course.training-course', compact('categoryList'));
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
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\TrainingCourseRequest $request
     * @return json
     */
    public function store(TrainingCourseRequest $request)
    {
        try {
            \DB::beginTransaction();
            if (!$request->has('status')) {
                $request->merge(['status' => 0]);
            }
            if (!$request->has('add_to_course_library')) {
                $request->merge(['add_to_course_library' => 0]);
            }
            $course = $this->repository->save($request->all());
            if ($request->hasFile('course_image')) {
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
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */

    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
                $this->team_course_repo->deleteTeamCoursesByCourseId($id);
                $this->user_content_repo->removeByCourseId($id);
                $inputs['course_id'] = $id;
                $this->user_course_allocation_repo->teamUnallocation($inputs);
                $this->content_repo->deleteByCourseId($id);
                $course_delete = $this->repository->delete($id);
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
}
