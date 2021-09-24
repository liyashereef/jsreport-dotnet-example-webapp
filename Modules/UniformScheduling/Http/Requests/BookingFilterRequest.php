<?php

namespace Modules\UniformScheduling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'booked_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'booked_date.required' => 'Booking date is a mandatory field',
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
