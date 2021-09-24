<?php

namespace Modules\ProjectManagement\Http\Controllers;

use App\Services\HelperService;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\ProjectManagement\Repositories\GroupRepository;
use Modules\ProjectManagement\Repositories\ProjectManagementRepository;
use Modules\ProjectManagement\Repositories\ProjectRepository;
use Modules\ProjectManagement\Repositories\TaskFollowerConfigurationRepository;
use Modules\ProjectManagement\Repositories\TaskOwnerRepository;
use Modules\ProjectManagement\Repositories\TaskRepository;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;

class ProjectManagementController extends Controller
{

    /**
     * The Repository instance.
     *
     * @var \App\Repositories\CustomerRepository
     * @var \App\Services\HelperService
     */
    protected $helperService, $projectManagementRepository, $customerRepository, $taskOwnerRepository, $taskFollowerConfigurationRepository;

    /**
     * Create Repository instance.
     *
     * @param  \App\Repositories\CustomerRepository $customerRepository
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(
        HelperService $helperService,
        ProjectManagementRepository $projectManagementRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        EmployeeAllocationRepository $employeeAllocationRepository,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        CustomerRepository $customerRepository,
        GroupRepository $groupRepository,
        TaskOwnerRepository $taskOwnerRepository,
        TaskFollowerConfigurationRepository $taskFollowerConfigurationRepository,
        EmployeeRatingLookupRepository $ratingRepo
    ) {
        $this->helperService = $helperService;
        $this->projectManagementRepository = $projectManagementRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->taskOwnerRepository = $taskOwnerRepository;
        $this->taskFollowerConfigurationRepository = $taskFollowerConfigurationRepository;
        $this->ratingRepo = $ratingRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($taskId = null)
    {
        $args = [];
        if (\Auth::user()->can('view_all_reports')) {
            $args['customers'] = $this->customerRepository->getList();
        } else {
            $args['customers'] = $this->customerEmployeeAllocationRepository->getDirectAllocatedCustomersList(\Auth::user());
        }
        if (!empty(array_keys($args['customers']))) {
            $employees_list = $this->customerEmployeeAllocationRepository->allocationList(array_keys($args['customers']))->pluck('full_name', 'id')->toArray();
            $key_exists = array_key_exists(\Auth::user()->id, $employees_list);
            if (!$key_exists) {
                $employees_list[\Auth::user()->id] = \Auth::user()->full_name;
                asort($employees_list);
            }
            $args['employees'] = $employees_list;
        } else {
            $args['employees'] = [];
        }
        if ((!\Auth::user()->can('view_all_reports')) && (!\Auth::user()->can('view_allocated_customer_reports')) && (\Auth::user()->can('view_assigned_reports'))) {
            $auth_user[\Auth::user()->id] = \Auth::user()->full_name;
            $args['employees'] = $auth_user;
        }
        $args['projects'] = $this->projectRepository->getAsArray(array_keys($args['customers']));
        $taskDetails = $this->taskRepository->getByTaskUniqueId($taskId);
        $args['site_id'] = $taskDetails['site_id'];
        $args['project_id'] = $taskDetails['project_id'];

        $args['followerConfigurationValue'] = 0;
        $followerConfiguration = $this->taskFollowerConfigurationRepository->getFirstData();
        if (!empty($followerConfiguration)) {
            $args['followerConfigurationValue'] = $followerConfiguration->value;
        }
        $ratings = $this->ratingRepo->getList();
        return view('projectmanagement::project-report', $args, compact('ratings'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('projectmanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('projectmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('projectmanagement::edit');
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
    public function destroy()
    {
    }

    public function dueDateNotification()
    {
        try {
            \DB::beginTransaction();
            $result = $this->projectManagementRepository->sendNotification();
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /*
     * api wih filter
     * @param  id, string
     * @return boolean
     */

    public function getProjectDetails(Request $request)
    {
        try {
            $result = $this->projectManagementRepository->getDataBasedOnPermissions($request);

            // $result = $this->projectManagementRepository->getAllfilteredData($project_id, $customer_id, $user_id, $startdate, $enddate, $status_from,$status_to);
            $content['data'] = $result;
            $content['success'] = true;
            $code = 200;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $code = 406;
        }
        return response()->json($content, $code);
    }

    public function getPerformanceReport()
    {
        $current = Carbon::now();
        $enddate = $current->toDateString();
        $startdate = $current->addDays(-2)->toDateString();
        if (\Auth::user()->can('view_all_performance_reports')) {
            $customer_list = $this->customerRepository->getList();
        } else {
            $customer_list= $this->customerEmployeeAllocationRepository->getDirectAllocatedCustomersList(\Auth::user());
        }
        if (!empty($customer_list)) {
            $employees_list = $this->customerEmployeeAllocationRepository->allocationList(array_keys($customer_list))->pluck('full_name', 'id')->toArray();
            $key_exists = array_key_exists(\Auth::user()->id, $employees_list);
            if (!$key_exists) {
                $employees_list[\Auth::user()->id] = \Auth::user()->full_name;
                asort($employees_list);
            }
            $user_list= $employees_list;
        } else {
            $user_list = [];
        }
        $project_list = $this->projectRepository->getProjectNamesForPerformanceReport(array_keys($customer_list));
        $group_list = $this->groupRepository->getGroupNames($project_list);
        return view('projectmanagement::project-rating', compact('current', 'enddate', 'startdate', 'user_list', 'project_list', 'group_list'));
    }

    public function getRatinglist(Request $request)
    {
        $enddate = isset($request->enddate) ? $request->enddate : null;
        $startdate = isset($request->startdate) ? $request->startdate : null;
        $project_id = isset($request->project_id) ? $request->project_id : null;
        $group_id = isset($request->group_id) ? $request->group_id : null;
        $emp_id = isset($request->group_id) ? $request->emp_id : null;
        $data = $this->taskRepository->getRatings($startdate, $enddate, $project_id, $group_id, $emp_id);
        return datatables()
            ->eloquent($data)
            ->setTransformer(function ($item) {
                $taskId = (int) $item->id;
                $taskOwnerNames = $this->taskOwnerRepository->fetchTaskOwnerVsFollowerName($taskId);
                $taskFollowerNames = $this->taskOwnerRepository->fetchTaskOwnerVsFollowerName($taskId, true);

                $assignee = "";
                if ($taskOwnerNames != "") {
                    $taskOwnerNames = str_replace(',', '<br />', $taskOwnerNames);
                    $assignee .= "<span class='assignee'>Task Owner</span><br />" . $taskOwnerNames . "<br />";
                }

                if ($taskFollowerNames != "") {
                    $taskFollowerNames = str_replace(',', '<br />', $taskFollowerNames);
                    $assignee .= "<span class='assignee'>Task Follower</span><br />" . $taskFollowerNames;
                }

                return [
                    'id' => $taskId,
                    'task_name' => $item->name,
                    'group' => $item->groupDetails->name,
                    'deadline_rating_id' => isset($item->deadline_rating_id) ? $item->deadline_rating_id : '--',
                    'value_add_rating_id' => isset($item->value_add_rating_id) ? $item->value_add_rating_id : '--',
                    'initiative_rating_id' => isset($item->initiative_rating_id) ? $item->initiative_rating_id : '--',
                    'commitment_rating_id' => isset($item->commitment_rating_id) ? $item->commitment_rating_id : '--',
                    'complexity_rating_id' => isset($item->complexity_rating_id) ? $item->complexity_rating_id : '--',
                    'efficiency_rating_id' => isset($item->efficiency_rating_id) ? $item->efficiency_rating_id : '--',
                    'rating_notes' => isset($item->rating_notes) ? $item->rating_notes : '--',
                    'project' => isset($item->projects) ? $item->projects->name : '--',
                    'assignee' => $assignee,
                    'created_at' => date_format(date_create($item->created_at), "j-M-y"),
                    'due_date' => date_format(date_create($item->due_date), "j-M-y"),
                    'rating_date' => isset($item->rated_at) ? date_format(date_create($item->rated_at), "j-M-y") : '--',
                    'average' => isset($item->average_rating) ? number_format((float) $item->average_rating, 2, '.', '') : '--',
                    'rating' => isset($item->average_rating) ? $this->taskRepository->getScoreRating($item->average_rating) : '--',
                ];
            })
            ->addIndexColumn()
            ->toJson();
    }
}
