<?php

namespace Modules\Admin\Http\Requests;

class PostOrderGroupRequest extends Request
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
            'group' => "bail|required|regex:/^[a-zA-Z0-9 \-]+$/u|max:100|unique:post_order_groups,group,{$id},id,deleted_at,NULL",
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
            'group.required' => 'Group is required.',
            'group.unique' => 'Group is already added.',
            'group.max' => 'The group should not exceed 100 characters.',
            'group.regex' => 'Invalid format. Only use alphabets,hyphen and numbers',
        ];
    }

}
