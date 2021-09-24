<?php

namespace Modules\Admin\Http\Requests;

class ContractCellphoneProviderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $ratechangetitile = request();
        return $rules = [
            'cellphoneprovidertitle' => "required",
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
            'cellphoneprovidertitle.required' => 'Who provides cellphone is required.',

        ];
    }

}
