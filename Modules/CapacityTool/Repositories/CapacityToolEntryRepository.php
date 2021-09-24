<?php

namespace Modules\CapacityTool\Repositories;

use Modules\Admin\Models\User;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\CapacityTool\Models\CapacityToolEntry;
use Modules\CapacityTool\Repositories\CapacityToolQuestionRepository;

class CapacityToolEntryRepository
{

    /**
     * Create a new CapacityToolEntry instance.
     *
     * @param  Modules\Admin\Models\CapacityTool $capacityTool
     */
    public function __construct()
    {
        $this->model = new CapacityToolEntry;
        $this->capacityToolQuestionRepository = new CapacityToolQuestionRepository();
        $this->employeeAllocationRepository = new EmployeeAllocationRepository();
    }

    /**
     * Get all resource list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {

    }

    /**
     * Get all resource list based on permissions
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        $logged_in_user_id = \Auth::id();
        $logged_in_user = User::find($logged_in_user_id);

        $query = $this->model->whereHas('user', function ($query) use ($logged_in_user, $logged_in_user_id) {
            if ($logged_in_user->hasAnyPermission(['view_all_capacity_tool','admin', 'super_admin'])) {
            } elseif ($logged_in_user->hasPermissionTo('view_allocated_capacity_tool')) {
                $user_ids = $this->employeeAllocationRepository->getEmployeeIdAssigned($logged_in_user_id);
                $user_ids->prepend($logged_in_user_id);
                $query->whereIn('id', $user_ids);

            } else {
                $user_ids = [$logged_in_user_id];
                $query->whereIn('id', $user_ids);
            }

        });

        $capacity_tool_entries = $query->with(['user.employee', 'parentcapacitytools.answerable'])->orderBy('created_at', 'desc')->get();

        return $capacity_tool_entries_formatted = $capacity_tool_entries->map(function ($item) {

            $new_item = [];
            $new_item['employee_id'] = $item->user->id;
            $new_item['capacity_tool_entry_id'] = $item->id;
            $new_item['employee_no'] = $item->user->employee->employee_no;
            $new_item['name'] = $item->user->full_name;
            $date = $item->created_at;
            $new_item['created_at'] = $date->format('Y-m-d');
            $i = 1;
            $status_lookup_name = 'Modules\Admin\Models\CapacityToolStatusLookup';
            $expected_capacity_tool_entries_count = 8;
            $capacity_tool_entries_count = count($item->parentcapacitytools);
            foreach ($item->parentcapacitytools as $answer) {
                if ($answer['answer_type'] == $status_lookup_name) {
                    $new_item['answer_' . $i] = $answer['answerable']['short_name'];

                } else {
                    $new_item['answer_' . $i] = ($answer['answer_type'] != null) ? $answer['answerable']['value'] : $answer['answer'];
                    //Condition to check if there are entries for all questions,if not then set start date and duration as null
                    if ($answer['answer_type'] == "Modules\Admin\Models\CapacityToolTaskTypeLookup" && $capacity_tool_entries_count != $expected_capacity_tool_entries_count) {
                        $new_item['answer_' . ++$i] = '--';
                        $new_item['answer_' . ++$i] = '--';
                    }

                }
                $i++;
            }
            return $new_item;
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  integer
     * @return integer
     */
    public function store($employee_id)
    {
        $capacity_tool_entry = $this->model->create(['employee_id' => $employee_id]);
        return $capacity_tool_entry->id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        //return $this->model->destroy($id);
    }
}
