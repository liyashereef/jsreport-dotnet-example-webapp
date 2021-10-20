<?php

namespace Modules\IdsScheduling\Http\Requests;

class RescheduleBookingRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'id' => "required",
            'first_name' => "required|max:50",
            'last_name' => "required|max:50",
            'email' => "required|email|max:50",
            'phone_number' => "required|max:20",
            'ids_office_id' => "required",
            'ids_service_id' => "required",
            'slot_booked_date' => "nullable|date|date_format:Y-m-d|after_or_equal:today|required_if:is_office_change,1",
            'ids_office_slot_id'=>"required_with:slot_booked_date|required_if:is_office_change,1",
            'payment_reason'=>"required_if:ids_payment_reason_id,1",
            'is_mask_given'=>"required_if:is_client_show_up,1",
            'no_masks_given'=>"required_if:is_mask_given,1",
            // 'federal_billing_employer'=>"required_if:is_federal_billing,1",
            'notes'=>"max:300"
        ];
        $balanceFeeRules = [];
        $balance_fee = request('balance_fee');
        $refundStatuVal = request('refundStatuVal');
        // $is_federal_billing = request('is_federal_billing');
        // $is_candidate = request('is_candidate');
        if($balance_fee > 0 || is_null($balance_fee)){
            $balanceFeeRules = [
                'is_payment_received'=>"required_if:is_client_show_up,1",
                'ids_payment_reason_id'=>"required_if:is_payment_received,0",
                'ids_payment_method_id'=>"required_if:is_payment_received,1",
            ];
        }
        if($balance_fee < 0 && $refundStatuVal !=3){
            $balanceFeeRules = [
                'refund_status'=>"required_if:is_client_show_up,1",
                'ids_payment_reason_id'=>"required_if:is_federal_billing,1|required_if:is_candidate,1"
            ];
            // if($is_federal_billing == 1 || $is_candidate == 1){
            //     $balanceFeeRules = [
            //         'ids_payment_reason_id'=>"required"
            //     ];
            // }
        }
        $rules = array_merge($rules,$balanceFeeRules);
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
            'id.required' => 'Booking Id is required.',
            'first_name.required' => 'First Name is required.',
            'last_name.required' => 'Last Name is required.',
            'email.required' => 'Email is required.',
            'phone_number.required' => 'Phone Number is required.',
            'ids_office_id.required' => 'Office is required.',
            'ids_service_id.required' => 'Service is required.',
            'ids_office_slot_id.required_with' => 'Slot is required.',
            'slot_booked_date.required_if' =>'Reschedule date is required when office changes',
            'ids_office_slot_id.required_if' =>'Slot is required when office changes',
            'ids_payment_method_id.required_if' => 'Payment Type is required.',
            'is_payment_received.required_if' =>'Payment Received is required when client shows up',
            'ids_payment_reason_id.required' =>'Payment Reason is required',
            'ids_payment_reason_id.required_if' =>'Payment Reason is required',
            'payment_reason.required_if' => 'Other reason is required.',
            'is_mask_given.required_if' =>'Mask given is required',
            'no_masks_given.required_if' =>'Number of masks field is required',
            'refund_status.required_if' =>'Refund request field is required',
        ];
    }

    public function authorize()
    {
        return true;
    }

}
