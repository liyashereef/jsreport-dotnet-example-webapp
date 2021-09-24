<?php

namespace Modules\Timetracker\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Timetracker\Models\SatelliteTrackingSetting;

class SatelliteTrackingSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $template_min_value = SatelliteTrackingSetting::RANGE_MIN;
        $template_max_value = SatelliteTrackingSetting::RANGE_MAX;
        $min_value = request('min_value');
        $max_value = request('max_value');

        $rules = [];

        for ($i = 0; $i < sizeof(request('min_value')); $i++) {

            $tmin = $min_value[$i] + 1;
            $tmax = $max_value[$i];

            $min_max_rules = [
                'rule_color.' . $i => ['bail', 'not_in:Choose one', 'required_with:min_value.' . $i, 'required_with:max_value.' . $i],
                'min_value.' . $i => [
                    'bail', 'required_with:rule_color.' . $i, 'nullable', 'numeric', 'min:' . $template_min_value,
                    'max:' . $template_max_value
                ],
                'max_value.' . $i => [
                    'bail', 'required_with:rule_color.' . $i, 'nullable', 'numeric', 'min:' . $tmin,
                    'max:' . $template_max_value
                ],
            ];

            $template_min_value = $tmax;
            $template_min_value++;

            $rules = array_merge($rules, $min_max_rules);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'rule_color.*.required' => 'Color is required.',
            'rule_color.*.required_with' => 'Color is required.',
            'rule_color.*.not_in' => 'Color is required.',
            'min_value.*.required_with' => 'Min Value is required.',
            'max_value.*.required_with' => 'Max Value is required.',
            'min_value.*.numeric' => 'Min Value should be numeric value.',
            'max_value.*.numeric' => 'Max Value should be numeric value.',
            'min_value.*.min' => 'Min Value should not be less than previous max value.',
            'min_value.*.max' => 'Min Value should not be greater than 100.',
            'max_value.*.max' => 'Max Value should not be greater than 100.',
            'max_value.*.min' => 'Max Value should be greater than min value.',
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
