<?php

namespace Modules\Contracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Attachmainfilerequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cmuf_contract_document' =>'required|mimes:doc,pdf,docx,xlsx|max:10000',
            



        ];
    }

    public function messages(){
        return [
            'cmuf_contract_document.required' => 'Contract document is mandatory ',
            
            
            
            
            


        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
