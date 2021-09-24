<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\TrainingEmployeeProfileRequest;
use Modules\Admin\Models\TrainingCourse;
use Modules\Admin\Repositories\TrainingEmployeeProfileRepository;
use Spatie\Permission\Models\Role;

class TrainingEmployeeProfileController extends Controller
{

    /**
     * Repository instance.
     * @var \App\Repositories\TrainingEmployeeProfileRepository
     *
     */
    protected $repository, $helperService;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingEmployeeProfileRepository $trainingEmployeeProfileRepository
     * @return void
     */
    public function __construct(TrainingEmployeeProfileRepository $trainingEmployeeProfileRepository, HelperService $helperService)
    {
        $this->repository = $trainingEmployeeProfileRepository;
        $this->helperService = $helperService;
    }

    // /**
    //  * Load the Employee Profile Page
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index()
    // {
    //     $courses_list = TrainingCourse::orderBy('course_title')->pluck('course_title', 'id')->toArray();
    //     return view('admin::employee-profile.employee_profile', compact('courses_list'));
    // }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles_list = Role::orderBy('name')->pluck('name', 'id')->toArray();
        $courses_list = TrainingCourse::orderBy('course_title')->pluck('course_title', 'id')->toArray();
        return view('admin::employee-profile.employee-profile', compact('courses_list', 'roles_list'));
    }

    /**
     *Store Employee  Training Profile .
     *
     * @param Modules\Admin\Http\Requests\TrainingEmployeeProfileRequest $request
     * @return Json
     */
    public function store(TrainingEmployeeProfileRequest $request)
    {
        try {
            \DB::beginTransaction();
            $data = $this->repository->save($request);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }
    /**
     * Display a listing of Training Profile resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAllTrainingProfile())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->getTrainingProfile($id));
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
            $profile_delete = $this->repository->deleteTrainingProfile($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
