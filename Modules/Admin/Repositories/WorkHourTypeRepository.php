<?php

namespace Modules\Admin\Repositories;

use Modules\Timetracker\Models\EmployeeShiftWorkHourType;

class WorkHourTypeRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new EmployeeShiftWorkHourType instance.
     *
     * @param  \App\Models\EmployeeShiftWorkHourType $employeeShiftWorkHourType
     */
    public function __construct(EmployeeShiftWorkHourType $employeeShiftWorkHourType)
    {
        $this->model = $employeeShiftWorkHourType;
    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'name', 'description', 'sort_order', 'is_editable', 'is_deletable', 'created_at', 'updated_at'])->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if ($data['id'] > 0) {
            $detailedData = $this->model->find($data['id']);
            // Condition to move from order to lesser number like sort order 7 to 5
            if ($detailedData->sort_order > $data['sort_order']) {
                $this->model->where("sort_order", ">=", $data['sort_order'])
                    ->where("sort_order", "<=", $detailedData->sort_order)->update(
                        ['sort_order' => \DB::raw('sort_order + 1')]
                    );
            }
            // Condition to move from order to bigger number like sort order 5 to 7
            else if ($detailedData->sort_order < $data['sort_order']) {
                $this->model->where("sort_order", "<=", $data['sort_order'])
                    ->where("sort_order", ">=", $detailedData->sort_order)->update(
                        ['sort_order' => \DB::raw('sort_order - 1')]
                    );
            }
        }
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $detailedData = $this->model->find($id);
        $sortOrder = $detailedData->sort_order;
        if ($sortOrder > 0) {
            $this->model->where("sort_order", ">", $sortOrder)->update(
                ['sort_order' => \DB::raw('sort_order - 1')]
            );
        }
        return $this->model->destroy($id);
    }
}
