<?php

namespace Modules\Recruitment\Http\Requests;

class RecUniformItemRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return [
            'item_name' => "bail|required|max:255|unique:mysql_rec.rec_uniform_items,item_name,{$id},id,deleted_at,NULL",
            'measuring_points' => "bail|required|not_in:0",
            'min.*'=>"bail|required",
            'max.*'=>"bail|required",
            'size.*'=>"bail|required|not_in:0|distinct"
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'item_name.required' => 'Item Name is required.',
            'item_name.unique' => 'This item name is already added.',
            'item_name.max' => 'This item name should not exceed 255 characters.',
            'measuring_points.not_in' => 'Please choose a measuring point',
            'size.*.required'=>'Please select any size',
            'size.*.not_in'=>'Please select any size',
            'min.*.required'=>'Minimum value is required',
            'max.*.required'=>'Maximum value is required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
