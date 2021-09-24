<?php

namespace Modules\Admin\Repositories;

use App\Services\HelperService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\EmployeeAllocation;
use Modules\Admin\Models\KpiGroup;
use Modules\Admin\Models\KpiGroupEmployeeAllocation;
use Modules\Admin\Repositories\UserRepository;

class KpiGroupEmployeeAllocationRepository
{

    protected $user_repository;
    protected $role_repository;
    protected $helperService;
    protected $empAllocationRepo;
    protected $model;

    /**
     * Constructor.
     */
    public function __construct(
        UserRepository $userRepository,
        RolesAndPermissionRepository $rolesAndPermissionRepository,
        HelperService $helperService,
        KpiGroupEmployeeAllocation $model,
        EmployeeAllocationRepository $empAllocationRepo

    ) {
        $this->user_repository = $userRepository;
        $this->role_repository = $rolesAndPermissionRepository;
        $this->helperService = $helperService;
        $this->model = $model;
        $this->empAllocationRepo = $empAllocationRepo;
    }

    public function save($inputs)
    {
        $this->model->create($inputs);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function unallocate($employee_id, $group_id)
    {
        $emp_unallocation = KpiGroupEmployeeAllocation::where('user_id', $employee_id);
        if ($group_id != 0) {
            $emp_unallocation->where('kpi_group_id', $group_id);
        }

        $emp_unallocation->update(['updated_by' => Auth::user()->id]);
        $result = $emp_unallocation->delete();
        return $result;
    }


    public function allocateEmployee($employee_id_list, $group_id, $request)
    {
        $selected_group_employee_list = $this->model->where('kpi_group_id', $group_id)->pluck('user_id')->toArray();
        $allocated_employee_list = array_intersect($selected_group_employee_list, $employee_id_list);

        if (!empty($allocated_employee_list)) {
            return false;
        }
        foreach ($employee_id_list as $employee_id) {
            $this->model->create([
                'kpi_group_id' => $group_id,
                'user_id' => $employee_id,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
        }
        return true;
    }

    //TODO: temperory replace later
    public function tmpGetUsersWithGroupInfo()
    {
        try {
            $userallocationlist = $this->empAllocationRepo->getUsersCanReportTo(null, null, $isPermissionWise = false)
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'emp_no' => $item->employee->employee_no ?? '',
                        'emp_name' => $item->full_name,
                        'emp_email' => $item->email,
                        'emp_role' => implode(', ', array_map(array(HelperService::class, "snakeToTitleCase"), data_get($item, 'roles.*.name'))),
                        'emp_groups' => implode(', ', array_filter(data_get($item, 'allocatedGroups.*.group.name'))),
                    ];
                });
        } catch (\Throwable $th) {
            $userallocationlist = [];
        }

        return $userallocationlist;
    }

    public function groupsOfaUser($userId)
    {
        $gids =  KpiGroupEmployeeAllocation::where('user_id', '=', $userId)->pluck('kpi_group_id')->toArray();;
        return KpiGroup::find($gids);
    }
}
