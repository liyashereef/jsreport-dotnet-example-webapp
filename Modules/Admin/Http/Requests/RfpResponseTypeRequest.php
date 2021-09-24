<?php


namespace Modules\Admin\Http\Requests;


class RfpResponseTypeRequest extends Request
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
            'rfpResponseType' => "bail|required|max:200|unique:rfp_response_type_lookups,rfp_response_type,{$id},id,deleted_at,NULL",
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
            'rfpResponseType.required' => 'Response type is required.',
            'rfpResponseType.unique' => 'Response type is already added.',
            'rfpResponseType.max' => 'The response type should not exceed 200 characters.',
        ];
    }
}
