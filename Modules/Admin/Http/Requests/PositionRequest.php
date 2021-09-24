<?php

namespace Modules\Admin\Http\Requests;

class PositionRequest extends Request
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
            'position' => "bail|required|max:255|unique:position_lookups,position,{$id},id,deleted_at,NULL",
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
            'position.required' => 'Position is required.',
            'position.unique' => 'This Position is already added.',
            'position.max' => 'The Position should not exceed 255 characters.',
        ];
    }

}
