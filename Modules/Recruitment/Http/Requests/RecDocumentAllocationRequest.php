<?php

namespace Modules\Recruitment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecDocumentAllocationRequest extends FormRequest
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
        $category_id = request('category_id');

        return [
            'customer_id' => "bail|required|not_in:Please Select",
            'category_id' => "bail|required|not_in:Please Select",
            'document_id.*' => "bail|required|not_in:0",
            'document_name.*' => "bail|required",
            'order.*' => "bail|required"
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
            'customer_id.required' => 'Please choose a customer',
            'customer_id.not_in' => 'Please choose a customer',
            'category_id.required' => 'Please choose a category',
            'category_id.not_in' => 'Please choose a category',
            'document_id.*.required' => 'Please select a document',
            'document_id.*.not_in' => 'Please select a document',
            'document_name.*.required' => 'Please choose a document name',
            'order.*.required' => 'Please choose a order'
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
