<?php

namespace Modules\Admin\Http\Requests;


class RfpAwardDateLookupRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        return $rules = [
     
            'award_dates' => "bail|required|numeric",
        ];
   
        }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
        'award_dates.required' => 'Award Date is required.',
        'award_dates.numeric' => 'Award Date is only accept numbers.',
        ];
    }
}
