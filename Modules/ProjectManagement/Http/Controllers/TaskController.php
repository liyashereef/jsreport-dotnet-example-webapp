<?php

namespace Modules\ProjectManagement\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\EmployeeRatingLookupRepository;
use Modules\ProjectManagement\Http\Requests\TaskRequest;
use Modules\ProjectManagement\Repositories\GroupRepository;
use Modules\ProjectManagement\Repositories\TaskOwnerRepository;
use Modules\ProjectManagement\Repositories\TaskRepository;
use Modules\ProjectManagement\Repositories\TaskStatusRepository;
use Carbon\Carbon;
use Modules\ProjectManagement\Http\Requests\TaskRatingRequest;

class TaskController extends Controller
{

    protected $helperService;
    protected $repository;
    protected $groupRepo;
    protected $empRatingRepo;
    protected $statusRepo;
    protected $taskOwnerRepo;

    public function __construct(
        HelperService $helperService,
        TaskRepository $taskRepository,
        GroupRepository $groupRepo,
        EmployeeRatingLookupRepository $empRatingRepo,
        TaskStatusRepository $statusRepo,
        TaskOwnerRepository $taskOwnerRepo
    ) {
        $this->helperService = $helperService;
        $this->repository = $taskRepository;
        $this->groupRepo = $groupRepo;
        $this->empRatingRepo = $empRatingRepo;
        $this->statusRepo = $statusRepo;
        $this->taskOwnerRepo = $taskOwnerRepo;
    }

    public function groupTasks($groupId)
    {
        return datatables()->of($this->repository->getByGroup($groupId))->addIndexColumn()->toJson();
    }

    public function show($id)
    {
        return response()->json($this->repository->get($id));
    }

    public function store(TaskRequest $request)
    {
        try {
            $input = $request->all();
            //Group find parent hierarchy
            if (isset($input['group_id']) && !empty($input['group_id'])) {
                $group = $this->groupRepo->get($input['group_id']);
                if (is_object(($group))) {
                    $input['site_id'] = $group->projectDetails->customer_id;
                    $input['project_id'] = $group->projectDetails->id;
                }
            }
            //Created by
            $input['created_by'] = auth()->user()->id;
            if ($input['id'] == null) {
                $input['unique_key'] = md5(microtime() . rand());
            }
            //Status
            $input['is_completed'] = isset($input['is_completed']) ? $input['is_completed'] : 0;
            $input['completed_date'] = (isset($input['is_completed']) && $input['is_completed']==1 ) ? Carbon::now() : null;

            //follower update enable or disable
            $input['followers_can_update'] = isset($input['followers_can_update']) ? 1 : 0;

            $taskOwners = [];
            if (isset($input['assigned_to'])) {
                $taskOwners = $input['assigned_to'];
                unset($input['assigned_to']);
            }

            $followers = [];
            if (isset($input['followers'])) {
                $followers = $input['followers'];
                unset($input['followers']);
            }
            DB::beginTransaction();
            $task = $this->repository->save($input);
            $statusTaskOwners = $this->taskOwnerRepo->saveTaskOwners($task->id, $taskOwners, $followers);
            if ($input['id'] == null) {
                $statusSave = $this->statusRepo->saveStatus($task->id, 0, 'Task started');
                $mail = $this->repository->sendTaskMail($task, 'project_management_task_created');
            }
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $status = $this->repository->deleteTask($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function ratings()
    {
        return response()->json($this->empRatingRepo->getAll());
    }

    public function storeRating(TaskRatingRequest $request)
    {
        try {
            $input = $request->all();
            //Created by
            $input['rated_by'] = auth()->user()->id;
            //Status
            $input['deadline_rating_id'] = $input['deadline_rating_id'];
            $input['value_add_rating_id'] = $input['value_add_rating_id'];
            $input['initiative_rating_id'] = $input['initiative_rating_id'];
            $input['commitment_rating_id'] = $input['commitment_rating_id'];
            $input['complexity_rating_id'] = $input['complexity_rating_id'];
            $input['efficiency_rating_id'] = $input['efficiency_rating_id'];
            $input['deadline_weightage'] = $input['deadline_weightage'];
            $input['value_add_weightage'] = $input['value_add_weightage'];
            $input['initiative_weightage'] = $input['initiative_weightage'];
            $input['commitment_weightage'] = $input['commitment_weightage'];
            $input['complexity_weightage'] = $input['complexity_weightage'];
            $input['efficiency_weightage'] = $input['efficiency_weightage'];
            $input['rating_notes'] = $input['rating_notes'];
            $input['rated_at'] = date('Y-m-d H:i:s');
            $input['average_rating'] =  $this->repository->calculateAverage($input);

            DB::beginTransaction();
            $task = $this->repository->save($input);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function markProgress($taskId, $is_completed)
    {
        try {
            $input['is_completed'] = !($is_completed);
            $input['id'] = $taskId;
            DB::beginTransaction();
            if ($input['is_completed'] == 1) {
                $input['completed_date'] = Carbon::now();
                $statusSave = $this->statusRepo->saveStatus($taskId, 100, 'Task Completed');
            } else {
                $input['completed_date'] =null;
                $statusDelete = $this->statusRepo->deleteTaskStatusByPercentage($taskId);
            }
            $task = $this->repository->save($input);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }
}
