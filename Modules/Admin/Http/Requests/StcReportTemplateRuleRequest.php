<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Rules\FirstMinValue;
use Modules\Admin\Rules\Greaterthan;
use Modules\Admin\Rules\LastMaxValue;
use Modules\Admin\Rules\MinvalueGreaterthanMaxvalue;
use Modules\Admin\Rules\TemplateColor;

class StcReportTemplateRuleRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $template_max_value = 100;
        $template_min_value = 0;
        $rule_colors = request('rule_color');
        $array_size = sizeof(request('min_value'));
        $row_size = sizeof(array_filter(request('min_value'), function ($x) {return isset($x);})) - 1;
        $min_value = request('min_value');
        $max_value = request('max_value');
        $selected_rule_color = array_filter($rule_colors);
        $rules = [];
        for ($i = 0; $i < $array_size; $i++) {
            $max_value[-1] = 0;
            $previous_max_value = $max_value[$i - 1];

            $min_max_rules = [
                'rule_color.' . $i => ['bail', 'not_in:Choose one', 'required_with:min_value.' . $i, 'required_with:max_value.' . $i, new TemplateColor($selected_rule_color)],
                'min_value.' . $i => ['bail', 'required_with:rule_color.' . $i, 'nullable', 'numeric', 'max:' . $template_max_value, new FirstMinValue($i, $template_min_value, $min_value[$i]), new MinvalueGreaterthanMaxvalue($min_value[$i], $previous_max_value, $i, config('globals.stc_report_color_precision'))],
                'max_value.' . $i => ['bail', 'required_with:rule_color.' . $i, 'nullable', 'numeric', 'max:' . $template_max_value, new Greaterthan($min_value[$i], $max_value[$i]), new LastMaxValue($i, $row_size, $template_max_value, $max_value[$i])],
            ];
            $rules = array_merge($rules, $min_max_rules);

        }
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
            'rule_color.*.required' => 'Color is required.',
            'rule_color.*.required_with' => 'Color is required.',
            'rule_color.*.not_in' => 'Color is required.',
            'min_value.*.required_with' => 'Min Value is required.',
            'max_value.*.required_with' => 'Max Value is required.',
            'min_value.*.numeric' => 'Min Value should be numeric value.',
            'max_value.*.numeric' => 'Max Value should be numeric value.',
            'max_value.*.min' => 'Max Value should be greater than min value.',
            'min_value.*.max' => 'Min Value should not be greater than 100.',
            'max_value.*.max' => 'Max Value should not be greater than 100.',
        ];
    }

}
