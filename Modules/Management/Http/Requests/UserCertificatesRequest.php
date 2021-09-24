<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\UserRequest;


class UserCertificatesRequest extends UserRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $row_ids = request('row-no');
        $certificate_row_ids = request('certificate-row-no');
        $rules = [];
        if ($certificate_row_ids != null) {
            foreach ($certificate_row_ids as $id) {
                $certificate_rules = [
                    'certificate_' . $id => 'bail|nullable',
                    'expiry_' . $id => 'bail|required_with:certificate_' . $id . '|nullable|date',
                ];
                $rules = array_merge($rules, $certificate_rules);
            }
        }
        return $rules;
    }
    public function messages()
    {
        $id = request('id');
        $row_ids = request('row-no');
        $certificate_row_ids = request('certificate-row-no');
        $message = [];
        if ($certificate_row_ids != null) {
            foreach ($certificate_row_ids as $id) {
                $certificate_rules = [
                    'certificate_' . $id . '.not_in' => 'Please choose a certificate.',
                    'valid_until_' . $id . '.date' => 'Enter a valid date.',
                    'expiry_' . $id . '.required_with' => 'Enter valid until date.',
                ];
                $message = array_merge($message, $certificate_rules);
            }
        }
        return $message;
    }
}