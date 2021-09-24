<?php

namespace Modules\Admin\Repositories;

use Modules\Timetracker\Models\WorkHourActivityCodeCustomer;

class WorkHourCustomerRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new WorkHourActivityCodeCustomer instance.
     *
     * @param  \App\Models\WorkHourActivityCodeCustomer $clientFeedbackLookup
     */
    public function __construct(WorkHourActivityCodeCustomer $workHourActivityCodeCustomer)
    {
        $this->model = $workHourActivityCodeCustomer;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model
            ->select([
                'id', 'work_hour_type_id', 'customer_type_id', 'code', 'duplicate_code', 'description', 'created_by', 'updated_by'
            ])
            ->with('work_hour_type_trashed', 'customer_type_trashed')
            ->get();
    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('feedback', 'asc')->pluck('feedback', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id)->load('work_hour_type');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        if ($data['id']) {
            $data['updated_by'] = \Auth::id();
        } else {
            $data['created_by'] = \Auth::id();
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
        return $this->model->destroy($id);
    }

    public function workTypeAllocationCheck($workTypeId)
    {
        $res = $this->model->where('work_hour_type_id', '=', $workTypeId)->get();
        if ($res->isEmpty()) {
            return false;
        }
        return true;
    }
}
