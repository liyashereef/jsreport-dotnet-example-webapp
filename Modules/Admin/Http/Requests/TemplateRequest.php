<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Admin\Models\TemplateSetting;
use Modules\Admin\Rules\DateOverlap;

class TemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $start_date = request('start_date');
        $end_date = request('end_date');
        $template_min_value = TemplateSetting::value('min_value');
        $template_max_value = TemplateSetting::value('max_value');

        return [
            'template_name' => 'bail|required|max:100|unique:templates,template_name,' . $id . ',id,deleted_at,NULL',
            'template_description' => 'bail|sometimes|nullable|max:1000|',
            'start_date' => ['bail', 'required', 'date', new DateOverlap($id, $start_date, $end_date)],
            'end_date' => ['bail', 'required', 'date', 'after_or_equal:start_date', 'after:today', new DateOverlap($id, $start_date, $end_date)],
            'question_type.*' => 'bail|required|not_in:Choose one|',
            'parent_question.*' => 'bail|required|not_in:Choose one|',
            'question_text.*' => 'bail|required|max:500|',
            'answer_type.*' => 'bail|required|not_in:Choose one|',
            'multiple_answers.*' => 'bail|required|',
            'yes_value.*' => 'bail|nullable|regex:/^\d*(\.\d{1,4})?$/|numeric|min:' . $template_min_value . '|max:' . $template_max_value,
            'no_value.*' => 'bail|nullable|regex:/^\d*(\.\d{1,4})?$/|numeric|min:' . $template_min_value . '|max:' . $template_max_value,
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        $template_min_value = TemplateSetting::value('min_value');
        $template_max_value = TemplateSetting::value('max_value');
        return [
            'template_name.required' => 'Template name is required',
            'template_name.unique' => 'Template name already taken',
            'template_name.max' => 'Template name should not be more than 100 characters',
            'template_description.max' => 'Template description should not be more than 1000 characters',
            'start_date.required' => 'Start Date is required',
            'start_date.date' => 'Start Date must be a date',
            'start_date.after_or_equal' => 'Start Date must be a date after or equal to today date',
            'end_date.required' => 'End Date is required',
            'end_date.date' => 'End Date must be a date',
            'end_date.after_or_equal' => 'End Date must be a date after or equal to start date',
            'question_type.*.required' => 'Question type is required',
            'parent_question.*.required' => 'Parent question is required',
            'parent_question.*.not_in' => 'Parent question is required',
            'question_text.*.required' => 'Question text is required',
            'question_text.*.max' => 'Question text should not be more than 500 characters',
            'answer_type.*.required' => 'Answer type is required',
            'answer_type.*.not_in' => 'Answer type is required',
            'multiple_answers.*.required' => 'Multiple answers is required',
            'yes_value.*.numeric' => 'Score should be a number',
            'no_value.*.numeric' => 'Score should be a number',
            'yes_value.*.regex' => 'Enter positive number with 4 decimal places',
            'no_value.*.regex' => 'Enter positive number with 4 decimal places',
            'yes_value.*.min' => 'Score should be between ' . $template_min_value . ' to ' . $template_max_value,
            'no_value.*.min' => 'Score should be between ' . $template_min_value . ' to ' . $template_max_value,
            'yes_value.*.max' => 'Score should be between ' . $template_min_value . ' to ' . $template_max_value,
            'no_value.*.max' => 'Score should be between ' . $template_min_value . ' to ' . $template_max_value,
        ];
    }
}
