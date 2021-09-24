<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Models\CustomerShifts;
use Carbon\Carbon;

class CustomerShiftRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\CustomerShifts $CustomerShiftsModel
     */
    public function __construct(CustomerShifts $CustomerShiftsModel)
    {
        $this->model = $CustomerShiftsModel;

    }

    /**
     * Get  CustomerShifts list
     *
     * @param empty
     * @return array
     */
    public function getAll($client_id = null)
    {
        $data = CustomerShifts::
            join('customers', 'customer_shifts.customer_id', '=', 'customers.id')->select('*',
            \DB::raw("customer_shifts.id as shift_id"),
             \DB::raw("CONCAT(customers.project_number, ' - ',customers.client_name) as client_name"),
        \DB::raw("TIME_FORMAT(starttime, '%h:%i %p') as starttime"), \DB::raw("TIME_FORMAT(endtime, '%h:%i %p') as endtime")
        )->where('customers.deleted_at',null)
        ->orderBy('customers.client_name', 'ASC')
        ->get();
        $data = $data->when($client_id!=null, function ($q) use ($client_id) {
            return $q->where('customer_id', $client_id);
        });
        return $data;
    }

    public function get($id)
    {
        $data = $this->model->with(['customer'])->find($id);
        $data->starttime = \Carbon::createFromFormat('H:i:s', $data->starttime)->format('g:i A');
        $data->endtime = \Carbon::createFromFormat('H:i:s', $data->endtime)->format('g:i A');
        return $data;
    }

    public function save($data)
    {
        $data['starttime'] = \Carbon::createFromFormat('h:i a', $data['starttime']);
        $data['endtime'] = \Carbon::createFromFormat('h:i a', $data['endtime']);
        return $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    /**
     * Remove the specified CustomerShifts from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteCustomerShift($id)
    {
        return $this->model->destroy($id);
    }

}
