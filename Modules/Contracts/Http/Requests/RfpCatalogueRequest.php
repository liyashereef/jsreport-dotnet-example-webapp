<?php

namespace Modules\Contracts\Http\Requests;



class RfpCatalogueRequest extends Request
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
            'rfpCatalogueTopic' => 'bail|required|regex:/^[a-zA-Z0-9 \-]+$/u|max:100',
            'rfpCatalogueGroup' => 'bail|required|exists:rfp_catalogue_groups,id',
            'rfpCatalogueDescription' => 'bail|required|max:5000',
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
            'rfpCatalogueTopic.required' => 'Topic is required.',
            'rfpCatalogueTopic.exists' => 'Invalid entry.',
            'rfpCatalogueTopic.regex' => 'Invalid format. Only use alphabets,hyphen and numbers',
            'rfpCatalogueGroup.required' => 'Group is required.',
            'rfpCatalogueGroup.exists' => 'Invalid entry.',
            'rfpCatalogueDescription.required' => 'Description is required.',
            'rfpCatalogueDescription.max' => 'Character limit must not exceed 5000.',
            'all_attachments.required' => 'Attachment is required.',
        ];
    }

}
