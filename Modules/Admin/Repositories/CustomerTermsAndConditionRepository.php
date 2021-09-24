<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\CustomerTermsAndCondition;

class CustomerTermsAndConditionRepository
{
    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $termsAndCondition;

    /**
     * Create new Repository instance.
     *
     * @param  \App\Models\CustomerTermsAndCondition $terms_and_condition
     */
    public function __construct(CustomerTermsAndCondition $termsAndCondition)
    {
        $this->termsAndCondition = $termsAndCondition; 
    }

    public function store($inputs){
        $inputs['created_by'] = \Auth::id();
        return $this->termsAndCondition->create($inputs);
    }

    public function update($id,$inputs){
        $inputs['updated_by'] = \Auth::id();
        return $this->termsAndCondition->where('id',$id)->update($inputs);
    }

    public function getAll(){
        return $this->termsAndCondition
        ->select('id','customer_id','type_id','terms_and_conditions')
        ->with(array('customer'=> function($query){
            $query->select('id','project_number','client_name');
        }))
        ->get();
    }
    public function getAllCustomerTermsAndConditions(){
        return $this->termsAndCondition
        ->select('id','customer_id','type_id','terms_and_conditions')
        ->where('customer_id','!=',0)
        ->with(array('customer'=> function($query){
            $query->select('id','project_number','client_name');
        }))
        ->get();
    }
    
    public function getById($id){
        return $this->termsAndCondition
        ->with(array('customer'=> function($query){
            $query->select('id','project_number','client_name');
        }))
        ->find($id);
    }

    public function getAllByType($typeId){
        return $this->termsAndCondition
        ->where('type_id',$typeId)
        ->select('id','customer_id','type_id','terms_and_conditions')
        ->get();
    }

    public function getDefaultByType($typeId){
        return $this->termsAndCondition
        ->where('type_id',$typeId)
        ->where('customer_id',0)
        ->select('id','customer_id','type_id','terms_and_conditions')
        ->first();
    }

    public function getByCustomerAndType($typeId,$customerId){
        return $this->termsAndCondition
        ->where('type_id',$typeId)
        ->where('customer_id',$customerId)
        ->select('id','customer_id','type_id','terms_and_conditions')
        ->first();
    }

    public function delete($id){  
        return $this->termsAndCondition->where('id',$id)->delete();
    }


}