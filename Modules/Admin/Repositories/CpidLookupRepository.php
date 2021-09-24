<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CpidLookup;
use Modules\Admin\Models\CpidRates;
use Modules\Admin\Models\Employee;

class CpidLookupRepository {

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new IncidentPriorityLookup instance.
     *
     * @param  \App\Models\IncidentPriorityLookup $incidentPriorityLookupModel
     */
    public function __construct(CpidLookup $cpidLookupModel, CpidRates $cpidRateModel) {
        $this->model = $cpidLookupModel;
        $this->cpidRateModel = $cpidRateModel;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll() {
        return $this->model->select(
                                [
                                    'id',
                                    'cpid',
                                    'short_name',
                                    'description',
                                    'created_at',
                                    'updated_at',
                                    'noc',
                                    'position_id'
                                ]
                        )
                        ->with('cpidRates')
                        ->with('position')
                        ->orderBy('updated_at', 'DESC')
                        ->get();
    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList() {
        return $this->model->orderBy('cpid', 'asc')->pluck('cpid', 'id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id) {
        // return $this->model->find($id);
        return $this->model->with('cpidRates')->get()->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data) {
        $cpid = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        if ($cpid->id) {
            CpidRates::where('cp_id', $cpid->id)->delete();
            if ($data['effective_from']) {
                $cpid->touch();
                CpidRates::create(['cp_id' => $cpid->id, 'effective_from' => $data['effective_from'], 'p_standard' => $data['p_standard'], 'p_overtime' => $data['p_overtime'], 'p_holiday' => $data['p_holiday'], 'b_standard' => $data['b_standard'], 'b_overtime' => $data['b_overtime'], 'b_holiday' => $data['b_holiday'],
                ]);
            }
        }
        return $cpid;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id) {
        return $this->model->destroy($id);
    }

    /**
     * Get all cpid history list
     *
     * @param empty
     * @return array
     */
    public function getHistoryAll($cp_id) {
        return $this->cpidRateModel->select(['id', 'cp_id', 'effective_from', 'p_standard', 'p_overtime', 'p_holiday', 'b_standard', 'b_overtime', 'b_holiday', 'created_at', 'updated_at'])->withTrashed()->where('cp_id', $cp_id)->orderBy('created_at', 'DESC')->get();
    }

    public function getAllByCustomerId($customer_id) {
        $data = $this->model->whereHas('cpidCustomerAllocation', function ($query) use ($customer_id) {
                    $query->where('customer_id', $customer_id);
                })->with('cpidCustomerAllocation.position')->get();
        return $data;
    }

    public function get_cpid_positions() {
        return $this->model->with(['position','cpidFunction'])->get();
    }

    /*
     * fetch cpid rate by customer and employee
     */

    public function getAllCpidByParameters($customerId = null, $type = false) {
        $cpIds = [];
        if ($type == true) {
            //for mobile app
            $employeeObj = \Auth::user()->employee;
            $positionId = $employeeObj->position_id;
            //$positionId = null;
        } else {
            //for web app
            $positionId = null;
        }

        $role_name = \Auth::user()->roles->first()->name;
        $customerAlocations = $this->model->whereHas('cpidCustomerAllocation', function ($query) use ($customerId) {
                    if ($customerId != null) {
                        $query->where('customer_id', $customerId);
                    }
                })->when(($positionId != null), function ($query) use ($positionId) {
                    $query->where('position_id', $positionId);
                })->get();
        foreach ($customerAlocations as $ky => $customerAlocation) {
            $cpIds[$ky]['id'] = $customerAlocation->cpidRates->id;
            $cpIds[$ky]['value'] = $customerAlocation->cpidRates->cpidLookup->cpid;
        }
        return $cpIds;
    }

    public function checkFunctionAllocation($id)
    {
        $res = $this->model->where('cpid_function_id', '=', $id)->get();
        if ($res->isEmpty()) {
            return false;
        }
        return true;
    }

}
