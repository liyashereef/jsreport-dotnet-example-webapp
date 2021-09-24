<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\EmailGroup;
use Modules\Admin\Models\EmailGroupAllocation;
use Carbon\Carbon;

class EmailGroupRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct(EmailGroup $emailGroup, EmailGroupAllocation $emailGroupAllocation, Customer $Customer)
    {
        $this->customer = $Customer;
        $this->emailGroup = $emailGroup;
        $this->emailGroupAllocation = $emailGroupAllocation;

    }


    public function getSingleGroupDetails($id){
        $data = $this->emailGroup->with(['allocation'])->find($id);
        return $data;
    }

    public function save($request){
        //dd($request);
        if($request['id'] == null){
            $emailGroup = $this->emailGroup->create(['id' => $request['id'], 'group_name' => $request['group_name']]);
            if (!empty($request['customer'])) {
                foreach ($request['customer'] as $key => $customer) {
                    $allocation = $this->emailGroupAllocation->create(['group_id' => $emailGroup->id, 'customer_id' => $request['customer'][$key]]);
                }
            }
        }else{
            $emailgroup['group_name'] = $request['group_name'];
            $emailGroup = $this->emailGroup->updateOrCreate(['id' => $request['id']], $emailgroup);
                if($emailGroup->id){
                    $this->emailGroupAllocation->where('group_id',$emailGroup->id)->delete();
                    foreach ($request['customer'] as $key => $customer) {
                        $allocation = $this->emailGroupAllocation->create(['group_id' => $emailGroup->id, 'customer_id' => $request['customer'][$key]]);
                    }
                }
        }

    }

    public function getEmailGroups(){
        $data = $this->emailGroup->with('allocation.customer')->get();
        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            $each_row["id"] = $each_record->id;
            $each_row["group_name"] = $each_record->group_name;
            $customers = data_get($each_record, 'allocation.*');
            $cust_arr['customer'] = [];
            foreach ($customers as $key => $each_customer_id) {
                if (isset($each_customer_id->customer) && $each_customer_id->customer->client_name) {
                    array_push($cust_arr['customer'], $each_customer_id->customer->client_name);
                }
            }
            $latest_customer_data = $cust_arr;
            $combined_result = $each_row + $latest_customer_data;
            array_push($datatable_rows, $combined_result);
        }
        return $datatable_rows;
    }

    public function delete($id)
    {
         $id=$this->emailGroup->destroy($id);
         return $this->emailGroupAllocation->where('group_id',$id)->delete();
    }


}

?>
