<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CustomerIncidentSubjectAllocation;
use Modules\Admin\Models\CustomerIncidentPriority;
use Modules\Admin\Models\IncidentPriorityLookup;
use Modules\Admin\Models\SampleIncidentSubjects;
use Log;

class CustomerIncidentSubjectAllocationRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new CustomerIncidentSubjectMapping instance.
     *
     * @param  \App\Models\CustomerIncidentSubjectAllocation $customerIncidentSubjectAllocation
     */
    public function __construct(CustomerIncidentSubjectAllocation $customerIncidentSubjectAllocation)
    {
        $this->model = $customerIncidentSubjectAllocation;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll($id)
    {
        return $this->model->with('subjectWithTrashed','categoryWithTrashed','incidentPriority')->where('customer_id',$id)->get();
    }



    /**
     * Display a listing of resources.
     *
     * @param empty
     * @return array
     */
    public function getList($id)
    {
        return $this->model->with('customer','subject')->where('customer_id',$id)->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('incidentPriority')->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */

    /**
     * Get Subject Allocation List For App
     *
     * @param empty
     * @return array
     */
    public function getSubjectAllocationForApp($id)
    {
         $subjects = $this->model->with('subject','category','incidentPriority')->where('customer_id',$id)->get();
         $allocations =[];
         foreach ($subjects as $key => $value) {
           $allocations[$key]['id'] = $value->subject->id;
           $allocations[$key]['subject'] = $value->subject->subject;
           $allocations[$key]['priority'] = $value->incidentPriority->id;
           $allocations[$key]['response_time'] = $value->incident_response_time;
           $allocations[$key]['sop'] = $value->sop;
         }
       return $allocations;
    }

    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']),['customer_id' => $data['customer_id'],'subject_id' => $data['subject_id'],'category_id' => $data['category_id'],'priority_id' => $data['priority_id'],'incident_response_time' => $data['incident_response_time']*60,'sop' => $data['sop']]);        
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

     /**
     * Get Subject Allocation List For App
     *
     * @param empty
     * @return array
     */
    public function getPriorityId($subject_id,$customer_id)
    {
       $priority = $this->model->select('priority_id')->where('customer_id',$customer_id)->where('subject_id',$subject_id)->first();
       return $priority;
    }

    /**
     * Get Subject Category Code for Incident Report
     *
     * @param empty
     * @return array
     */
    public function getSubjectCategoryCode($subject_id,$customer_id)
    {
       $allocation_details = $this->model->with('customer','subject','category')->where('customer_id',$customer_id)->where('subject_id',$subject_id)->first();
       $result = $allocation_details->subject->subject_short_name.'-'.$allocation_details->category->category_short_name.'-'.$allocation_details->customer->project_number;
       return $result;
    }


    /**
     * Update customer priority
     *
     * @param empty
     * @return array
     */
    public function updateCustomerIncidentPriority()
    {
        $customer_ids = $this->model->groupBy('customer_id')->pluck('customer_id')->toArray();
        $priorities   = IncidentPriorityLookup::pluck('value','id')->toArray();
        CustomerIncidentPriority::truncate();
        $response_time =0;
        $added_rows = 0;
        foreach ($customer_ids as $key => $each_customer) {

           foreach ($priorities as $pkey => $each_priority) {
                if ($each_priority == 'High') {
                    $response_time = config('globals.incident_high_response_time');
                } else if($each_priority == 'Medium'){
                    $response_time = config('globals.incident_medium_response_time');
                } else if($each_priority == 'Low'){
                    $response_time = config('globals.incident_low_response_time');
                }

                $result = CustomerIncidentPriority::firstOrCreate(['priority_id' => $pkey,'customer_id' => $each_customer,'response_time' => $response_time]);
                if($result){
                    $added_rows ++;
                }
           }

        }  

        return $added_rows;
    }


    /**
     * Update customer subject allocation 
     *
     * @param empty
     * @return array
     */
    public function updateCustomerIncidentSubject()
    {
       $all_incident_subjects = $this->model->select('id','customer_id', 'subject_id', 'category_id', 'priority_id', 'incident_response_time', 'sop')->get();
       foreach ($all_incident_subjects as $key => $eachval) {
           $subject_details = SampleIncidentSubjects::where('subject_id',$eachval->subject_id)->first();
           if(!empty($subject_details)){
           $stats = $this->model->updateOrCreate(array('id' => $eachval['id']),['category_id' => $subject_details->category_id,'priority_id' => $subject_details->priority_id,'incident_response_time' => $subject_details->incident_response_time,'sop' => $subject_details->sop]);        
        }
       }

    }
    

}
