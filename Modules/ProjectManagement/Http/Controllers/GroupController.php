<?php

namespace Modules\ProjectManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;
use Modules\ProjectManagement\Repositories\GroupRepository;
use Modules\ProjectManagement\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Modules\ProjectManagement\Repositories\TaskRepository;
use Modules\ProjectManagement\Http\Requests\GroupRequest;

class GroupController extends Controller
{
    protected $helperService, $repository;

    public function __construct(
        HelperService $helperService,
        GroupRepository $groupRepository,
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository
    ) {
        $this->helperService = $helperService;
        $this->repository = $groupRepository;
        $this->projectRepository = $projectRepository;
         $this->taskRepository = $taskRepository;
    }

    /**
     * Store  newly created  Group in storage.
     *
     * @param  Modules\Admin\Http\Requests\WorkTypeRequest $request
     * @return Json
     */
    public function store(GroupRequest $request)
    {

        try {
            DB::beginTransaction();
            $group = $this->repository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * List all Group in datatable.
     * @return Json
     */
    public function list()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * List all Group in datatable.
     * @return Json
     */
    public function getByProject($projectId)
    {
        return datatables()->of($this->repository->getByProject($projectId))->addIndexColumn()->toJson();
    }
     /**
     * List all Group in datatable.
     * @return Json
     */
    public function getGroupsofProject($projectId)
    {
        return $this->repository->getByProject($projectId);
    }

    /**
     * Show the form for editing the specified Group.
     *
     * @param  $id
     * @return Json
     */
    public function show($id)
    {
        return response()->json($this->repository->get($id));
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
            $getAllRelatedData=$this->repository->get($id); 
            $relatedtaskListId=$getAllRelatedData->tasks->pluck('id')->toArray();
            $this->taskRepository->deleteTask($relatedtaskListId);
            $status = $this->repository->deleteGroup($id);
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }
}
