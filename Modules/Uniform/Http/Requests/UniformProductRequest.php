<?php

namespace Modules\Uniform\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniformProductRequest extends FormRequest
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
            'name' => "bail|required|max:255|unique:uniform_products,name,{$id},id,deleted_at,NULL",
            'selling_price' => "bail|required",
            'tax_id' => 'required|numeric'
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
            'tax_id.required' => 'Tax is required.',
            'name.required' => 'Product Name is required.',
            'name.unique' => 'This item name is already added.',
            'name.max' => 'This item name should not exceed 255 characters.',
            'selling_price.required' => 'Selling Price is required.',
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
