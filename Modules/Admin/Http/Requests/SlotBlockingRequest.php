<?php

namespace Modules\Admin\Http\Requests;

class SlotBlockingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        return $rules = [
            'slot_block_date' => "required|date",
            'slot_ids' => "required",
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
            'slot_block_date.required' => 'Date is required.',
            'slot_ids.required' => 'Slot is required.',
        ];
    }

}
