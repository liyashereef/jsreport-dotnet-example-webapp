<?php

namespace Modules\Client\Http\Requests;

use Modules\Admin\Models\VisitorLogTemplateFields;

class VisitorLogRequest extends Request
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $template_id = request('template_id');
        $field_details = VisitorLogTemplateFields::where('template_id', $template_id)->get();
        $custom_field_rules = [];
        $custom_feature_rules = [];
        $rule = [];
        $rules = [
               'first_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255',   
        ];
      
            foreach ($field_details as $key => $eachfield) {

                      if($eachfield->fieldname == 'last_name' && $eachfield->is_required == 1){
                       $rule = ['last_name' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255'];
                      }elseif ($eachfield->fieldname == 'last_name' && $eachfield->is_required == 0 && $eachfield->is_visible ==1 && request('last_name') ) {
                       $rule = ['last_name' => 'bail|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255'];  
                      }  

                     if($eachfield->fieldname == 'phone' && $eachfield->is_required == 1){
                       $rule = ['phone' => 'bail|required|max:13|min:13'];
                      }elseif ($eachfield->fieldname == 'phone' && $eachfield->is_required == 0 && $eachfield->is_visible ==1  && request('phone')) {
                        $rule = ['phone' => 'bail|max:13|min:13'];
                      }  

                      if($eachfield->fieldname == 'email' && $eachfield->is_required == 1){
                        $rule = ['email' => 'bail|required|max:255|email'];
                      }elseif ($eachfield->fieldname == 'email' && $eachfield->is_required == 0 && $eachfield->is_visible ==1 && request('email') ) {
                        $rule = ['email' => 'bail|max:255|email'];
                      }  

                      if($eachfield->fieldname == 'name_of_company' && $eachfield->is_required == 1){
                       $rule = ['name_of_company' => 'bail|required|max:255'];
                      }elseif ($eachfield->fieldname == 'name_of_company' && $eachfield->is_required == 0 && $eachfield->is_visible ==1 ) {
                        $rule = ['name_of_company' => 'bail|max:255'];
                      }  

                      if($eachfield->fieldname == 'whom_to_visit' && $eachfield->is_required == 1){
                        $rule = ['whom_to_visit' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255'];
                      }elseif ($eachfield->fieldname == 'whom_to_visit' && $eachfield->is_required == 0 && $eachfield->is_visible ==1  && request('whom_to_visit')) {
                       $rule = ['whom_to_visit' => 'bail|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:255'];
                      }  
                      if(request('license_number_validation') !=1){
                      if($eachfield->fieldname == 'license_number' && $eachfield->is_required == 1 ){
                        $rule = ['license_number' => 'bail|required|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:20'];
                      }elseif ($eachfield->fieldname == 'license_number' && $eachfield->is_required == 0 && $eachfield->is_visible ==1  && request('license_number')) {
                       $rule = ['license_number' => 'bail|regex:/^[0-9A-Za-z\'\s\-]+$/u|max:20'];
                      }
                      }  

                      if($eachfield->fieldname == 'visitor_type_id' && $eachfield->is_required == 1){
                        $rule = ['visitor_type_id' => 'bail|required'];
                      } 

                $custom_field_rules =  array_merge($rule,$custom_field_rules);

            }

      return  $new_rules = array_merge($rules, $custom_field_rules);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'Full name is required.',
            'first_name.max' => 'Full name should not exceed 255 characters.',
            'last_name.required' => 'Last name is required.',
            'first_name.regex' => 'Please enter only characters.',
            'last_name.regex' => 'Please enter only characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter valid email address',
            'email.max' => 'Email should not exceed 255 characters.',
            'phone.required' => 'Phone is required.',
            'phone.min' => 'Please enter a valid phone number.',
            'phone:max' => 'Please enter a valid phone number.',
            'name_of_company.required' => 'Company name is required.',
            'whom_to_visit.required' => 'Person to visit is required.',
            'whom_to_visit.regex' => 'Please enter only characters.',
            'whom_to_visit.max' => 'Name should not exceed 255 characters.',
            'license_number.required' => 'Verhicle license plate number is required.',
            'license_number.regex' => 'Please enter only characters.',
            'license_number.max' => 'Verhicle license plate number should not exceed 20 characters.',
            'visitor_type_id.required' => 'Visitor Type is required.',


        ];
    }

}
