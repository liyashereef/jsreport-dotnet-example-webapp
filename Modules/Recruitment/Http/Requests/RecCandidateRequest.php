<?php

namespace Modules\Recruitment\Http\Requests;

use DB;
use Illuminate\Support\Facades\Input;
use Modules\Admin\Models\SecurityGuardLicenceThreshold;
use Carbon\Carbon;

class RecCandidateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $threshold = SecurityGuardLicenceThreshold::first();
        $guard_licence = Input::get('guard_licence');
        $use_of_force = Input::get('use_of_force');
        $applied_employment = Input::get('applied_employment');
        $employed_by_corps = Input::get('employed_by_corps');
        $veteran_of_armedforce = Input::get('veteran_of_armedforce');
        $criminal_convicted = Input::get('criminal_convicted');
        $current_employee_commissionaries = Input::get('current_employee_commissionaries');
        $today=Carbon::today();
        $force_doc_id = Input::get('force_file');
        $test_score_doc_id = Input::get('test_score_path');
        if ($guard_licence == "Yes") {
            $security_clearance = Input::get('security_clearance');
            $start_date_guard_license = Input::get('start_date_guard_license');
            $diff_date =Carbon::today()->subMonth($threshold['threshold']);
        }
        if ($use_of_force == "Yes") {
            $use_of_force_lookups_id = Input::get('use_of_force_lookups_id');
            $force_expiry = Input::get('force_expiry');
            $force_file = Input::get('force_file');
        }

        $rules = [
            'first_name' => 'bail|required|regex:/^[a-zA-Z\s.\-]+$/u|max:255',
            'last_name' => 'bail|required|regex:/^[a-zA-Z\s.\-]+$/u|max:255',
            'dob' => 'bail|date_format:"Y-m-d"|required|before:today',
            'email' => 'bail|required|email|max:255', //|unique:candidate_screening,email_candidate,position_code,$this->position_code',
            'phone_home' => 'sometimes|nullable|max:13|min:13',
            'phone_cellular' => 'sometimes|nullable|max:13|min:13',
            'address' => 'bail|required|max:255',
            'city' => 'bail|required|max:255',
            'postal_code' => 'bail|required|regex:/^[a-zA-Z][0-9][a-zA-Z][0-9][a-zA-Z][0-9]$/|max:6|min:6',
            //'fit_assessment_why_apply_for_this_job' => 'bail|required|max:500',
            'candidate_commissionaires_understandings_id' => 'bail|required',
            'guard_licence' => 'bail|required',
            'years_security_experience' => 'bail|min:0|max:5',
            /* 'site_supervisor' => 'bail|required|numeric|between:0,99',
            'shift_leader' => 'bail|required|numeric|between:0,99',
            'foot_patrol' => 'bail|required|numeric|between:0,99',
            'concierge' => 'bail|required|numeric|between:0,99',
            'security_guard' => 'bail|required|numeric|between:0,99',
            'access_control' => 'bail|required|numeric|between:0,99',
            'cctv_operator' => 'bail|required|numeric|between:0,99',
            'mobile_patrols' => 'bail|required|numeric|between:0,99',
            'investigations' => 'bail|required|numeric|between:0,99',
            'loss_prevention_officer' => 'bail|required|numeric|between:0,99',
            'operations' => 'bail|required|numeric|between:0,99',
            'dispatch' => 'bail|required|numeric|between:0,99',
            'other' => 'bail|required|numeric|between:0,99', */
            'wage_expectations' => 'bail|required|numeric',
            // 'wage_expectations_to' => 'bail|required|numeric|greater_than_field:wage_expectations_from',
            'wage_last_hourly' => 'bail|required|numeric',
            'current_paystub' => 'bail|required',
            'explanation_wage_expectation' => 'bail|required|max:500',
            'wage_last_provider' => 'bail|required',
            'last_role_held' => 'bail|required',
            'skill.*' => 'bail|required',
            'availability_start' => 'bail|required|date_format:"Y-m-d"',
            'current_availability' => 'bail|required',
            'availability_explanation' => 'required_if:current_availability,==,Part-Time (Less than 40 hours per week)',
            'understand_shift_availability' => 'bail|required',
            'available_shift_work' => 'bail|required|max:255',
            'explanation_restrictions' => 'required_if:available_shift_work,==,No|max:500',
            'work_status_in_canada' => 'bail|required',
            'years_lived_in_canada' => 'bail|required|min:0',
            'prepared_for_security_screening' => 'bail|required',
            'no_clearance' => 'bail|required',
            'no_clearance_explanation' => 'required_if:no_clearance,==,Yes|max:500',
            'prev_address_from.*' => 'bail|date_format:"Y-m-d"|required|before:today',
            'prev_address_to.*' => 'bail|date_format:"Y-m-d"|required|after:prev_address_from.*',
            'prev_address.*' => 'bail|required',
            'access_vehicle' => 'bail|required',
            'access_public_transport' => 'bail|required',
            'transportation_limitted' => 'bail|required',
            'explanation_transport_limit' => 'required_if:transportation_limitted,==,Yes|max:500',
            //Employment history
            'employement_start_date.*' => 'bail|required|date_format:"Y-m-d"|before:today',
            'employement_end_date.*' => 'bail|required|date_format:"Y-m-d"|after:employement_start_date.*',
            'employer.*' => 'bail|required|max:255',
            'employement_role.*' => 'bail|required|max:255',
            'employement_duties.*' => 'bail|required|max:255',
            'employement_reason.*' => 'bail|required|max:255',
            // //Reference
            'reference_name.*' => 'bail|required|max:255',
            'reference_employer.*' => 'bail|required|max:255',
            'reference_position.*' => 'bail|required|max:255',
            'contact_phone.*' => 'bail|required|max:13',
            'contact_email.*' => 'bail|required|email|max:255',
            // // Education
            'start_date_education.*' => 'bail|required|date_format:"Y-m-d"|before:today',
            'end_date_education.*' => 'bail|required|date_format:"Y-m-d"|after:start_date_education.*',
            'grade.*' => 'bail|required|max:255',
            'program.*' => 'bail|required|max:255',
            'school.*' => 'bail|required|max:255',
            'speaking_english' => 'bail|required',
            'reading_english' => 'bail|required',
            'writing_english' => 'bail|required',
            'speaking_french' => 'bail|required',
            'reading_french' => 'bail|required',
            'writing_french' => 'bail|required',
            'use_of_force' => 'bail|required',
            'current_employee_commissionaries' => 'bail|required',
            'applied_employment' => 'bail|required',
            'employed_by_corps' => 'bail|required',
            'veteran_of_armedforce' => 'bail|required',
            'spouse_of_armedforce' => 'bail|required',
            'dismissed' => 'bail|required',
            'explanation_dismissed' => 'required_if:dismissed,==,Yes|max:500',
            'limitations' => 'bail|required',
            'limitation_explain' => 'required_if:limitations,==,Yes|max:500',
            'criminal_convicted' => 'bail|required',
            'offence' => 'required_if:criminal_convicted,==,Yes',
            'offence_date' => 'required_if:criminal_convicted,==,Yes',
            'offence_location' => 'required_if:criminal_convicted,==,Yes',
            'career_interest' => 'bail|required',
            'other_roles' => 'bail|required',
            'wage_last_provider_other' => 'required_if:wage_last_provider,==,15|max:255',
            'security_provider_strengths' => 'bail|required|max:1000',
            'security_provider_notes' => 'bail|required|max:1000',
            'rate_experience' => 'bail|required',
            'optradio' => 'bail|required|in:Yes,No',
        ];
        $positions_lookups = DB::table('position_lookups')
            ->whereNull('deleted_at')
            ->pluck('position', 'id')
            ->toArray();
        if (is_array($positions_lookups)) {
            foreach ($positions_lookups + array(0 => 'Other') as $each_position) {
                $control_name = str_replace(' ', '_', strtolower($each_position));
                $rules_pos[$control_name] = 'bail|required|numeric|between:0,99';
            }
            $rules = array_merge($rules, $rules_pos);
        }
        if ($guard_licence == "Yes") {
            $rule_guard = [
                'start_date_guard_license' => 'bail|required|date_format:"Y-m-d"|before:today',
                'start_date_first_aid' => 'bail|required|date_format:"Y-m-d"|before:today',
                'start_date_cpr' => 'bail|required|date_format:"Y-m-d"|before:today',
                'expiry_guard_license' => 'bail|required|date_format:"Y-m-d"|after:start_date_guard_license',
                'expiry_first_aid' => 'bail|required|date_format:"Y-m-d"|after:start_date_first_aid',
                'expiry_cpr' => 'bail|required|date_format:"Y-m-d"|after:start_date_cpr',
                'security_clearance' => 'bail|required',
            ];
            $rules = array_merge($rules, $rule_guard);
        }
        if ($use_of_force == "Yes") {
            if (isset($force_doc_id)) {
                $rule_force = [
                    'use_of_force_lookups_id' => 'bail|required',
                    'force_expiry' => 'bail|required|date_format:"Y-m-d"|after:today',
                    'uof_path' => 'bail|required'
                ];
            } else {
                $rule_force = [
                    'use_of_force_lookups_id' => 'bail|required',
                    'force_expiry' => 'bail|required|date_format:"Y-m-d"|after:today',
                    'uof_path' => 'bail|required',
                ];
            }

            $rules = array_merge($rules, $rule_force);
        }
        if (isset($security_clearance) && ($security_clearance == "Yes")) {
            $rule_security_clearance = [
                'security_clearance_expiry_date' => 'bail|required|date_format:"Y-m-d"|after_or_equal:today',
                'security_clearance_type' => 'bail|required',
            ];
            $rules = array_merge($rules, $rule_security_clearance);
        }
        if ($applied_employment == "Yes") {
            $rule_comm = [
                'start_date_position_applied' => 'bail|required|date_format:"Y-m-d"|before:today',
                'end_date_position_applied' => 'bail|required|date_format:"Y-m-d"|after:start_date_position_applied',
                'position_applied' => 'bail|required',
            ];
            $rules = array_merge($rules, $rule_comm);
        }
        if ($employed_by_corps == "Yes") {
            $rule_employed = [
                'position_employed' => 'bail|required|max:100',
                'start_date_employed' => 'bail|required|date_format:"Y-m-d"|before:today',
                'end_date_employed' => 'bail|required|date_format:"Y-m-d"|after:start_date_employed',
                'location_employed' => 'bail|required',
                'employee_num' => 'bail|required|numeric|digits:6',
            ];
            $rules = array_merge($rules, $rule_employed);
        }
        if ($veteran_of_armedforce == "Yes") {
            $rule_canadian = [
                'service_number' => 'bail|required|max:15',
                'canadian_force' => 'bail|required|max:255',
                'enrollment_date' => 'bail|required|date_format:"Y-m-d"|before:today',
                'release_date' => 'bail|required|date_format:"Y-m-d"|after:enrollment_date',
                'item_release_number' => 'bail|required|max:255',
                'rank_on_release' => 'bail|required|max:255',
                'military_occupation' => 'bail|required|max:255',
                'reason_for_release' => 'bail|required|max:255',
            ];
            $rules = array_merge($rules, $rule_canadian);
        }
        if ($criminal_convicted == "Yes") {
            $rule_criminal = [
                'offence' => 'bail|required|max:255',
                'offence_location' => 'bail|required|max:100',
                'offence_date' => 'bail|required|date_format:"Y-m-d"|before:today',
            ];
            $rules = array_merge($rules, $rule_criminal);
        }
        if ($current_employee_commissionaries == "Yes") {
            $rule_current_employee = [
                'employee_number' => 'bail|required|numeric|digits:6',
                'currently_posted_site' => 'bail|required|max:255',
                'position' => 'bail|required',
                'hours_per_week' => 'bail|required|numeric|max:255',
            ];
            $rules = array_merge($rules, $rule_current_employee);
        }
        if ($guard_licence == "Yes") {
            if ($start_date_guard_license>=Carbon::parse($diff_date)->format('Y-m-d') && $start_date_guard_license <= Carbon::parse($today)->format('Y-m-d')) {
                if (isset($test_score_doc_id)) {
                    $rule_threshold = [
                    'test_score_percentage' => 'bail|required|numeric|max:100',
                    ];
                } else {
                    $rule_threshold = [
                    'test_score_percentage' => 'bail|required|numeric|max:100',
                    'test_score_document_id' => 'bail|required|mimetypes:application/pdf,image/jpeg,image/png,image/webp,image/gif,image/bmp,image/svg+xml|max:3072',
                    ];
                }
                $rules = array_merge($rules, $rule_threshold);
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'prev_address_from.*.required' => 'The Starting address is required',
            'prev_address_from.*.date_format' => 'Please enter the date in Y-m-d format',
            'prev_address_from.*.before' => 'Start date must be a date before today',
            'prev_address_to.*.required' => 'The End address is required',
            'prev_address_to.*.date_format' => 'Please enter the date in Y-m-d format',
            'prev_address.*.required' => 'Previous Address field is required',
            'prev_address_to.*.after' => 'End Date should be after Start date ',
            'between' => 'The value must be in between 0 and 99',
            'optradio.required' => 'Please choose any option',
            'guard_licence.required' => 'Please select the licensing information',
            'start_date_guard_license.required' => 'Start date of  Guarding licence is required since security guarding licence chosen is Yes',
            'start_date_guard_license.date_format' => 'Please enter the date in Y-m-d format',
            'start_date_first_aid.required' => 'Start date of  First Aid licence is required since security guarding licence chosen is Yes',
            'start_date_first_aid.date_format' => 'Please enter the date in Y-m-d format',
            'start_date_cpr.required' => 'Start date of  CPR certification is required since security guarding licence chosen is Yes',
            'start_date_cpr.date_format' => 'Please enter the date in Y-m-d format',
            'expiry_guard_license.required' => 'Expiry date of  Guarding licence is required since security guarding licence chosen is Yes',
            'expiry_guard_license.date_format' => 'Please enter the date in Y-m-d format',
            'expiry_first_aid.required' => 'Expiry date of  First Aid licence  is required since security guarding licence chosen is Yes',
            'expiry_first_aid.date_format' => 'Please enter the date in Y-m-d format',
            'expiry_cpr.required' => 'Expiry date of  CPR certification is required since security guarding licence chosen is Yes',
            'expiry_cpr.date_format' => 'Please enter the date in Y-m-d format',
            'security_clearance.required' => 'Security clearance is required since guarding licence chosen is Yes',
            'security_clearance_type.required' => 'Type of  security clearance is required since security clearance chosen is Yes',
            'security_clearance_expiry_date.required' => 'Expiry date of  security clearance is required since security clearance chosen is Yes',
            'security_clearance_expiry_date.date_format' => 'Please enter the date in Y-m-d format',
            'availability_explanation.required_if' => 'Briefly explain the reason for your availability only for Part-Time(Less than 40 hours per week)',
            'availability_explanation.required' => 'Briefly explain the reason for your availability only for Part-Time(Less than 40 hours per week)',
            'explanation_restrictions.required_if' => 'Please explain your restrictions for shift work',
            'explanation_restrictions.required' => 'Please explain your restrictions for shift work',
            'years_lived_in_canada.required' => 'Please enter 0 if you have not lived in canada',
            'no_clearance_explanation.required_if' => 'Please explain the reason you may NOT be granted a clearance',
            'no_clearance_explanation.required' => 'Please explain the reason you may NOT be granted a clearance',
            'explanation_transport_limit.required_if' => 'Please explain the reason for transportation limit your availability',
            'explanation_transport_limit.required' => 'Please explain the reason for transportation limit your availability',
            'position_applied.required' => 'Enter the position held with Commissionaires Great Lakes?',
            'start_date_position_applied.required' => 'Enter the start date for employment with Commissionaires Great Lakes?',
            'start_date_position_applied.date_format' => 'Please enter the date in Y-m-d format',
            'end_date_position_applied.required' => 'Enter the end date for employment with Commissionaires Great Lakes?',
            'end_date_position_applied.date_format' => 'Please enter the date in Y-m-d format',
            'explanation_wage_expectation.required' => 'Justify Wage Expectation',
            'position_employed.required_if' => 'Enter the position held with Corps of Commissionaires?',
            'position_employed.required' => 'Enter your position',
            'start_date_employed.required' => 'Enter the start date for employment with Corps of Commissionaires?',
            'start_date_employed.date_format' => 'Please enter the date in Y-m-d format',
            'end_date_employed.date_format' => 'Please enter the date in Y-m-d format',
            'end_date_employed.required' => 'Enter the end date for employment with Corps of Commissionaires?',
            'location_employed.required' => 'Select the division of working with Corps of Commissionaires?',
            'employee_num.required_if' => 'Enter the employee number with Corps of Commissionaires?',
            'employee_num.required' => 'Enter the employee number with Corps of Commissionaires?',
            'service_number.required_if' => 'Enter your service number held with Canadian Armed Forces, our Allies, or RCMP?',
            'service_number.required' => 'Enter your service number held with Canadian Armed Forces, our Allies, or RCMP?',
            'canadian_force.required_if' => 'Enter your Canadian Forces Branch or RCMP?',
            'canadian_force.required' => 'Enter your Canadian Forces Branch or RCMP?',
            'enrollment_date.required' => 'Enter your Enrollment date?',
            'enrollment_date.date_format' => 'Please enter the date in Y-m-d format',
            'release_date.required' => 'Enter your Release date?',
            'release_date.date_format' => 'Please enter the date in Y-m-d format',
            'item_release_number.required_if' => 'Enter your item release number?',
            'item_release_number.required' => 'Enter your item release number?',
            'rank_on_release.required_if' => 'Enter your rank on release?',
            'rank_on_release.required' => 'Enter your rank on release?',
            'military_occupation.required_if' => 'Enter your military occupation?',
            'military_occupation.required' => 'Enter your military occupation?',
            'reason_for_release.required_if' => 'Enter your  reason for release?',
            'reason_for_release.required' => 'Enter your  reason for release?',
            'spouse_of_armedforce.required'=>'This field is required',
            'explanation_dismissed.required_if' => 'Enter the reason for any dismissals or asked to resign from employment?',
            'explanation_dismissed.required' => 'Enter the reason for any dismissals or asked to resign from employment?',
            'limitation_explain.required_if' => 'Enter the disposition we could take in order to assist you in performing security guard duties',
            'wage_last_provider_other.required_if' => 'Enter name of security provider',
            'limitation_explain.required' => 'Enter the disposition we could take in order to assist you in performing security guard duties',
            'offence.required' => 'Enter the criminal offence happened',
            'offence_date.required' => 'Enter the criminal offence occured date?',
            'offence_date.date_format' => 'Please enter the date in Y-m-d format',
            'offence_location.required' => 'Enter the criminal offence occured location?',
            'current_paystub.required' => 'Please select an option',
            'postal_code.max' => 'Please enter postal code with 6 characters',
            'postal_code.min' => 'Please enter postal code with 6 characters',
            'postal_code.required' => 'Please enter postal code with 6 characters',
            'city.required' => 'Please enter city',
            'current_availability.required' => 'Enter the current availability status',
            'address.required' => 'Please enter address',
            'understand_shift_availability.required' => 'This field is required',
            'available_shift_work.required' => 'Choose whether you are available for shift works',
            'email.required' => 'Please enter email',
            'availability_start.required' => 'Please select the date',
            'availability_start.date_format' => 'Please enter the date in Y-m-d format',
            'contact_email.email' => 'Please enter a valid candidate email address',
            'fit_assessment_why_apply_for_this_job.required' => 'Please enter the fit assessment',
            'legal_name.required' => 'Please enter the full legal name',
            'legal_name.regex' => 'Please enter only text',
            'site_supervisor.required' => 'Please enter 0 if you have no  site supervisor experience.',
            'shift_leader.required' => 'Please enter 0 if you have no  shift leader experience.',
            'foot_patrol.required' => 'Please enter 0 if you have no  foot patrol experience.',
            'concierge.required' => 'Please enter 0 if you have no  concierge experience.',
            'security_guard.required' => 'Please enter 0 if you have no security guards experience.',
            'access_control.required' => 'Please enter 0 if you have no  access control experience.',
            'cctv_operator.required' => 'Please enter 0 if you have no  cctv operator experience.',
            'mobile_patrols.required' => 'Please enter 0 if you have no  mobile patrol experience.',
            'investigations.required' => 'Please enter 0 if you have no investigations experience.',
            'loss_prevention_officer.required' => 'Please enter 0 if you have no  loss preventionofficer experience.',
            'operations.required' => 'Please enter 0 if you have no  operations experience.',
            'dispatch.required' => 'Please enter 0 if you have no  dispatch experience.',
            'other.required' => 'Please enter 0 if you have no other experience.',
            'wage_expectations_to.required' => 'Please enter maximum wage expectation.',
            'wage_expectations_from.required' => 'Please enter minimum wage expectation.',
            'wage_expectations_to.greater_than_field' => 'Enter value greater than minimum wage expected',
            'explanation_wage_expectation' => 'Enter the reason for the wage expectation',
            'wage_last_hourly.required' => 'Please enter last hourly wage.',
            'shift_work.required' => 'Please select the availability for shift work',
            'start_date_guard_license.before' => 'Start date must be a date before today',
            'start_date_first_aid.before' => 'Start date must be a date before today',
            'start_date_cpr.before' => 'Start date must be a date before today',
            'expiry_guard_license.after' => 'End date should be greater than licence start date',
            'expiry_first_aid.after' => 'End date should be greater than the first aid start date',
            'expiry_cpr.after' => 'End date should be greater than the CPR start date',
            'start_date_position_applied.before' => 'Start date must be a date before today',
            'end_date_position_applied.after' => 'End date must be a date after start date',
            'start_date_employed.before' => 'Start date must be a date before today',
            'end_date_employed.after' => 'End date must be a date after start date',
            'enrollment_date.before' => 'Enrollment date must be a date before today',
            'release_date.after' => 'Release date must be a date after enrollment date',
            'offence_date.before' => 'Offence date must be a date before today',
            'current_available.required' => 'Please enter your current availability',
            'work_status_in_canada.required' => 'Please select your work status',
            'prepared_for_security_screening.required' => 'Please select whether you are prepared for security screening',
            'no_clearance.required' => 'Please select your reason',
            'access_vehicle.required' => 'Please select your access option',
            'access_public_transport.required' => 'Please select your access option to public transport',
            'transportation_limitted.required' => 'Please select your transportation limit',
            // 'phonest.required' => 'Please enter your contact number',
            'speaking_english.required' => 'Please select speaking/oral comprehension skills',
            'reading_english.required' => 'Please select your reading skills',
            'writing_english.required' => 'Please select your writing skills',
            'speaking_french.required' => 'Please select speaking/oral comprehension skills',
            'reading_french.required' => 'Please select your reading skills',
            'writing_french.required' => 'Please select your writing skills',
            'microsoft_word.required' => 'Please select your skills in microsoft word',
            'microsoft_excel.required' => 'Please select your skills in microsoft excel',
            'microsoft_power.required' => 'Please select your skills in microsoft powerpoint',
            'custom_service.required' => 'Please select your skills in customer service',
            'leadership.required' => 'Please select your skills in leadership',
            'employee_num.numeric' => 'Employee number should be an numbers only',
            'pblmsolve.required' => 'Please select your skills in problem solving and critical thinking',
            'time_mngmnt.required' => 'Please select your skills in time management',
            'current_employee_commissionaries.required' => 'Please select any option',
            'applied_employment.required' => 'Please select your  option for Commissionaires Great Lakes',
            'employed_by_corps.required' => 'Please select your option for corps of Commissionaires',
            'veteran_of_armedforce.required' => 'Please select your option for military experience',
            'dismissed.required' => 'Please select your option for dismissals',
            'limitations.required' => 'Please select your option for limitation',
            'criminal_convicted.required' => 'Please select your option for criminal convictions',
            'career_interest.required' => 'Please select your option for long term career interests',
            'other_roles.required' => 'Please select your option for other roles',
            'wage_last_provider.required' => 'Please select security provider',
            'last_role_held.required' => 'Please select your previous role',
            'years_security_experience.min' => 'Please enter 0 if you have no experience',
            'employee_number.required' => 'Please fill your employee number',
            'employee_number.max' => 'The employee number must be 6 digits',
            'employee_number.min' => 'The employee number must be 6 digits',
            'currently_posted_site.required' => 'Please fill your currently posted site',
            'position.required' => 'Please choose position',
            'hours_per_week.required' => 'Please fill hours per week',
            'employee_num.required' => 'Please enter employee number',
            'employee_num.max' => 'The employee number must be 6 digits',
            'employee_num.min' => 'The employee number must be 6 digits',
            'service_number.required' => 'Please enter service number',
            'item_release_number.required' => 'Please enter Item Release number',
            //Employment History
            'employement_start_date.*.required' => 'Please Enter the start date',
            'employement_end_date.*.required' => 'Please Enter the end date',
            'employement_start_date.*.date_format' => 'Please enter the date in Y-m-d format',
            'employement_end_date.*.date_format' => 'Please enter the date in Y-m-d format',
            'employer.*.required' => 'Please Enter the employer name',
            'employer.*.max' => 'Character limit exceeded(255 characters)',
            'employement_role.*.required' => 'Please Enter the employer role',
            'employement_role.*.max' => 'Character limit exceeded(255 characters)',
            'employement_duties.*.required' => 'Please Enter the employer duties',
            'employement_duties.*.max' => 'Character limit exceeded(255 characters)',
            'employement_reason.*.required' => 'Please Enter the reason for leaving',
            'employement_reason.*.max' => 'Character limit exceeded(255 characters)',
            'employement_start_date.*.before' => 'Start date must be a date before today',
            'employement_end_date.*.after' => 'End date must be a date after start date',
            //Reference
            'reference_name.*.required' => 'Please Enter the name',
            'reference_name.*.max' => 'Character limit exceeded(255 characters)',
            'reference_employer.*.required' => 'Please Enter the Employer',
            'reference_employer.*.max' => 'Character limit exceeded(255 characters)',
            'reference_position.*.required' => 'Please Enter the Position',
            'reference_position.*.max' => 'Character limit exceeded(255 characters)',
            'contact_phone.*.required' => 'Please Enter the Phone number',
            'contact_phone.*.max' => 'Character limit exceeded(13 characters)',
            'contact_email.*.required' => 'Please Enter the email',
            //Education
            'start_date_education.*.required' => 'Please select the start date',
            'end_date_education.*.required' => 'Please select the end date',
            'start_date_education.*.date_format' => 'Please enter the date in Y-m-d format',
            'end_date_education.*.date_format' => 'Please enter the date in Y-m-d format',
            'grade.*.required' => 'Please enter the Grade',
            'grade.*.max' => 'Character limit exceeded(255 characters)',
            'program.*.required' => 'Please enter the Program',
            'program.*.max' => 'Character limit exceeded(255 characters)',
            'school.*.required' => 'Please enter the School',
            'school.*.max' => 'Character limit exceeded(255 characters)',
            'start_date_education.*.before' => 'Start date must be a date before today',
            'end_date_education.*.after' => 'End date must be a date after start date',
            //Additional fields
            'dob.date_format' => 'Please enter the date in Y-m-d format',
            'dob.before' => 'Date of Birth must be a date before today',
            'dob.required' => 'Date of Birth is required',
            'candidate_commissionaires_understandings_id.required' => 'Please share your understanding of Commissionaires ',
            'test_score_percentage.required'=>'Percentage is required',
            'test_score_document_id.required'=>'Please upload file',
            'test_score_document_id.mimetypes'=>'Please upload file of type image or pdf',
            'test_score_document_id.max'=>'File too large to upload',
            'test_score_percentage.max'=>'The percentage must be maximum 100'

        ];
    }
}
