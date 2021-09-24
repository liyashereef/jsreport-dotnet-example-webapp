<?php

namespace Modules\Reports\Repositories;

use Illuminate\Support\Facades\Mail;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exports\RecruitingAnalyticsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Modules\Hranalytics\Models\CandidateJob;
use Modules\Admin\Models\TrackingProcessLookup;
use Illuminate\Support\Facades\Route;
use App\Jobs\FileDelete;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Modules\Reports\Emails\RecrutingAnalyticsReportEmail;


class RecruitingAnalyticsRepository
{
    protected $candidateJobModel, $trackingProcessLookupModel;

    public function __construct(
        CandidateJob $candidateJobModel,
        TrackingProcessLookup $trackingProcessLookupModel
    ) {
        $this->candidateJobModel = $candidateJobModel;
        $this->trackingProcessLookupModel = $trackingProcessLookupModel;
    }

    public function getCandidateRecruitingStatus()
    {

        $result = $this->candidateJobModel
            ->with([
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
        return $this->prepareCandidateRecruitingStatusReport($result);
    }

    public function prepareCandidateRecruitingStatusReport($result)
    {

        $tracking_lookup = $this->trackingProcessLookupModel->get();
        $datatable_rows = array();
        $each_row = [];
        foreach ($result as $key => $each_list) {
            $all_candidate_request = collect($each_list->candidateTracking);
            foreach ($tracking_lookup as $value) {
                $each_row["id"] = isset($each_list->id) ? $each_list->id : "--";
                $each_row['candiate_name'] = $each_list->candidate->name;
                $each_row['date_applied'] = ($each_list->submitted_date != null) ? Carbon::parse($each_list->submitted_date)->format('m/d/Y H:i') : '';
                $each_row['inital-rating'] = ($each_list->feedback != null) ? $each_list->feedback->feedback : '';
                $each_row['candiate_city'] = $each_list->candidate->city;
                $each_row['years_of_security_experiance'] = $each_list->candidate->guardingExperience->years_security_experience;
                $each_row['last_wage'] = ($each_list->candidate->wageExpectation) ? "$" . $each_list->candidate->wageExpectation->wage_last_hourly : '';
                $each_row['last_provider'] = $each_list->candidate->wageExpectation->wageprovider->security_provider;
                $each_row['last_provider_other'] = $each_list->candidate->wageExpectation->wage_last_provider_other;
                $each_row['working_status'] = $each_list->candidate->securityclearance->work_status_in_canada;
                $each_row['years_lived_in_canada'] = $each_list->candidate->securityclearance->years_lived_in_canada;
                $each_row['drivers_licenece'] = $each_list->candidate->securityproximity->driver_license;
                $each_row['english_speaking'] = ($each_list->candidate->languages[0]->language_id == 1) ? $each_list->candidate->languages[0]->speaking : '';
                $each_row['english_reading'] = ($each_list->candidate->languages[0]->language_id == 1) ? $each_list->candidate->languages[0]->reading : '';
                $each_row['english_writing'] = ($each_list->candidate->languages[0]->language_id == 1) ? $each_list->candidate->languages[0]->writing : '';
                $each_row['career_interest'] = $each_list->candidate->miscellaneous->career_interest;
                $each_row['case_study_score'] = $each_list->average_score;
                $each_row['english_proficiency'] = ($each_list->englishproficiency != null) ? $each_list->englishproficiency->english_ratings : '';
                $each_row['personality_score'] = isset($each_list->candidate->personality_scores[0]->score) ? $each_list->candidate->personality_scores[0]->score : '';
                $each_row['candiate_email'] = $each_list->candidate->email;
                $each_row['candiate_phone'] = $each_list->candidate->phone_cellular;
                $each_row['job_intially_applied_to'] = $each_list->job->unique_key;
                $each_row['client_name'] =  $each_list->job->customer->client_name;
                $each_row['date_required'] =  \Carbon::parse($each_list->job->required_job_start_date)->format('m/d/Y');
                $each_row['position_open'] =  $each_list->job->no_of_vaccancies;
                $each_row['position_role'] =  ($each_list->job->positionBeeingHired) ? $each_list->job->positionBeeingHired->position : '';
                $each_row['job_code_reassignment'] =  ($each_list->job_reassigned_id != 0) ? $each_list->jobReassigned->unique_key : 'none';
                $each_row['client_reassignment'] =  ($each_list->job_reassigned_id != 0) ? $each_list->jobReassigned->customer->client_name : '';
                $propossed_low_wage = ($each_list->proposed_wage_low != null) ? number_format((float)$each_list->proposed_wage_low, 2, '.', '') : number_format((float)$each_list->job->wage_low, 2, '.', '');
                $propossed_high_wage = ($each_list->proposed_wage_high != null) ? number_format((float)$each_list->proposed_wage_high, 2, '.', '') : number_format((float)$each_list->job->wage_high, 2, '.', '');
                $each_row['current_wage'] = "$" . $propossed_low_wage . " - $" . $propossed_high_wage;
                $each_row['reassigned_wage'] =  ($each_list->job_reassigned_id != 0) ? "$" . number_format((float)$each_list->jobReassigned->wage_low, 2, '.', '') . ' - $' . number_format((float)$each_list->jobReassigned->wage_high, 2, '.', '') : '';
                $each_row['process_step'] =  $value->process_steps;
                $each_row['process_number'] = $value->id;
                $each_row['notes'] = '';
                $each_row['completion_date'] = '';
                $each_row['entered_by'] = '';
                $candidate_tracking = $all_candidate_request->where('lookup_id', $value->id)->first();
                if (!empty($candidate_tracking)) {
                    $each_row['notes'] = $candidate_tracking->notes;
                    $each_row['completion_date'] = Carbon::parse($candidate_tracking->completion_date)->format('m/d/Y');
                    $each_row['entered_by'] = $candidate_tracking->entered_by->first_name . ' ' . $candidate_tracking->entered_by->last_name;
                }
                array_push($datatable_rows, $each_row);
            }
        }
        return $datatable_rows;
    }

    public function recruitingAnalyticsExcelReport($email)
    {

        Log::channel('reportLog')->info("Excel Export Started");
        $folder_name = "Report";
        $date = date('Y-m-d', strtotime(carbon::now()));
        $file_name = "recruting_analytics_report_" . $date . ".xlsx";

        if (!file_exists(storage_path('app/') . $folder_name)) {
            mkdir(storage_path('app/') . $folder_name, 0755, true);
        }
        // Store on a different disk with a defined writer type.
        try {
            if (Excel::store(new RecruitingAnalyticsExport(2020), $folder_name . '/' . $file_name)) {
                Log::channel('reportLog')->info("Excel report completed");
                $path = "app/Report/" . $file_name;
                $filepath = storage_path($path);
                $this->sendNotification($filepath, $email, $file_name);
            } else {
                Log::channel('reportLog')->info("Excel report not completed");
                throw new \Exception("Excel report not completed and File not created");
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('reportLog')
                ->error($errorMessage);
        }
    }

    /**
     * To send notificaion to candidates
     *
     * @param $candidate_id
     *  @param $filename
     * @return void
     */

    public function sendNotification($filepath, $email, $file_name)
    {
        Log::channel('reportLog')->info("Mail Function enetering" . $filepath);
        Log::channel('reportLog')->info("Mail Path");
        $to = $email;
        $mail = Mail::to($to);
        $send = $mail->send(new RecrutingAnalyticsReportEmail('mail.notification', $filepath, $file_name));
        Log::channel('reportLog')->info("mail Send");
        if (Mail::failures()) {
            Log::channel('fileDeleteJobLog')->info(Mail::failures());
        } else {
            Log::channel('fileDeleteJobLog')->info("file deletion starts");
            FileDelete::dispatch($filepath)->delay(now()->addDay(1));
            Log::channel('fileDeleteJobLog')->info("file deletion sucess");
        }
    }
}
