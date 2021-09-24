<?php

namespace Modules\Admin\Repositories;
use Modules\Admin\Models\EmployeeRatingPolicies;
use Modules\Admin\Models\EmployeeRatingPolicyAllocation;
use Modules\Admin\Models\TrainingTimingLookup;

use DB;
class EmployeeRatingPolicyRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$employeeRatingPolicyAllocation;

    /**
     * Create a new TrainingLookupRepository instance.
     *
     * @param  \App\Models\TrainingLookup $trainingLookup
     */
    public function __construct(EmployeeRatingPolicies $employeeRatingPolicies,
    EmployeeRatingPolicyAllocation $employeeRatingPolicyAllocation)
    {
        $this->model = $employeeRatingPolicies;
        $this->employeeRatingPolicyAllocation = $employeeRatingPolicyAllocation;

    }

    /**
     * Get security clearance lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->select(['id', 'policy','description','created_at','updated_at'])->get();
    }

    /**
     * Get Position lookup list
     *
     * @param empty
     * @return array
     */
    public function getList()
    {
        return $this->model->orderBy('id', 'asc')->pluck('policy', 'description','id')->toArray();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with('rating_allocation')->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        $policies = $this->model->updateOrCreate(array('id' => $data['id']), $data);
        if ($data['id'] != null) {
            EmployeeRatingPolicyAllocation::where('employee_rating_policy_id',$data['id'])->delete();
            $policy_id = $data['id'];
        }else{
            $policy_id = $policies->id;
        }
        if(!empty($data['ratings'])){
        foreach ($data['ratings'] as $row) {
        $allocation=EmployeeRatingPolicyAllocation::Create([
            'employee_rating_policy_id'=>$policy_id,
            'employee_rating_id'=>$row,
        ]);
        }
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
        $result = $this->model->destroy($id);
        if($result){
            $this->employeeRatingPolicyAllocation->where('employee_rating_policy_id',$id)->delete();
        }
    }

     /**
     * Get Rating Policy details for App
     *
     * @param empty
     * @return array
     */
    public function getRatingPolicyDetails($rating_id)
    {
      $data = $this->employeeRatingPolicyAllocation->with('policy')->where('employee_rating_id',$rating_id)->get();
      $rating_policy = [];
      foreach ($data as $key => $value)
      {
          $object = new \stdClass();
          $object->id = $value->policy->id;
          $object->name = $value->policy->policy;
          $object->description = $value->policy->description;
          $rating_policy[] =  $object;
      }
        if (!empty($rating_policy)) {
            usort($rating_policy, function( $a, $b ) {
                return strcmp($a->name, $b->name);
            });
        }
      return $rating_policy;
    }

    /**
     * Get Rating Policy details
     *
     * @param empty
     * @return array
     */
    public function getPolicyByRatingId($rating_id)
    {
      $policies = $this->employeeRatingPolicyAllocation->with('policy')->where('employee_rating_id',$rating_id)->get();
      return $policies;
    }

    /**
     * Get Policy and Descripton for App
     * @param Response array
     */
    public function getEmployeeRatingPolicies()
    {
        return $this->model->orderBy('id', 'asc')->select(['id', 'policy', 'description'])->get();
    }
}
