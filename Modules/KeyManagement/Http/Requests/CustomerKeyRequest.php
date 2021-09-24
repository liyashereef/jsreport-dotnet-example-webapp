<?php

namespace Modules\KeyManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerKeyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $customer_id = request('customer_id');
        return $rules = [
            'key_id' => "bail|required|max:40|unique:customer_key_details,key_id,{$id},id,customer_id,{$customer_id},deleted_at,NULL",
            'room_name' => 'bail|required|max:40',
            'key_image' => 'mimes:jpeg,jpg,png,gif|dimensions:max_width=225,max_height=225,min_width=225,min_height=225',
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

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'key_id.required'=>'Key Id is required',
            'key_id.max'=>'The key id should not exceed 40 characters.',
            'key_id.unique'=>'The key id has already been taken.',
            'room_name.required'=>'Room name is required',
            'room_name.max'=>'Room name should not exceed 40 characters',
            'key_image.mimes' => 'The key image must be a file of type: jpeg, jpg, png, gif.',
            'key_image.dimensions' =>'Image dimensions must be of width 225px and max height 225px',
            
        ];
    }
}
