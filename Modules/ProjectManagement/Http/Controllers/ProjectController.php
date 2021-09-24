<?php

namespace Modules\ProjectManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use Modules\ProjectManagement\Http\Requests\ProjectRequest;
use Modules\ProjectManagement\Repositories\GroupRepository;
use Modules\ProjectManagement\Repositories\ProjectRepository;
use Modules\ProjectManagement\Repositories\TaskFollowerConfigurationRepository;
use Modules\ProjectManagement\Repositories\TaskRepository;

class ProjectController extends Controller
{
    protected $helperService, $repository, $taskFollowerConfigurationRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @var \Modules\Admin\Repositories\WorkTypeRepository $workTypeRepository;
     * @return void
     */
    public function __construct(
        HelperService $helperService,
        ProjectRepository $projectRepository,
        CustomerRepository $customerRepository,
        GroupRepository $groupRepo,
        TaskRepository $taskRepo,
        EmployeeRatingLookupRepository $ratingRepo,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        TaskFollowerConfigurationRepository $taskFollowerConfigurationRepository

    ) {
        $this->helperService = $helperService;
        $this->repository = $projectRepository;
        $this->customerRepository = $customerRepository;
        $this->groupRepo = $groupRepo;
        $this->taskRepo = $taskRepo;
        $this->ratingRepo = $ratingRepo;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->taskFollowerConfigurationRepository = $taskFollowerConfigurationRepository;
    }

    /**
     * Display a listing of the Project.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('create_task_all_customer')) {
            $customers = $this->customerRepository->getCustomerList();
        } else if (\Auth::user()->can('create_task_allocated_customer')) {
            $allocated_customers = $this->customerEmployeeAllocationRepository->getDirectAllocatedCustomers(\Auth::user());
            $customers = $this->customerRepository->getCustomerList('PERMANENT_CUSTOMER', 'ACTIVE', $allocated_customers);
        }
        $ratings = $this->ratingRepo->getList();
        $followerConfigurationValue = 0;
        $followerConfiguration = $this->taskFollowerConfigurationRepository->getFirstData();
        if (!empty($followerConfiguration)) {
            $followerConfigurationValue = $followerConfiguration->value;
        }
        return view('projectmanagement::project', compact('customers', 'ratings', 'followerConfigurationValue'));
    }

    /**
     * Store  newly created  Project in storage.
     *
     * @param  Modules\Admin\Http\Requests\WorkTypeRequest $request
     * @return Json
     */
    public function store(ProjectRequest $request)
    {
        try {
            DB::beginTransaction();
            $project = $this->repository->save($request->all());
            $task_update = $this->taskRepo->updateCustomer($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * List all Project in datatable.
     *
     *
     * @return Json
     */
    function list(Request $request) {
        $client_id=$request->client_id;
        if (\Auth::user()->can('create_task_all_customer')) {
            $allocated_customers = null;
        } else if (\Auth::user()->can('create_task_allocated_customer')) {
            $allocated_customers = $this->customerEmployeeAllocationRepository->getDirectAllocatedCustomers(\Auth::user());
        }
        return datatables()->of($this->repository->getAll($allocated_customers, $client_id))->addIndexColumn()->toJson();
    }

    /**
     * Show the form for editing the specified Project.
     *
     * @param  $id
     * @return Json
     */
    public function show($id)
    {
        return response()->json($this->repository->get($id));
    }

    /**
     * Get all users of a project.
     */
    public function usersList($id)
    {
        $users = [];
        //Get users by project
        if (!empty($id)) {
            $users = $this->repository->getUsersOfProject($id);
        }
        //Return users

        return response()->json($users);
    }

    /**
     * Remove the specified Project from storage.
     *
     * @param  $id
     * @return Json
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $getAllRelatedData = $this->repository->get($id);
            $relatedGroupsId = $getAllRelatedData->groups->pluck('id')->toArray();
            $this->groupRepo->deleteGroup($relatedGroupsId);
            $relatedtaskListId = $getAllRelatedData->taskList->pluck('id')->toArray();
            $this->taskRepo->deleteTask($relatedtaskListId);
            $status = $this->repository->deleteProject($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
