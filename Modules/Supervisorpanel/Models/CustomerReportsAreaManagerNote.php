<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class CustomerReportsAreaManagerNote extends Model {

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['notes', 'element_id', 'customer_template_payperiod_id', 'created_by', 'updated_by'];
    
    
    /**
     * Insert value into customer_reports_area_manager_notes
     * @param Array $param
     *  Eg: array('customer_template_payperiod_id'=>20,'element_id'=>10)
     * @return type
     */

    public function insertValue($param) {
        $note_insert = CustomerReportsAreaManagerNote::create([
            'notes' => $param['notes'],
            'customer_template_payperiod_id' => $param['customer_template_payperiod_id'],
            'element_id' => $param['element_id'],
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
        return $note_insert;
    }
    
    /**
     * Soft delete element from the DB
     * @param Array $param
     *  Eg: array('customer_template_payperiod_id'=>20,'element_id'=>10) 
     * @return type
     */
    public function destroyValue($param) {
        $note_destroy = CustomerReportsAreaManagerNote::where('element_id',$param['element_id'])
                ->where('customer_template_payperiod_id',$param['customer_template_payperiod_id'])
                ->delete();
        return $note_destroy;
    }
    
    public function fetchAreaManagerNotesOrderedByElementId($customer_payperiod_id){
        return CustomerReportsAreaManagerNote::where('customer_template_payperiod_id',$customer_payperiod_id)->orderBy('element_id')->get();
    }
    
    public function fetchSingleAreaManagerNote($customer_payperiod_id,$element_id){
        return CustomerReportsAreaManagerNote::where('customer_template_payperiod_id',$customer_payperiod_id)->where('element_id',$element_id)->first();
    }
    

}
