<?php

namespace Modules\Contracts\Http\Requests;



class PostOrderRequest extends Request
{

    /**
     * @param PostOrderRepository $postOrderRepository
     */
    public function __construct()
    {

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'postOrderTopic' => 'bail|required|exists:post_order_topics,id',
            'postOrderGroup' => 'bail|required|exists:post_order_groups,id',
            'project' => 'bail|required|exists:customers,id',
            'postOrderDescription' => 'bail|required|max:5000',
            'all_attachments' => 'bail|required',
        ];
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'postOrderTopic.required' => 'Topic is required.',
            'postOrderTopic.exists' => 'Invalid entry.',
            'postOrderGroup.required' => 'Group is required.',
            'postOrderGroup.exists' => 'Invalid entry.',
            'project.required' => 'Client name is required.',
            'project.exists' => 'Invalid entry.',
            'postOrderDescription.required' => 'Description is required.',
            'postOrderDescription.max' => 'Character limit must not exceed 5000.',
            'all_attachments.required' => 'Attachment is required.',
        ];
    }

}
