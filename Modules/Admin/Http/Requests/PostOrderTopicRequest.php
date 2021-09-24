<?php

namespace Modules\Admin\Http\Requests;

class PostOrderTopicRequest extends Request
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
            'topic' => "bail|required|regex:/^[a-zA-Z0-9 \-]+$/u|max:100|unique:post_order_topics,topic,{$id},id,deleted_at,NULL",
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
            'topic.required' => 'Topic is required.',
            'topic.unique' => 'Topic is already added.',
            'topic.max' => 'The topic should not exceed 100 characters.',
            'topic.regex' => 'Invalid format. Only use alphabets,hyphen and numbers',
        ];
    }

}
