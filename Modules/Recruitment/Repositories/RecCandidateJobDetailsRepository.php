<?php

namespace Modules\Recruitment\Repositories;

use Auth;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use App\Services\HelperService;
use Carbon;

class RecCandidateJobDetailsRepository
{
   /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new RecBrandAwareness instance.
     *
     * @param  Modules\Recruitment\Models\RecCandidate $recCandidate;
     */
    public function __construct(RecCandidateJobDetails $recCandidateJobDetails)
    {
        $this->model = $recCandidateJobDetails;
        $this->helperService = new HelperService();
    }


    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }


    /**
     *
     * @param type $filter
     * @return type
     */
    public function getJobs($job_status = null, $filter = false, $customer_session = false)
    {
        $job_query = $this->prepareJobsRecords($job_status, $filter, false, 'created_at', 'desc', $customer_session);
        return ($job_query->get());
    }

    /**
     * Prepare jobs records
     *
     * @param [type] $job_status
     * @param boolean $filter
     * @return void
     */
    public function prepareJobsRecords($job_status = null, $filter = null, $exclude_archived = false, $order_by = null, $order_by_dir = 'asc', $customer_session = false)
    {
        $user = \Auth::user();

        $job_query = $this->model->with([
        'positionBeeingHired',
        'assignmentType',
        'customer',
        'training',
        'training_timing',
        'experiences',
        'reason',
        'assignee',
        'trackingstep',
        'trackingstep.process',
        ]);

        $job_query->when($exclude_archived || !$user->can('rec-archive-job'), function ($job_query) {
            $job_query->whereActive(true);
        });
        $job_query->when($job_status != null, function ($job_query) use ($job_status) {
            if (is_array($job_status)) {
                $job_query->whereIn('status', $job_status);
            } else {
                $job_query->where('status', '=', $job_status);
            }
        });

        $job_query->when($filter != null, function ($job_query) use ($filter) {
            if (!empty($area_manager = $filter->get('area_manager'))) {
                $job_query->where('area_manager', '=', $area_manager);
            }
            if (!empty($from = $filter->get('from'))) {
                $job_query->whereDate('created_at', '>=', $from);
            }
            if (!empty($to = $filter->get('to'))) {
                $job_query->whereDate('created_at', '<=', $to);
            }
            if (!empty($area_manager = $filter->get('requester'))) {
                $job_query->where('requester', '=', $area_manager);
            }
            if (!empty($type = $filter->get('type'))) {
                $job_query->where('reason_id', '=', $type);
            }
            if (!empty($job_status = $filter->get('job_status'))) {
                $job_query->where('status', '=', $job_status);
            }
        });

        /** START ** Get Customer Ids from Session and Filter */
        $customer_ids = [];
        if ($customer_session) {
            $customer_ids = $this->helperService->getCustomerIds();
            if (!empty($customer_ids)) {
                $job_query->whereIn('customer_id', $customer_ids);
            }
        }

        /** END ** Get Customer Ids from Session and Filter */
        $job_query->when((!$user->hasAnyPermission(['rec-list-jobs-from-all', 'rec-view_all_candidates_candidate_geomapping', 'admin', 'super_admin'])), function ($job_query) use ($user) {
            $job_query->where(function ($job_query) use ($user) {
                $job_query->where('hr_rep_id', '=', $user->id)
                    ->orWhere('user_id', '=', $user->id);
            });
        });

        if ($order_by != null) {
            $job_query->orderBy($order_by, $order_by_dir);
        } else {
            $job_query->orderBy('id', 'desc');
        }

        return $job_query;
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }





    /**
     * Return datatable values as array
     *
     * @param empty
     */
    public function prepareDataForJobTrackingSummary($job_list, $wigetRequest = false)
    {

        $datatable_rows = array();
        $i = 0;
        foreach ($job_list as $key => $each_job) {
            if ($each_job->active && in_array($each_job->status, ["approved", "completed"]) /* && $each_job->trackingstep != null */) {
                $each_row["id"] = $each_job->id;
                $each_row["active"] = $each_job->active;
                $each_row["status"] = $each_job->status;
                $each_row["user_id"] = $each_job->user_id;
                $each_row["unique_key"] = $each_job->unique_key;
                $each_row["requester"] = $each_job->requester;
                $each_row["client_name"] = $each_job->customer->client_name;
                $each_row["no_of_vaccancies"] = $each_job->no_of_vaccancies;
                $each_row["date_entered"] = $each_job->created_at->format('Y-m-d');
                $each_row["requisition_date"] = $each_job->required_job_start_date;

                // get difference between required date and entered date. Also color code.

                $notice = $this->NoticePeriod(
                    $required = $each_job->required_job_start_date,
                    $entered = $each_job->created_at
                );


                $each_row["notice"] = $notice['dateDiff'];
                $each_row["colorCode"] = $notice['colorCode'];

                $each_row["wage"] = $each_job->wage;
                $each_row["assignment_type"] = $each_job->assignmentType->type;
                $each_row["assignee"] = (null != $each_job->assignee) ? $each_job->assignee->full_name : '--';
                $each_row["customer"] = $each_job->customer->project_number;
                $each_row["position"] = $each_job->positionBeeingHired ? $each_job->positionBeeingHired->position : '';
                $each_row["updated_at"] = null != $each_job->trackingstep ? $each_job->trackingstep->updated_at->format('Y-m-d') : '--';
                $each_row["process_name"] = null != $each_job->trackingstep ? $each_job->trackingstep->process->process_name : 'Job tracking not yet started';
                $each_row["process_id"] = null != $each_job->trackingstep ? $each_job->trackingstep->process_id : 0;
                array_push($datatable_rows, $each_row);
                $i++;
            }
            if ($wigetRequest && ($i == config('dashboard.job_ticket_status_row_limit'))) {
                break;
            }
        }

        return $datatable_rows;
    }

    public function getJobListForCandidate($candidate_id)
    {
        $jobList = RecCandidateJobDetails::with('job.customer', 'job.positionBeeingHired')
        ->whereHas('job', function ($q) {
                    $q->where('status', 'approved');
        })
        ->where('candidate_id', $candidate_id)
        ->whereNull('rec_preference')
        ->orderBy('rec_match_score', 'desc')
        ->get()->toArray();
        return $this->prepareJobListForCandidate($jobList);
    }

    public function prepareJobListForCandidate($data, $show_all_flag = 0)
    {
        $arr=$jobListArray=array();
        foreach ($data as $key => $value) {
            $filled_vacancy=RecCandidateJobDetails::where('job_id', $value['job_id'])->where('status', 3)->count();
            $total_number_of_vacancies=$value['job']['no_of_vaccancies'];
            $rem_vaccancies=($total_number_of_vacancies-$filled_vacancy)<=0?0:($total_number_of_vacancies-$filled_vacancy);
            if ($rem_vaccancies!=0 || $show_all_flag=1) {
                $arr['rem_vaccancies']=$rem_vaccancies;
                $arr['id'] = isset($value['id'])
                ? $value['id']
                : '--';

                $arr['preference'] = (isset($value['rec_preference'])) && ($value['rec_preference'] != 0)
                ? $value['rec_preference']
                : '';

                $arr['jobid'] = isset($value['job_id'])
                ? $value['job_id']
                : '--';

                $arr['customer'] = isset($value['job']['customer']['client_name'])
                ? $value['job']['customer']['client_name']
                : '--';

                $arr['position'] = isset($value['job']['position_beeing_hired']['position'])
                ? $value['job']['position_beeing_hired']['position']
                : '--';

                $arr['wage'] = isset($value['job']['wage'])
                ? $value['job']['wage']
                : '--';

                $arr['city'] = isset($value['job']['customer']['city'])
                ? $value['job']['customer']['city']
                : '--';

                $arr['schedule'] = isset($value['job']['days_required'])
                ? str_replace(',', '<br />', str_replace(array('[',']','"'), '', $value['job']['days_required']))
                :'--';

                $arr['shift'] = isset($value['job']['shifts'])
                ? str_replace(',', '<br />', str_replace(array('[',']','"'), '', $value['job']['shifts']))
                : '--';

                $arr['hours_per_week'] = isset($value['job']['hours_per_week'])
                ? $value['job']['hours_per_week']
                : '--';

                $arr['estimated_distance'] = isset($value['estimate_distance'])
                ?number_format((float)$value['estimate_distance'], 2, '.', '')
                : '--';

                $arr['estimated_travel_time'] = isset($value['estimated_travel_time'])
                ? intval(round($value['estimated_travel_time']))
                : '--';

                $arr['rec_match_score'] = isset($value['rec_match_score'])
                ? $value['rec_match_score']
                : '--';
                if (isset($value['status'])) {
                    if ($value['status']==4) {
                        $arr['status'] = '--';
                    } elseif ($value['status']==0) {
                        $arr['status'] = 'Rejected';
                    } else {
                        $arr['status'] =config('globals.rec_selection_status')[$value['status']];
                    }
                } else {
                     $arr['status'] = '--';
                }

                $arr['description'] = isset($value['job']['job_description'])
                ? $value['job']['job_description']
                : '--';

                $arr['total_number_of_vacancies'] = isset($value['job']['no_of_vaccancies'])
                ? $value['job']['no_of_vaccancies']
                : '--';

                $arr['area_manager'] = isset($value['job']['area_manager'])
                ? $value['job']['area_manager']
                : '--';

                $arr['area_manager_email'] = isset($value['job']['am_email'])
                ? $value['job']['am_email']
                : '--';

                $arr['requisition_date'] = isset($value['job']['requisition_date'])
                ? $value['job']['requisition_date']
                : '--';

                $arr['requester'] = isset($value['job']['requester'])
                ? $value['job']['requester']
                : '--';

                $arr['requester_email'] = isset($value['job']['email'])
                ? $value['job']['email']
                : '--';

                $arr['requester_phone'] = isset($value['job']['phone'])
                ? $value['job']['phone']
                : '--';

                $arr['requester_employee_num'] = isset($value['job']['employee_num'])
                ? $value['job']['employee_num']
                : '--';

                $arr['unique_key'] = isset($value['job']['unique_key'])
                ? $value['job']['unique_key']
                : '--';

                array_push($jobListArray, $arr);
            }
        }
        
        return $jobListArray;
    }

    public function getJobAppliedListCandidate($candidate_id)
    {
        $jobAppliedList = RecCandidateJobDetails::with('job.customer', 'job.positionBeeingHired')
         ->whereHas('job', function ($q) {
                    $q->where('status', 'approved');
         })
        ->where('candidate_id', $candidate_id)
        ->whereNotNull('rec_preference')
        ->orderBy('rec_preference')
        ->get()->toArray();
    
        return $this->prepareJobListForCandidate($jobAppliedList);
    }
}
