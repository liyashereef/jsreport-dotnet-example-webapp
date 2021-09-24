<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Modules\Admin\Http\Requests\TrainingSiteProfileRequest;
use Modules\Admin\Models\TrainingCourse;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\TrainingSiteProfileRepository;

class TrainingSiteProfileController extends Controller
{

    /**
     * Repository instance.
     * @var \App\Repositories\TrainingSiteProfileRepository
     * @var \App\Repositories\CustomerRepository
     *
     */
    protected $repository, $helperService, $customerRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\TrainingSiteProfileRepository $trainingSiteProfileRepository
     * @param  \App\Repositories\CustomerRepository $customerRepository
     * @return void
     */
    public function __construct(TrainingSiteProfileRepository $trainingSiteProfileRepository, HelperService $helperService, CustomerRepository $customerRepository)
    {
        $this->repository = $trainingSiteProfileRepository;
        $this->customerRepository = $customerRepository;
        $this->helperService = $helperService;
    }

    /**
     * Load the resource listing Page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses_list = TrainingCourse::orderBy('course_title')->pluck('course_title', 'id')->toArray();
        $customer_list = $this->customerRepository->getCustomersNameList()->sortBy('client_name')->pluck('client_name', 'id')->toArray();
        return view('admin::site-profile.site-profile', compact('courses_list', 'customer_list'));
    }

    /**
     *Store Training Site Profile .
     *
     * @param Modules\Admin\Http\Requests\TrainingSiteProfileRequest $request
     * @return Json
     */
    public function store(TrainingSiteProfileRequest $request)
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
     * Display a listing of Training Site Profile resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getList()
    {
        return datatables()->of($this->repository->getAllTrainingSiteProfile())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getSingle($id)
    {
        return response()->json($this->repository->getTrainingSiteProfile($id));
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
            $profile_delete = $this->repository->deleteTrainingSiteProfile($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
