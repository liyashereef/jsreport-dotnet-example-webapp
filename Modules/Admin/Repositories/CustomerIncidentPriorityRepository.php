<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CustomerIncidentPriority;

class CustomerIncidentPriorityRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CustomerIncidentPriority instance.
     *
     * @param  \App\Models\CustomerIncidentPriority $customerIncidentPriorityModel
     */
    public function __construct(CustomerIncidentPriority $customerIncidentPriorityModel)
    {
        $this->model = $customerIncidentPriorityModel;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->with('priority')->get();
    }

    
    /**
     * To Check customer incident priority.
     *
     * @param empty
     * @return array
     */
    public function getCustomerIncidentPriority($customer_id)
    {
        $priorities = $this->model->with('priority')->where('customer_id',$customer_id)->get()->toArray();
        $priority = [];
        foreach ($priorities as $key => $each) {
        $priority[$key] = $each['priority']['priority_order'];
        }
        array_multisort($priority, SORT_NUMERIC, SORT_ASC, $priorities);
        return $priorities;

    }

    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->with('priority')->pluck('value', 'id')->toArray();
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
        for($i = 0; $i < count($data['response_time']); $i++){
            
            $pr_data[$i] = [
                'priority_id' => $data['priority_id'][$i],
                'customer_id' => $data['customer_id'],
                'response_time' => $data['response_time'][$i] * 60,
            ];
           $this->model->updateOrCreate(array('id' => $data['id'][$i]), $pr_data[$i]);
        }
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

    /* Update customer incident priority
    *
    * @param empty
    * @return array
    */
   public function updateCustomerIncidentPriority()
   {
       $customer_ids = $this->model->groupBy('customer_id')->pluck('customer_id')->toArray();

       foreach ($customer_ids as $key => $each_customer) {
           
       }  

       return $customer_ids;
   }
}
