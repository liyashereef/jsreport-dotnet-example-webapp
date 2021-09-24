<?php

namespace Modules\Recruitment\Http\Requests;

class RecCommissionairesUnderstandingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    //$id = request('id');

    public function rules()
    {
        $id = request('id');
        return $rules = [
            'commissionaires_understandings' => "bail|required|max:255|unique:mysql_rec.rec_commissionaires_understanding_lookups,commissionaires_understandings,{$id},id,deleted_at,NULL",
            'short_name' => "bail|nullable|max:100",
            'order_sequence' => "bail|required|integer|min:1|max:999|unique:mysql_rec.rec_commissionaires_understanding_lookups,order_sequence,{$id},id,deleted_at,NULL",
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
            'commissionaires_understandings.required' => 'Comment is required.',
            'commissionaires_understandings.unique' => 'This Comment is already added.',
            'commissionaires_understandings.max' => 'Comment should not exceed 255 characters.',
            'short_name.max' => 'Short name should not exceed 100 characters.',
            'order_sequence.required' => 'Order sequence is required.',
            'order_sequence.unique' => 'This order sequence is already added.',
            'order_sequence.min' => 'Minimum order sequence should be 1.',
            'order_sequence.max' => 'Maximum order sequence should not be greater than 999.',
        ];
    }
}
