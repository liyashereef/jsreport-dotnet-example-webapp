<?php

namespace Modules\Recruitment\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Admin\Repositories\UserRepository;
use Modules\Recruitment\Models\RecCandidateMatchScore;
use App\Repositories\MailQueueRepository;
use Modules\Recruitment\Repositories\RecJobRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Recruitment\Repositories\RecCandidateRepository;
use Modules\Recruitment\Models\RecCandidateJobDetailsStatusLog;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\EmailTemplate;
use Modules\Recruitment\Repositories\RecCandidateTrackingRepository;
use Modules\Recruitment\Models\RecJob;
use Modules\Recruitment\Models\RecCandidateTracking;
use Modules\Admin\Models\User;
use DateTime;

class RecCandidateSelectionController extends Controller
{
    protected $helperService;

    public function __construct(
        HelperService $helperService,
        MailQueueRepository $mailQueueRepository,
        CustomerRepository $customerRepository,
        RecCandidateRepository $recCandidateRepository,
        RecJobRepository $recJobRepository,
        RecCandidateTrackingRepository $recCandidateTrackingRepository,
        RecCandidateJobDetails $recCandidateJobDetails,
        RecCandidateTracking $recCandidateTracking
    ) {
        $this->helperService = $helperService;
        $this->userRepository = new UserRepository();
        $this->mailQueueRepository = $mailQueueRepository;
        $this->customerRepository=$customerRepository;
        $this->recCandidateRepository=$recCandidateRepository;
        $this->recJobRepository=$recJobRepository;
        $this->recCandidateTrackingRepository=$recCandidateTrackingRepository;
        $this->recCandidateJobDetails=$recCandidateJobDetails;
        $this->recCandidateTracking=$recCandidateTracking;
    }

    /**
     * Remove  Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function candidateSelection()
    {
        $notificationType=EmailNotificationType::where('type', 'select_interview_notification_mail')->first();
        $email_template=EmailTemplate::where('type_id', $notificationType->id)->first();
        $mail_content['body']= $email_template['email_body'];
        $candidate_selection_status_all=config('globals.rec_selection_status');
        $candidate_selection_status = array_diff($candidate_selection_status_all, array('Pending for Onboarding'));
        $hr_reps = $this->userRepository->getUserLookup(null, ['admin', 'super_admin'], null, null, ['assign_job_ticket']);
        $customers = $this->customerRepository->getList();
        $candidates = $this->recCandidateRepository->getCandidatesName();
        $job= $this->recJobRepository->getAll()->pluck('unique_key', 'id')->toArray();
        $users=User::get()->pluck('full_name', 'email')->toArray();
        return view('recruitment::candidate-selection', compact('candidate_selection_status', 'hr_reps', 'customers', 'candidates', 'job', 'mail_content', 'users'));
    }
    /**
     * Remove  Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function candidateSelectionList(Request $request)
    {
        $client_id= $request->client_id;
        $job_id= $request->job_id;
        $candidate_id= $request->candidate_id;
        $preference_id= $request->preference_id;
        $status= $request->status;
        $score_from= $request->score_from;
        $score_to= $request->score_to;
        $view_my_tickets=$request->viewmytickets?$request->viewmytickets:0;
        $query=RecCandidateJobDetails::whereHas('candidate')
        ->whereHas('job', function ($q) {
            $q->where('status', 'approved');
        })
        ->whereHas('candidate', function ($q) {
            $q->where('is_converted', 0);
        })
        ->with('recruiter', 'candidate.awareness', 'job.customer', 'candidate.guardingExperience.position', 'candidate.availability', 'candidate.wageExpectation', 'statusLog');
         $query->when(($view_my_tickets !== 0), function ($query) {
            $query->where('recruiter_id', \Auth::id());
         });
         $query->when(($client_id !== null), function ($query) use ($client_id) {
            $query->whereHas('job', function ($query) use ($client_id) {
                $query->where('customer_id', $client_id);
            });
         });
          $query->when(($job_id !== null), function ($query) use ($job_id) {
            $query->where('job_id', $job_id);
          });
         $query->when(($candidate_id !== null), function ($query) use ($candidate_id) {
            $query->where('candidate_id', $candidate_id);
         });
         $query->when(($preference_id !== null), function ($query) use ($preference_id) {
            $query->where('rec_preference', $preference_id);
         });
         $query->when(($status !== null), function ($query) use ($status) {
            $query->where('status', $status);
         });
         $query->when(($score_from !== null || $score_to !== null), function ($query) use ($score_from, $score_to) {
            if ($score_from==null) {
                $query->where('rec_match_score', '<=', $score_to);
            } elseif ($score_to==null) {
                $query->where('rec_match_score', '>=', $score_from);
            } else {
                $query->whereBetween('rec_match_score', [$score_from, $score_to]);
            }
         });
         $candidateSelections=$query->get();
        return datatables()->of($this->prepareArray($candidateSelections))->rawColumns(['status_html'])->addIndexColumn()->toJson();
    }

    public function prepareArray($data)
    {
        $arr=$datatable_rows=array();
        $match_score_color=config('globals.match_score_color');
        $approvedJobArr=[];
        $approvedJobArr=$this->recJobRepository->getAll()->where('status', 'approved')->pluck('id')->toArray();
        foreach ($data as $key => $each_data) {
             $val=0;
             $arr['id']=$each_data->id;
             $arr['job_id']=$each_data->job_id;
             $arr['unique_key']=$each_data->job->unique_key;
             $arr['candidate_id']=$each_data->candidate->id;
             $arr['candidate_name']=$each_data->candidate->name;
             $arr['rec_preference']=!empty($each_data->rec_preference)?$each_data->rec_preference:'--';
             $arr['project_number']=isset($each_data->job->customer)?$each_data->job->customer->project_number:'--';
             $arr['client_name']=isset($each_data->job->customer)?$each_data->job->customer->client_name:'--';
             //$arr['position']=isset($each_data->candidate->guardingExperience)?$each_data->candidate->guardingExperience->position->position:'--';
             $arr['position']=isset($each_data->job->positionBeeingHired)?$each_data->job->positionBeeingHired->position:'--';
            // $arr['wage_expectation']=isset($each_data->candidate->wageExpectation)?'$'.$each_data->candidate->wageExpectation->wage_expectations:'--';
             $arr['wage_expectation']=isset($each_data->job->wage)?'$'.$each_data->job->wage:'--';
             $arr['rec_match_score']=$each_data->rec_match_score;
             $arr['full_address']=$each_data->candidate->full_address;
             // $arr['days_required']=isset($each_data->candidate->availability)?str_replace(array('[',']','"'), '', $each_data->candidate->availability->days_required):'--';
             // $arr['shifts']=isset($each_data->candidate->availability)?str_replace(array('[',']','"'), '', $each_data->candidate->availability->shifts):'--';
             $arr['days_required']=isset($each_data->job->days_required)?str_replace(array('[',']','"'), '', $each_data->job->days_required):'--';
             $arr['shifts']=isset($each_data->job->shifts)?str_replace(array('[',']','"'), '', $each_data->job->shifts):'--';
             //$arr['prefered_hours_per_week']=isset($each_data->candidate->awareness)?$each_data->candidate->awareness->prefered_hours_per_week:'--';
             $arr['prefered_hours_per_week']=isset($each_data->job->hours_per_week)?$each_data->job->hours_per_week:'--';
             
             $filled_vacancy=$this->recCandidateJobDetails->where('job_id', $each_data->job_id)->where('status', 3)->whereIn('job_id', $approvedJobArr)->count();
             // $filled_vacancy=RecCandidateJobDetailsStatusLog::where('rec_job_details_id', $rem_vacancy['id'])->where('status', 3)->count();

             $arr['selected_for_interview_count']=$this->recCandidateJobDetails->where('candidate_id', $each_data->candidate->id)->whereIn('status', [1,4])->whereIn('job_id', $approvedJobArr)->count();
             $arr['is_interview_completed']=$this->recCandidateTracking->where('candidate_id', $each_data->candidate->id)->where('process_lookups_id', 11)->exists();
             $arr['filled_vacancy']=$filled_vacancy;
             $arr['total_vacancy']=$each_data->job->no_of_vaccancies;
             $arr['no_of_vaccancies']=isset($each_data->job)?($each_data->job->no_of_vaccancies-$filled_vacancy)<=0?0:($each_data->job->no_of_vaccancies-$filled_vacancy):'--';
            $arr['primary_recruiter']=isset($each_data->recruiter)?$each_data->recruiter->full_name:'--';
             $arr['recruiter']=isset($each_data->recruiter)?$each_data->recruiter->full_name:'--';
             $arr['status']=isset($each_data->status)?config('globals.rec_selection_status')[$each_data->status]:'--';
             $arr['status_id']=isset($each_data->status)?$each_data->status:'';
             $status_arr=array();
             $status_html='<table style="border:none">';
            foreach ($each_data->statusLog as $key => $eachstatusLog) {
                $status_html.='<tr>';
                $status= config('globals.rec_selection_status')[$eachstatusLog->status];
                $dt = new DateTime($eachstatusLog->datetime);
                $date = $dt->format('d-m-Y');
                $time = $dt->format('H:i:s');
                $datetime= $eachstatusLog->datetime;
                $recruiter= $eachstatusLog->recruiter->full_name;
                $status_arr[] = array("status" => $status, "datetime" => $datetime, "recruiter" => $recruiter);
                $status_html.="<td style='width:35%;border:none !important;'>".$status."</td><td style='width:35%;border:none !important;'>".$date. "<br>".$time."</td><td style='width:30%;border:none !important;'>".$recruiter."</td>";
                $status_html.='</tr>';
            }
            $status_html.='<table>';
            $arr['status_html'] =$status_html;
            $arr['status_log'] = $status_arr;
            $arr['color']='';
            $arr['fontcolor']='';
            for ($i=0; $i<count($match_score_color); $i++) {
                if ($i+1==count($match_score_color)) {
                    break;
                }
                if ($each_data->rec_match_score>=$match_score_color[$i]['upper_limit'] && $each_data->rec_match_score<=$match_score_color[$i+1]['upper_limit']) {
                       $arr['color']=$match_score_color[$i+1]['color'];
                       $arr['fontcolor']=$match_score_color[$i+1]['fontcolor'];
                       break;
                }
            }
        
             array_push($datatable_rows, $arr);
        }
        return $datatable_rows;
    }

    /**
     * Update Job Status
     *
     * @param Request $request
     * @return json
     */
    public function updateCandidateSelectionStatus(Request $request)
    {
        try {
            \DB::beginTransaction();
            $reccandidatejob=RecCandidateJobDetails::where('id', $request->id)->first();
            if ($request->status==3) {
                $is_interview_completed=RecCandidateTracking::where('candidate_id', $reccandidatejob->candidate_id)->where('process_lookups_id', 11)->exists();
                // $is_refereces_completed=RecCandidateTracking::where('candidate_id', $reccandidatejob->candidate_id)->where('process_lookups_id', 12)->exists();
                if (!$is_interview_completed) {
                    return response()->json(array('success' => 'interviewincomplete'));
                }
            }
            $recCandidateJobDetails['status'] = $request->get('status');
            if ($request->status==1 && empty($reccandidatejob->rec_preference)) {
                $recCandidateJobDetails['rec_preference'] = 0;
            } else {
                $recCandidateJobDetails['rec_preference']=!empty($reccandidatejob->rec_preference)?$reccandidatejob->rec_preference: null;
            }
            $recCandidateJobDetailsStatusLog['recruiter_id'] = \Auth::id();
            $recCandidateJobDetailsStatusLog['datetime']=\Carbon::now();
            $recCandidateJob=RecCandidateJobDetails::updateOrCreate(array('id' => $request->id), $recCandidateJobDetails);
            $recCandidateJobDetailsStatusLog['rec_job_details_id']=$recCandidateJob->id;
            if ($request->status==3) {//Begin Onboarding
                $filled_vacancy=RecCandidateJobDetails::where('job_id', $recCandidateJob->job_id)->where('status', 3)->whereHas('job', function ($q) {
                    $q->where('status', 'approved');
                })->count();
                $total_vacancy=RecJob::where('id', $recCandidateJob->job_id)->first();
                // $rem_vacancy=RecCandidateJobDetailsStatusLog::where('rec_job_details_id', $recCandidateJob->job_id)->where('status', 3)->count();
                if ($total_vacancy->no_of_vaccancies<$filled_vacancy) {
                     \DB::rollBack();
                    $recCandidateJobDetails['status'] = null;
                    $recCandidateJob=RecCandidateJobDetails::updateOrCreate(array('id' => $request->id), $recCandidateJobDetails);
                    return response()->json(array('success' => 'closedPositions'));
                }
                if (RecCandidateJobDetails::where('candidate_id', $recCandidateJob->candidate_id)->where('status', 3)->where('job_id', '!=', $recCandidateJob->job_id) ->whereHas('job', function ($q) {
                    $q->where('status', 'approved');
                })->count()>0) {
                    \DB::rollBack();
                    $recCandidateJobDetails['status'] = null;
                    $recCandidateJob=RecCandidateJobDetails::updateOrCreate(array('id' => $request->id), $recCandidateJobDetails);
                    return response()->json(array('success' => 'alreadyOnboarded'));
                }
                $jobdeatils=RecCandidateJobDetails::find($request->id);
                $this->recCandidateTrackingRepository->saveTracking($recCandidateJob->candidate_id, "onboarding_initiated", false, $jobdeatils->job_id);
                RecCandidateJobDetails::where('candidate_id', $recCandidateJob->candidate_id)->whereNotIn('id', [$recCandidateJob->id])->update(['status'=>4]);
            }
            if ($request->status==2 || $request->status==0) { //Reject for Role
                RecCandidateJobDetails::where('candidate_id', $recCandidateJob->candidate_id)->where('status', 4)->update(['status'=>null]);
                $this->recCandidateTrackingRepository->deleteTrackingWhenRoleRejected($recCandidateJob->candidate_id);
            }
            if ($request->status==1) {
                $jobdeatils=RecCandidateJobDetails::find($request->id);
                $this->recCandidateTrackingRepository->saveTracking($recCandidateJob->candidate_id, "hr_interview_scheduled", false, $jobdeatils->job_id);
            }
            RecCandidateJobDetailsStatusLog::create(array_merge($recCandidateJobDetailsStatusLog, $recCandidateJobDetails));
            $selection_type_arr=config('globals.selection_type_mail_arr');
            $jobDetails=RecCandidateJobDetails::with('candidate', 'recruiter', 'job')->find($recCandidateJob->id);
            if ($request->status==1) {
                $dynamicEmailBody = $request->emailScript;
            } else {
                $dynamicEmailBody = null;
            }

            \DB::commit();

            $helper_variable = array(
            '{receiverFullName}' => HelperService::sanitizeInput($jobDetails->candidate->name),
            '{recruiterFullName}' => HelperService::sanitizeInput($jobDetails->recruiter->full_name),
            '{jobCode}' => HelperService::sanitizeInput($jobDetails->job->unique_key),
            '{jobWage}' => HelperService::sanitizeInput($jobDetails->job->wage)
            );
            $emailResult = $this->mailQueueRepository
                   ->prepareMailTemplate(
                       $selection_type_arr[$jobDetails->status],
                       null,
                       $helper_variable,
                       "Modules\Recruitment\Models\RecCandidateJobDetails",
                       0,
                       0,
                       null,
                       null,
                       null,
                       null,
                       null,
                       null,
                       null,
                       $jobDetails->candidate_id,
                       $dynamicEmailBody
                   );
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    public function showCandidateMatchScore($candidate_id, $job_id)
    {
        $result=RecCandidateMatchScore::with('criteria')->where('candidate_id', $candidate_id)->where('job_id', $job_id)->get();
        return response()->json(['success'=>true,'data'=>$result]);
    }

    public function OnboardingDeadlineRemainder()
    {
        $onboarderdCandidates=RecCandidateJobDetails::where('status', 3)->pluck('candidate_id')->toArray();
        $mail_candidate_Arr=array();
        foreach ($onboarderdCandidates as $key => $candidate) {
            if (RecCandidateTracking::where('candidate_id', $candidate)->where('process_lookups_id', '=', 18)->count()==0) {
                $mail_candidate_Arr[]=$candidate;
            }
        }
        foreach ($mail_candidate_Arr as $key => $each_candidate) {
            $jobDetails=RecCandidateJobDetails::where('status', 3)->where('candidate_id', $each_candidate)->first();
            $job=RecJob::with('customer')->find($jobDetails->job_id);
            $current = date_create(\Carbon::now()->toDateString());
            $required_job_start_date =date_create($job->required_job_start_date);
            $interval=$job->customer->rec_onboarding_threshold_days;
            $diff = date_diff($required_job_start_date, $current);
            if (isset($interval)) {
                if ($diff->days<=$interval && $current <= $required_job_start_date) {
                    $helper_variable = array(
                    '{receiverFullName}' => HelperService::sanitizeInput($jobDetails->candidate->name),
                    '{jobCode}' => HelperService::sanitizeInput($jobDetails->job->unique_key),
                    '{dueInDays}' => $diff->days,
                    '{dueDate}' => date_format(date_create($job->required_job_start_date), "j F, Y"),
                    );
                    $emailResult = $this->mailQueueRepository
                       ->prepareMailTemplate(
                           'onboarding_deadline_remainder',
                           null,
                           $helper_variable,
                           "Modules\Recruitment\Models\RecCandidateJobDetails",
                           0,
                           0,
                           null,
                           null,
                           null,
                           null,
                           null,
                           null,
                           null,
                           $jobDetails->candidate_id
                       );
                }
            }
        }
    }
}
