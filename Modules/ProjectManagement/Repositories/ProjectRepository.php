<?php

namespace Modules\ProjectManagement\Repositories;

use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\ProjectManagement\Models\PmProject;

class ProjectRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;
    protected $customerEmpRepo;

    /**
     * Create a new Model instance.
     *
     * @param  \App\Models\WorkType $workTypeModel
     */
    public function __construct(PmProject $pmProject, CustomerEmployeeAllocationRepository $ceRepo)
    {
        $this->model = $pmProject;
        $this->customerEmpRepo = $ceRepo;
    }

    /**
     * Get all Users of a project
     */
    public function getUsersOfProject($projectId)
    {
        $project = PmProject::find($projectId);
        if ($project) {
            $data=  $this->customerEmpRepo->allocationList($project->customerDetails->id);
            return $this->prepareArray($data);
        }
        return [];
    }

     /**
     * Prepare user array
     */
    public function prepareArray($data)
    {
        $name_list = array();
        foreach ($data as $key => $each_list) {
            $each_row["full_name"] = $each_list->full_name;
            $each_row["id"] = $each_list->id;
            array_push($name_list, $each_row);
        }
        $key_exists = array_search(\Auth::user()->id, array_column($name_list, 'id'));
        if (!$key_exists) {
            $new_row =  array ('full_name' => \Auth::user()->full_name,'id' => \Auth::user()->id);
            array_push($name_list, $new_row);
        }
         usort($name_list, function ($a, $b) {
                 return strcmp($a['full_name'], $b['full_name']);
         });

        return $name_list;
    }

    /**
     * Get  Customer list
     *
     * @param empty
     * @return array
     */
    public function getAll($allocated_customers, $client_id=null)
    {
        $data = $this->model->select(['id', 'name', 'customer_id','created_at'])->when($allocated_customers!=null, function ($query) use ($allocated_customers) {
            $query->whereIn('customer_id', $allocated_customers);
        })->whereHas('customerDetails')->with('customerDetails')->get();

        if($client_id!=null) {
            $data= $data->where('customer_id', $client_id);
        }

        return $data;
    }

    /**
     * Get  project  list as based on customer
     *
     * @param empty
     * @return array
     */
    public function getAsArray($customer_id_array)
    {
        return $this->model->whereIn('customer_id', $customer_id_array)->orderBy('name')->pluck('name', 'id')->toArray();
    }

     /**
     * Get  Project name list
     *
     * @param empty
     * @return array
     */
    public function getProjectNamesForPerformanceReport($allocated_customers)
    {
        return $this->model->select('*')->when(\Auth::user()->hasPermissionTo('view_all_performance_reports'), function ($query) {
                $query;
        }, function ($query) use ($allocated_customers) {
            $query->whereIn('customer_id', $allocated_customers);
        })->orderBy('name')->pluck('name', 'id')->toArray();
    }

    /**
     * Get single project details
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('groups', 'taskList')->find($id);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  $request
     * @return object
     */
    public function save($data)
    {
        $data['name']=strip_tags($data['name']);
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  $id
     * @return object
     */
    public function deleteProject($id)
    {

        return $this->model->destroy($id);
    }
}
