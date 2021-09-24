<?php

namespace Modules\Supervisorpanel\Repositories;

use Modules\Supervisorpanel\Models\CustomerReportsAreaManagerNote;

class CustomerReportAreamanagerNotesRepository {

    private $index;
    private $employee_list_arr;
    
    private $customer_reports_area_manager_note;
    
    
    public function __construct()
    {
        $this->employee_list_arr = array();
        $this->customer_reports_area_manager_note = new CustomerReportsAreaManagerNote();
    }

    
    public function getAreaManagerNotesNames($request){
        $areamanager_notes_arr = array();
        foreach ($request as $key => $value) {
            $exploded_key = explode("_", $key);
            if($exploded_key[0] == "am"){
                $comment_details = array('name' => $key,'element_id'=> $exploded_key[1], 'value' => $value);
                array_push($areamanager_notes_arr, $comment_details);
            }
        }
        return $areamanager_notes_arr;
    }


    public function storeAreaManagerNotes($areamanager_note_name_arr, $customer_payperiod_id, $existing_comments){
        foreach($areamanager_note_name_arr as $each_note){  
            $form_element_id = (int) $each_note['element_id'];
            $form_note = $each_note['value'];
            $existing_note = (isset($existing_comments) && (count($existing_comments) > 0) && isset($existing_comments[$form_element_id])) ? $existing_comments[$form_element_id]['notes']: null;
            $parameter_arr = array('customer_template_payperiod_id' => $customer_payperiod_id, 'element_id' => $each_note['element_id'], 'notes' => $each_note['value']);
            if((!isset($existing_comments[$form_element_id])) &&  !empty($each_note['value'])){
                $this->customer_reports_area_manager_note->insertValue($parameter_arr);
            } else if(isset( $existing_comments[$form_element_id]) && ($form_note != $existing_note)){
                $this->customer_reports_area_manager_note->destroyValue($parameter_arr);
                $this->customer_reports_area_manager_note->insertValue($parameter_arr);
            }
        }
    }
    
    public function fetchAreaManagerNotes($customer_payperiod_id){
        $area_manager_notes_arr = array();
        $area_manager_notes = $this->customer_reports_area_manager_note->fetchAreaManagerNotesOrderedByElementId($customer_payperiod_id);
        foreach ($area_manager_notes as $each_note){
            $element_id = $each_note['element_id'];
            $area_manager_notes_arr[$element_id]['notes'] = $each_note['notes'];
            $area_manager_notes_arr[$element_id]['customer_template_payperiod_id'] = $each_note['customer_template_payperiod_id'];
            $area_manager_notes_arr[$element_id]['created_by'] = $each_note['created_by'];
            $area_manager_notes_arr[$element_id]['updated_by'] = $each_note['updated_by'];
        }
        return $area_manager_notes_arr;
    }

}
