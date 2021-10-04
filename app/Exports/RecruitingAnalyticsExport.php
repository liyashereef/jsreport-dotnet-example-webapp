<?php

namespace App\Exports;

use App\User;
use Carbon\Carbon;
use Modules\Hranalytics\Models\CandidateJob;
use Maatwebsite\Excel\Concerns\FromArray;
use Modules\Admin\Models\TrackingProcessLookup;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;



class RecruitingAnalyticsExport implements WithColumnFormatting, FromArray, WithHeadings, ShouldAutoSize, WithEvents
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $result = CandidateJob::with([
            'candidateTracking',
            'candidate',
            'candidate.latestJobApplied',
            'candidateTracking.entered_by',
            'candidate.guardingExperience',
            'candidate.wageExpectation',
            'candidate.wageExpectation.wageprovider',
            'candidate.securityclearance',
            'candidate.securityproximity',
            'candidate.languages',
            'candidate.miscellaneous',
            'candidate.personality_scores',
            'job',
            'job.customer',
            'jobReassigned.customer',
            'job.positionBeeingHired',
            'feedback',
            'englishproficiency'

        ])
            ->where('status', 'Applied')
            ->get();
        return $this->getCandidateRecruitingStatus($result);
    }

    public function headings(): array
    {
        return [
            'Candidate Name',
            'Date Applied',
            'Initial Rating',
            'City',
            'Years Of Security Experience',
            'Last Wage In Industry',
            'Last Provider',
            'Last Provider - Other',
            'Working Status',
            'Years Lived In Canada',
            'Do You Have Driving Licence',
            'English Speaking',
            'English Reading',
            'English Writing',
            'Career Interest With Commissionaires',
            'Case Study Score',
            'English Proficiency',
            'Personality Score',
            'Candidate Email',
            'Candidate Phone',
            'Job Initially Applied To',
            'Client',
            'Date Required',
            'Number Of Positions Open',
            'Role',
            'Job Code Reassignment',
            'Client Reassignment',
            'Current Wage',
            'Reassigned Wage',
            'Process Number',
            'Process Step',
            'Completion Date',
            'Notes',
            'Entered By',
        ];
    }


    public function getCandidateRecruitingStatus($result)
    {
        $tracking_lookup = TrackingProcessLookup::all();
        $datatable_rows = array();
        $each_row = [];
        foreach ($result as $key => $each_list) {
            $all_candidate_request = collect($each_list->candidateTracking);
            foreach ($tracking_lookup as $value) {
                $each_row['candiate_name'] = ($each_list->candidate->name) ? $each_list->candidate->name : '';
                $each_row['date_applied'] = ($each_list->submitted_date != null) ? Date::stringToExcel($each_list->submitted_date) : '';
                $each_row['inital-rating'] = ($each_list->feedback != null) ? $each_list->feedback->feedback : '';
                $each_row['candiate_city'] = ($each_list->candidate->city) ? $each_list->candidate->city : '';
                $each_row['years_of_security_experiance'] = ($each_list->candidate->guardingExperience->years_security_experience) ? $each_list->candidate->guardingExperience->years_security_experience : '';
                $each_row['last_wage'] = ($each_list->candidate->wageExpectation) ? "$" . $each_list->candidate->wageExpectation->wage_last_hourly : '';;
                $each_row['last_provider'] = ($each_list->candidate->wageExpectation->wageprovider->security_provider) ? $each_list->candidate->wageExpectation->wageprovider->security_provider : '';
                $each_row['last_provider_other'] = ($each_list->candidate->wageExpectation->wage_last_provider_other) ? $each_list->candidate->wageExpectation->wage_last_provider_other : '';
                $each_row['working_status'] = ($each_list->candidate->securityclearance->work_status_in_canada) ? $each_list->candidate->securityclearance->work_status_in_canada : '';
                $each_row['years_lived_in_canada'] = ($each_list->candidate->securityclearance->years_lived_in_canada) ? $each_list->candidate->securityclearance->years_lived_in_canada : '';
                $each_row['drivers_licenece'] = ($each_list->candidate->securityproximity->driver_license) ? $each_list->candidate->securityproximity->driver_license : '';
                $each_row['english_speaking'] = ($each_list->candidate->languages[0]->language_id == 1) ? $each_list->candidate->languages[0]->speaking : '';
                $each_row['english_reading'] = ($each_list->candidate->languages[0]->language_id == 1) ? $each_list->candidate->languages[0]->reading : '';
                $each_row['english_writing'] = ($each_list->candidate->languages[0]->language_id == 1) ? $each_list->candidate->languages[0]->writing : '';
                $each_row['career_interest'] = ($each_list->candidate->miscellaneous->career_interest) ? $each_list->candidate->miscellaneous->career_interest : '';
                $each_row['case_study_score'] = ($each_list->average_score) ? $each_list->average_score : '';
                $each_row['english_proficiency'] = ($each_list->englishproficiency != null) ? $each_list->englishproficiency->english_ratings : '';
                $each_row['personality_score'] = isset($each_list->candidate->personality_scores[0]->score) ? $each_list->candidate->personality_scores[0]->score : '';
                $each_row['candiate_email'] = ($each_list->candidate->email) ? $each_list->candidate->email : '';
                $each_row['candiate_phone'] = ($each_list->candidate->phone_cellular) ? $each_list->candidate->phone_cellular : '';
                $each_row['job_intially_applied_to'] = ($each_list->job->unique_key) ? $each_list->job->unique_key : '';
                $each_row['client_name'] =  ($each_list->job->customer->client_name) ? $each_list->job->customer->client_name : '';
                $each_row['date_required'] = ($each_list->job->required_job_start_date) ? Date::stringToExcel($each_list->job->required_job_start_date) : '';
                $each_row['position_open'] =  ($each_list->job->no_of_vaccancies) ? $each_list->job->no_of_vaccancies : '';
                $each_row['position_role'] =  ($each_list->job->positionBeeingHired) ? $each_list->job->positionBeeingHired->position : '';
                $each_row['job_code_reassignment'] =  ($each_list->job_reassigned_id != 0) ? $each_list->jobReassigned->unique_key : 'none';
                $each_row['client_reassignment'] =  ($each_list->job_reassigned_id != 0) ? $each_list->jobReassigned->customer->client_name : '';
                $propossed_low_wage = ($each_list->proposed_wage_low != null) ? number_format((float)$each_list->proposed_wage_low, 2, '.', '') : number_format((float)$each_list->job->wage_low, 2, '.', '');
                $propossed_high_wage = ($each_list->proposed_wage_high != null) ? number_format((float)$each_list->proposed_wage_high, 2, '.', '') : number_format((float)$each_list->job->wage_high, 2, '.', '');
                $each_row['current_wage'] = "$" . $propossed_low_wage . " - $" . $propossed_high_wage;
                $each_row['reassigned_wage'] =  ($each_list->job_reassigned_id != 0) ? "$" . number_format((float)$each_list->jobReassigned->wage_low, 2, '.', '') . ' - $' . number_format((float)$each_list->jobReassigned->wage_high, 2, '.', '') : '';
                $each_row['process_number'] = $value->id;
                $each_row['process_step'] =  $value->process_steps;
                $each_row['completion_date'] = '';
                $each_row['notes'] = '';
                $each_row['entered_by'] = '';
                $candidate_tracking = $all_candidate_request->where('lookup_id', $value->id)->first();
                if (!empty($candidate_tracking)) {
                    $each_row['completion_date'] = Date::stringToExcel($candidate_tracking->completion_date);
                    $each_row['notes'] = $candidate_tracking->notes;
                    $each_row['entered_by'] = $candidate_tracking->entered_by->first_name . ' ' . $candidate_tracking->entered_by->last_name;
                }
                array_push($datatable_rows, $each_row);
            }
        }
        return $datatable_rows;
    }

    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:AH1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12)->setBold(true);
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
            'W' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'AF' => NumberFormat::FORMAT_DATE_DMYSLASH,
        ];
    }
}
