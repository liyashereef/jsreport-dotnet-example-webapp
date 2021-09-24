<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\TrainingCourseRequest;
use Modules\Admin\Repositories\TrainingCategoryRepository;
use Modules\Admin\Repositories\TrainingCourseRepository;

class TrainingCourseController extends Controller
{
    protected $repository, $helperService, $trainingCategoryRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingCourseRepository $trainingCourseRepository
     * @param  \App\Repositories\TrainingCategoryRepository $trainingCategoryRepository
     * @return void
     */
    public function __construct(TrainingCourseRepository $trainingCourseRepository, TrainingCategoryRepository $trainingCategoryRepository, HelperService $helperService)
    {
        $this->repository = $trainingCourseRepository;
        $this->trainingCategoryRepository = $trainingCategoryRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryList = $this->trainingCategoryRepository->getList();
        return view('admin::masters.training-course', compact('categoryList'));
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
            $course = $this->repository->save($request->all());
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
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return json
     */

    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
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
