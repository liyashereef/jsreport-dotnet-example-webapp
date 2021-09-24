<?php

namespace Modules\Admin\Http\Requests;

class SiteNoteStatusLookupRequest extends Request
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
            'status' => "bail|required|max:250|unique:site_note_status_lookups,status,{$id},id,deleted_at,NULL",
            'order_sequence' => "bail|required|integer|min:1|max:100000|unique:site_note_status_lookups,order_sequence,{$id},id,deleted_at,NULL",
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
            'status.required' => 'Status is required.',
            'status.unique' => 'Status already exists',
        ];
    }

}
