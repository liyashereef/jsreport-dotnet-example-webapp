<?php

namespace Modules\Client\Http\Requests;

use Illuminate\Validation\Rule;

class VisitorRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->get('id');

        return  [
            'customerId' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'visitorTypeId' => 'required',
            'visitorStatusId' => 'required',
            'email' => [
                'nullable',
                'email',
                Rule::unique('visitors')->where(function ($query) use ($id) {
                    $query->where('customerId', $this->get('customerId'));
                    $query->whereNull('deleted_at');
                    if (!empty($id)) {
                        $query->where('id', '!=', $id);
                    }
                })
            ],
            'phone' => [
                'nullable',
                Rule::unique('visitors')->where(function ($query) use ($id) {
                    $query->where('customerId', $this->get('customerId'));
                    $query->whereNull('deleted_at');
                    if (!empty($id)) {
                        $query->where('id', '!=', $id);
                    }
                })
            ],
            'barCode' => [
                'nullable', Rule::unique('visitors')->where(function ($query) use ($id) {
                    $query->where('customerId', $this->get('customerId'));
                    $query->whereNull('deleted_at');
                    if (!empty($id)) {
                        $query->where('id', '!=', $id);
                    }
                })
            ],
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
            'customerId.required' => 'Customer is required.',
            'firstName.required' => 'First name is required.',
            'lastName.required' => 'Last name is required.',
            'visitorTypeId.required' => 'Visitor type is required.',
            'visitorStatusId.required' => 'Visitor status is required.',
        ];
    }
}
