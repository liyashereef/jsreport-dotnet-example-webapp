<?php

namespace Modules\Timetracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DispatchCoordinatesSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'idle_time' => 'required|numeric',
                        'id' => 'required|exists:dispatch_coordinates_idle_settings,id'
                    ];
                }
            default: return [];
        }
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
