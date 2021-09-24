<?php

namespace Modules\Osgc\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OsgcCourseRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $rowno = count(request('row-no'));//dd(count($rowno));
        $rules = [
            'title' => "bail|required|max:60|unique:osgc_courses,title,{$id},id,deleted_at,NULL",
            'price' => "required|numeric|regex:/^\d*(\.\d{2})?$/",
            'description' => "required",
        ];
        for($i=0;$i<$rowno;$i++)
        {
            $heading='heading_'.$i;
            $sort_order='sort_order_'.$i;
            $status='status_'.$i;
            if(request($heading) =='')
            {
                $rules[$heading] = "required";
            }else{
               // $rules[$heading] = "max:20";
            }
            if(request($sort_order) =='')
            {
                $rules[$sort_order] = "required";
            }
            if(request($status) =='')
            {
                $rules[$status] = "required";
            }
        }
        
        return $rules;
        
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $rowno = count(request('row-no'));
        $msg= [
            'tile.required' => 'The Course Title is required.',
            'tile.unique' => 'The Course Title is already added.',
            'tile.max' => 'The Course Title should not exceed 255 characters.',
        ];
        for($i=0;$i<$rowno;$i++)
        {
            $heading='heading_'.$i;
            $sort_order='sort_order_'.$i;
            $status='status_'.$i;
            if(request($heading) =='')
            {
                $msg[$heading.'.required'] = "The Course heading is required";
                
            }else{
                $msg[$heading.'.max']= "The Course heading should not exceed 20 characters.";
            }
            if(request($sort_order) =='')
            {
                $msg[$sort_order.'.required'] = "The Sort order is required";
                
            }
            if(request($status) =='')
            {
                $msg[$status.'.required'] = "The Status is required";
                
            }
        }
        return $msg;
    }
    public function authorize()
    {
        return true;
    }

}
