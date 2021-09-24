<?php

namespace Modules\ProjectManagement\Http\Controllers;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManagement\Repositories\TaskRepository;
use Modules\ProjectManagement\Repositories\TaskStatusLogRepository;
use Modules\ProjectManagement\Repositories\TaskStatusRepository;

class TaskStatusController extends Controller
{
    protected $helperService;
    protected $repository;

    public function __construct(
        HelperService $helperService,
        TaskStatusRepository $taskStatusRepository,
        TaskStatusLogRepository $taskStatusLogRepository,
        TaskRepository $taskRepository
    ) {
        $this->helperService = $helperService;
        $this->repository = $taskStatusRepository;
        $this->taskStatusLogRepository = $taskStatusLogRepository;
        $this->taskRepository = $taskRepository;
    }

    public function store(Request $request)
    {
        $max_percentage_status = $this->repository->getMaxPercentage($request->task_id);
        $request->validate([
            'notes' => 'required',
            'percentage' => $request->id == $max_percentage_status->id ? '' : 'gte:' . $max_percentage_status->percentage,
        ], [
            'notes.required' => 'Notes is required',
            'notes.max' => 'Notes should not exceed 1000 characters.',
            'percentage.gte' => 'Percentage must be at least ' . $max_percentage_status->percentage,
        ]);
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['status_date'] = Carbon::now();
            $input['updated_by'] = \Auth::user()->id;
            $ts = $this->repository->save($input);
            // $log=$this->taskStatusLogRepository->save($request->task_id);
            $updated_percentage = $this->repository->getByTask($request->task_id)->pluck('percentage', 'id')->toArray();
            $data['id'] = $request->task_id;
            if (in_array(100, $updated_percentage)) {
                $data['is_completed'] = 1;
                $data['completed_date'] = Carbon::now();
            } else {
                $data['is_completed'] = 0;
                $data['completed_date'] = null;
            }
            $taskCompletionUpdate = $this->taskRepository->save($data);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function show($id)
    {
        return response()->json($this->repository->get($id));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $status = $this->repository->deleteTaskStatus($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function getStatusofTasks($taskId)
    {
        try {
            \DB::beginTransaction();
            $result = $this->repository->getByTask($taskId);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
