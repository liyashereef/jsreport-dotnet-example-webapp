<?php

namespace Modules\Hranalytics\Http\Requests;

use Carbon\Carbon;
use DateTime;
use Modules\Admin\Models\ScheduleMaximumHour;
use Modules\Admin\Models\ShiftTiming;

class ScheduleCustomerRequirementRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $type = request('type');
        $multiple_fill_id = config('globals.multiple_fill_id');
        $shift_hours_sum = 0;
        $maximum_overtime_hours = ScheduleMaximumHour::value('hours') * 60; //Convert hours to minutes
        $overtime_hours_notes = request('overtime_hours_notes');
        $shift_timing_id = request('shift_timing_id');

        if ($type == $multiple_fill_id) {
            /* Validation for total no of shift - Start */
            $start_date = request('start_date');
            $end_date = request('end_date');
            /* Validation for total no of shift - End */
        }

        $rules = [
            'customer_id' => 'bail|required|numeric',
            'site_rate' => ['required', 'numeric', 'regex:/^\d*(\.\d{1,3})?$/', 'between:1,9999999'],
            'type' => 'bail|required',
            'start_date' => 'bail|required|date|after_or_equal:today',
            'end_date' => 'bail|required|date|after_or_equal:start_date',
            'time_scheduled' => 'bail|nullable',
            'notes' => 'bail|nullable|max:500',
            'length_of_shift' => ['nullable', 'numeric', 'regex:/^\d*(\.\d{1,3})?$/', 'between:0,9999999999'],
            'require_security_clearance' => 'bail|nullable',
            'security_clearance_level' => 'bail|required_if:require_security_clearance,==,yes',
        ];

        if ($type == $multiple_fill_id) {
            $other_rules = [
                'shift_timing_id' => 'bail|required',
                'require_security_clearance' => 'bail|required',
            ];
            $rules = array_merge($rules, $other_rules);

            if (!empty($shift_timing_id)) {
                foreach ($shift_timing_id as $id) {
                    $shift_from = Carbon::parse(request('shift_from_' . $id));
                    $shift_to = Carbon::parse(request('shift_to_' . $id));
                    if ($shift_to->lessThan($shift_from)) {
                        $shift_to = Carbon::parse(request('shift_to_' . $id))->addDay(1);
                    }
                    $shift_hours_difference[] = $shift_from->diffInMinutes($shift_to);

                    if ($type == $multiple_fill_id) {
                        $startDate = new DateTime($start_date);
                        $endDate = new DateTime($end_date);
                        for ($date = $startDate; $date <= $endDate; $date->modify('+1 day')) {
                            $shift_timing_rules = [
                                'shift_from_' . $id . '_' . $date->format('d_m_Y') => 'bail|required',
                                'shift_to_' . $id . '_' . $date->format('d_m_Y') => 'bail|required',
                                'no_of_positions_' . $id . '_' . $date->format('d_m_Y') => 'bail|required|numeric|min:1',
                            ];
                            $rules = array_merge($rules, $shift_timing_rules);
                        }
                    } else {
                        $shift_timing_rules = [
                            'shift_from_' . $id => 'bail|required',
                            'shift_to_' . $id => 'bail|required',
                        ];
                        $rules = array_merge($rules, $shift_timing_rules);
                    }
                }
                if ((max($shift_hours_difference) > $maximum_overtime_hours) && ($overtime_hours_notes == null)) {
                    $maximum_overtime_hours = [
                        'overtime_shift_timing_id' => 'bail|max:0', // For overtime notes shift timing labels display
                        'overtime_notes' => 'bail|required',
                    ];
                    $rules = array_merge($rules, $maximum_overtime_hours);
                }
            }
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
        $days_shift = 0;
        $type = request('type');
        $multiple_fill_id = config('globals.multiple_fill_id');
        $maximum_overtime_hours = ScheduleMaximumHour::value('hours') * 60; //Convert hours to minutes
        $shift_timing_id = request('shift_timing_id');
        if ($type == $multiple_fill_id) {
            /* Validation for total no of shift - Start */
            $start_date = request('start_date');
            $end_date = request('end_date');
            /* Validation for total no of shift - End */
        }

        $messages = [
            'customer_id.required' => 'Please choose a project number',
            'notes.max' => 'Please enter the notes within 500 characters.',
            'time_scheduled.date_format' => 'Time Scheduled should match hh:mm AM/PM Format',
            'site_rate.required' => 'Site Rate is required',
            'site_rate.numeric' => 'Site Rate should be numeric value',
            'site_rate.regex' => 'Site Rate should have maximum 3 decimals',
            'site_rate.between' => 'Site Rate should be maximum 7 digits',
            'start_date.required' => 'Please enter the start date',
            'end_date.required' => 'Please enter the end date',
            'length_of_shift.required' => 'Please enter the length of shift',
            'length_of_shift.numeric' => 'Length of shift should be numeric value',
            'length_of_shift.regex' => 'Length of shift should have maximum 3 decimals',
            'length_of_shift.between' => 'Length of shift should be maximum 10 digits',
            'type.required' => 'Please choose assignment type',
            'security_clearance_level.required_if' => 'Choose security clearance level',
            'shift_timing_id.required' => 'Minimum one shift timing is required',
            'overtime_notes.required' => 'Overtime notes is required',
            'require_security_clearance.required' => 'Please select any option',
        ];

        if (!empty($shift_timing_id)) {
            foreach ($shift_timing_id as $id) {
                $shift_from = Carbon::parse(request('shift_from_' . $id));
                $shift_to = Carbon::parse(request('shift_to_' . $id));
                if ($shift_to->lessThan($shift_from)) {
                    $shift_to = Carbon::parse(request('shift_to_' . $id))->addDay(1);
                }
                $shift_hours_difference = $shift_from->diffInMinutes($shift_to);
                if ($shift_hours_difference > $maximum_overtime_hours) {
                    $overtime_shift_timing_ids[] = $id;
                }

                if ($type == $multiple_fill_id) {
                    $startDate = new DateTime($start_date);
                    $endDate = new DateTime($end_date);
                    for ($date = $startDate; $date <= $endDate; $date->modify('+1 day')) {
                        $shift_timing_messages = [
                            'shift_from_' . $id . '_' . $date->format('d_m_Y') => 'From time is required',
                            'shift_to_' . $id . '_' . $date->format('d_m_Y') => 'To time is required',
                            'no_of_positions_' . $id . '_' . $date->format('d_m_Y') => 'Number of Positions required',
                        ];
                        $messages = array_merge($messages, $shift_timing_messages);
                    }
                } else {
                    $shift_timing_messages = [
                        'shift_from_' . $id . '.required' => 'From time is required',
                        'shift_to_' . $id . '.required' => 'To time is required',
                    ];

                    $messages = array_merge($messages, $shift_timing_messages);
                }
            }

            /* For overtime notes shift timing labels display - Start */
            if (!empty($overtime_shift_timing_ids)) {
                $overtime_shift_timings = ShiftTiming::whereIn('id', $overtime_shift_timing_ids)->pluck('shift_name')->toArray();
                $overtime_shift_timings_name = implode(',', $overtime_shift_timings);
                $overtime_shift_timing_messages = [
                    'overtime_shift_timing_id.max' => '' . $overtime_shift_timings_name,
                ];
                $messages = array_merge($messages, $overtime_shift_timing_messages);
            }
            /* For overtime notes shift timing labels display - End */
        }
        return $messages;

    }

}
