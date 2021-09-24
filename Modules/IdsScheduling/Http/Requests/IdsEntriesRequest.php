<?php

namespace Modules\IdsScheduling\Http\Requests;

class IdsEntriesRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'first_name' => "required|max:50",
            'last_name' => "required|max:50",
            'email' => "required|email|max:50",
            'phone_number' => "required|max:20",
            'postal_code' => "required|max:6",
            'ids_office_id' => "required",
            'ids_service_id' => "required",
            'ids_office_slot_id' => "required",
            'slot_booked_date' => "date|date_format:Y-m-d|after_or_equal:today",
            'federal_billing_employer'=>"required_if:is_federal_billing,1",
            'payment_reason'=>"required_if:ids_payment_reason_id,1",
            'g-recaptcha-response' => 'required|recaptcha',
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
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'phone_number.required' => 'Phone number is required.',
            'ids_office_id.required' => 'Office is required.',
            'ids_service_id.required' => 'Service is required.',
            'ids_office_slot_id.required' => 'Office slot is required.',
            'slot_booked_date.required' => 'Booking date is required.',
            'postal_code.required' => 'Postal code date is required.',
            'federal_billing_employer.required_if' => 'Employer is required.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot',
        ];
    }

}
