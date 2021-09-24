<?php

namespace Modules\Recruitment\Repositories;

use Auth;
use Modules\Recruitment\Models\RecJob;
use Modules\Recruitment\Models\RecJobRequiredExperience;
use Modules\Admin\Models\PositionLookup;
use Modules\Recruitment\Models\RecCandidateAttachmentLookup;
use App\Services\HelperService;
use Carbon\Carbon;
use Mail;
use Modules\Recruitment\Models\RecJobDocumentAllocation;
use Modules\Recruitment\Models\RecJobTicketSetting;
use Modules\Recruitment\Repositories\RecMatchScoreCriteriaRepository;
use Modules\Recruitment\Models\RecJobProcess;
use Modules\Recruitment\Models\RecJobProcessLookup;
use App\Jobs\RecMatchScore;
use Modules\Recruitment\Models\RecDocumentAllocation;
use Modules\Recruitment\Mail\JobRequisition;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class RecJobRepository
{
   /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$recJobRequiredExperience;

    /**
     * Create a new RecBrandAwareness instance.
     *
     * @param  Modules\Recruitment\Models\RecCandidate $recCandidate;
     */
    public function __construct(RecJob $recJob, RecJobRequiredExperience $recJobRequiredExperience, RecCandidateAttachmentLookup $recCandidateAttachmentLookupModel, RecMatchScoreCriteriaRepository $recMatchScoreCriteriaRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository)
    {
        $this->model = $recJob;
        $this->recJobRequiredExperience=$recJobRequiredExperience;
        $this->recCandidateAttachmentLookupModel = $recCandidateAttachmentLookupModel;
        $this->recMatchScoreCriteriaRepository=$recMatchScoreCriteriaRepository;
        $this->helperService = new HelperService();
        $this->customerEmployeeAllocationRepository=$customerEmployeeAllocationRepository;
    }


    /**
     * Get Request Type lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->orderBy('unique_key', 'asc')->get();
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

    public function save($request)
    {
        try {
            \DB::beginTransaction();
            $request['user_id'] = auth()->id();
            $request['requisition_date'] = date('Y-m-d');
            $request['required_job_start_date'] = date('Y-m-d', strtotime($request->get('required_job_start_date')));
            $request['end'] = !empty($request->get('end')) ? date('Y-m-d', strtotime($request->get('end'))) : null;
            if ((int) $request->get('id') == 0) {
                $request['unique_key'] = $this->getUniqueJobKey($request);
            }
            $request['time'] = \Carbon::createFromFormat('h:i a', $request->get('time'))->format('H:i');
            $request['shifts'] = json_encode($request->get('shifts'));
            $request['days_required'] = json_encode($request->get('days_required'));
            $request['criterias'] = json_encode($request->get('criterias'));

            $job = $this->model->updateOrCreate(array('id' => $request->get('id')), $request->all());
            foreach ($request->get('experiences') as $each_experience) {
                if ($each_experience['experience_id'] != null) {
                    $data[] = array('job_id' => $job->id, 'experience_id' => $each_experience['experience_id'], 'year' => $each_experience['year']);
                }
            }
            $this->recJobRequiredExperience->where('job_id', $job->id)->delete();
            if (isset($data)) {
                $this->recJobRequiredExperience->insert($data); // Eloquent approach
            }

            $jobDocumentAllocationCollection = array();
            if (isset($request->document_allocation_with_trashed)) {
                $jobDocumentAllocationCollection = collect($request->document_allocation_with_trashed);
                $documentIds = $jobDocumentAllocationCollection->pluck('id')->all();

              //  if (in_array(null, $documentIds, true)) {
              //      RecJobDocumentAllocation::where('job_id', $job->id)->delete();
              //  }

                foreach ($request->document_allocation_with_trashed as $key => $value) {
                    foreach ($value['display'] as $kdisplay => $vdisplay) {
                        foreach ($value['mandatory'] as $kmandatory => $vmandatory) {
                            $recJobDocumentAllocation = [
                                'job_id' => $job->id,
                                'process_tab_id' => $value['process_tab_id'],
                                'document_id' => $kdisplay,
                                'display' => intval($vdisplay),
                                'is_mandatory' => intval($vmandatory)
                            ];

                            RecJobDocumentAllocation::updateOrCreate(array('id' => $value['id']), $recJobDocumentAllocation);
                        }
                    }
                }
            }
            // if (isset($request->job_document_allocation)) {
            //     $jobDocumentAllocationCollection = collect($request->job_document_allocation);
            //     $documentIds = $jobDocumentAllocationCollection->pluck('id')->all();

            //     if (in_array(null, $documentIds, true)) {
            //         RecJobDocumentAllocation::where('job_id', $job->id)->delete();
            //     }

            //     foreach ($request->job_document_allocation as $key => $value) {
            //         foreach ($value['display'] as $kdisplay => $vdisplay) {
            //             foreach ($value['mandatory'] as $kmandatory => $vmandatory) {
            //                 $recJobDocumentAllocation = [
            //                     'job_id' => $job->id,
            //                     'process_tab_id' => $value['process_tab_id'],
            //                     'document_id' => $kdisplay,
            //                     'display' => intval($vdisplay),
            //                     'is_mandatory' => intval($vmandatory)
            //                 ];

            //                 RecJobDocumentAllocation::updateOrCreate(array('id' => $value['id']), $recJobDocumentAllocation);
            //             }
            //         }
            //     }
            // }
            \DB::commit();
            if (null!==$request->get('id') && $job->status=='approved') {
                RecMatchScore::dispatch($job);
                //$score=$this->recMatchScoreCriteriaRepository->setScore($job);
            }
            //TO DO - MAIL CONFIGURE//
            $coos_mail_ids = User::role('coo')->pluck('email')->toArray();
            $this->sendNotification($job, $coos_mail_ids, 'mail.job.created');
            $result = ($job->wasRecentlyCreated) ? 'The Job Requisition has been successfully created' : 'The Job Requisition has been successfully updated';

            return response()->json(array('success' => true, 'result' => $result));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }


    /**
     * To send mail notification
     *
     * @param [type] $job
     * @param [type] $to
     * @param [type] $cc
     * @param [type] $template
     * @return void
     */
    public function sendNotification($job, $to, $template, $cc = null, $subject = null)
    {

        $mail = Mail::to($to);
        if ($cc != null) {
            $mail->cc($cc);
        }
        $mail->queue(new JobRequisition($job, $template, $subject));
    }

    /**
     *
     * @param type $filter
     * @return type
     */
    public function getJobs($job_status = null, $filter = false, $customer_session = false,$request=null)
    {
        $client_id = $request == null ? null : $request->get('client_id');
        $job_query = $this->prepareJobsRecords($job_status, $filter, false, 'created_at', 'desc', $customer_session,$client_id);

           //Filter by url attributes
        if ($request != null) {
            $from = $request->input('from');
            $to = $request->input('to');
            $cIds = $request->input('cIds');

            if (!empty($from) && !empty($to)) {
                $job_query = $job_query->whereDate('required_job_start_date', '<=', Carbon::parse($to));
                $job_query = $job_query->whereDate('required_job_start_date', '>=', Carbon::parse($from));
            }

            if(!empty($cIds)){
                $job_query->whereIn('customer_id',$cIds);
            }
        }
        return $job_query->get();
    }
     /**
     * Delete a tracking step
     *
     * @param [type] $job_id
     * @param [type] $step_id
     * @return void
     */
    public function deleteHrTracking($job_id, $step_id)
    {
        try {
            \DB::beginTransaction();
            RecJobProcess::where('process_id', '=', $step_id)
                    ->where('job_id', '=', $job_id)
                    ->delete();
            $this->model->where('id', '=', $job_id)->update(['status' => 'approved']);
            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    /**
     * Prepare jobs records
     *
     * @param [type] $job_status
     * @param boolean $filter
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function prepareJobsRecords($job_status = null, $filter = null, $exclude_archived = false, $order_by = null, $order_by_dir = 'asc', $customer_session = false, $customerId = null)
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


        $job_query->when($customerId!=null, function ($q) use ($customerId) {
            return $q->where('customer_id', $customerId);
        });


        /** END ** Get Customer Ids from Session and Filter */
        $job_query->when((!$user->hasAnyPermission(['rec-list-jobs-from-all', 'rec-view_all_candidates_candidate_geomapping', 'admin', 'super_admin','rec-view-allocated-job-requisitions'])), function ($job_query) use ($user) {

            $job_query->where(function ($job_query) use ($user) {
                $job_query->where('hr_rep_id', '=', $user->id)
                    ->orWhere('user_id', '=', $user->id);
            });
        });
      
         $job_query->when(($user->hasPermissionTo('rec-view-allocated-job-requisitions')), function ($job_query) use ($user) {
            $customer_list=$this->customerEmployeeAllocationRepository->getDirectAllocatedCustomersList($user);
             $job_query->where(function ($job_query) use ($customer_list) {
                $job_query->whereIn('customer_id', array_keys($customer_list));
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
     * Job unique key
     *
     * @param [type] $request
     * @return void
     */
    public function getUniqueJobKey($request)
    {
        $rand_no = str_pad((($this->model->where('customer_id', $request->get('customer_id'))->withTrashed()->count()) + 1), 3, "0", STR_PAD_LEFT);
        $client_name = substr($this->removeSpace($request->get('client_name')), 0, 5);
        $position = substr($this->removeSpace(PositionLookup::find($request->get('open_position_id'))->position), 0, 3);
        $city = substr($this->removeSpace($request->get('city')), 0, 3);
        $unique_key = strtoupper($client_name . $position . $city . $rand_no);
        if ($this->model->where('unique_key', '=', $unique_key)->count() > 0) {
            $unique_key = strtoupper($client_name . $position . $city . time());
        }
        return $unique_key;
    }

    /**
     * Remove spaces,Special Characters and multiple hyphens
     *
     * @param  $string
     * @return $string
     */
    public function removeSpace($string)
    {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    /**
     * Update job status
     *
     * @param [type] $job_id
     * @param Request $request
     * @return void
     */
    public function updateJobStatus($job_ids, $status, $hr_user_id = null, $status_reason = null)
    {
        try {
            \DB::beginTransaction();
            if (!empty($job_ids) && is_array($job_ids)) {
                foreach ($job_ids as $id) {
                    $job = $this->model->find($id);
                    switch ($status) {
                        case 'approved':
                            $job->status = 'approved';
                            $job->hr_rep_id = $hr_user_id;
                            $job->status_reason = '';
                            break;
                        case 'rejected':
                            $job->status = 'rejected';
                            $job->status_reason = $status_reason;
                            break;
                        case 'suspended':
                            $job->status = 'suspended';
                            $job->status_reason = $status_reason;
                            break;
                        case 'archive':
                            $job->active = !$job->active;
                            break;
                    }
                    $job->approved_by = \auth()->id();
                    $job->approved_at = \DB::raw('now()');
                    $job->save();
                }
            }

            \DB::commit();
            if ($status=='approved') {
                RecMatchScore::dispatch($job);
                //$score=$this->recMatchScoreCriteriaRepository->setScore($job);
            }
            //MAIL TO DO
            $mail_subject = "Your Job ticket for " . $job->customer->client_name . " has been " . $job->status . " by " . $job->approver->first_name . ($job->approver->last_name != '' ? " " . ucfirst($job->approver->last_name) . "." : ".");
            $job=$this->model->find($job->id);
            if ($status == 'approved' && (int) $job->hr_rep_id) {
                $to = User::find($job->hr_rep_id)->email;
                $coo = User::find($job->approved_by);
                $requester = User::find($job->user_id);
                $this->sendNotification($job, $to, 'mail.job.assigned', [$job->am_email, $coo->email, $requester->email], $mail_subject);
            }
            if ($status == 'rejected' || $status == 'suspended') {
                $requester = User::find($job->user_id)->email;
                $job->load('approver', 'customer');
                $this->sendNotification($job, $requester, 'mail.job.status-changed', null, $mail_subject);
            }
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     *
     * @param type $filter
     * @return type
     */
    public function getJobsForMapping($filter)
    {
        $job_query = $this->prepareJobsRecords(['pending', 'approved', 'completed'], $filter, true, 'unique_key');
        return ($job_query->get());
    }

    /**
     * This will save the mandatory attachements for a created job
     *
     * @param [type] $job_id
     * @param [type] $request
     * @return void
     */
    public function saveMandatoryAttachements($job_id, $request)
    {
        try {
            \DB::beginTransaction();
            $this->model = $this->model->find($job_id);
            /* default lookups */
            $mandatory_attachements = $request->get('mandatory_attachements');
            /* default lookups */
            $attachment_custom_labels = array_filter($request->get('attachment_custom_label'));
            $mandatory_attachements_custom = ($request->get('mandatory_attachements_new'));
            $arr_mandatory_attachment_id = $custom_lookup_ids = array();
            foreach ($attachment_custom_labels as $key => $attachment_name) {
                $id = (isset($mandatory_attachements_custom[$key]) && is_numeric($mandatory_attachements_custom[$key]) && $mandatory_attachements_custom[$key] > 0) ? $mandatory_attachements_custom[$key] : null;
                $candidateAttachmentLookup = $this->recCandidateAttachmentLookupModel->updateOrCreate(
                    ['attachment_name' => $attachment_name, 'job_id' => $job_id],
                    ['id' => $id]
                );
                if (isset($mandatory_attachements_custom[$key]) && ($mandatory_attachements_custom[$key] > 0 || $mandatory_attachements_custom[$key] == 'on')) {
                    $mandatory_attachements[] = $candidateAttachmentLookup->id;
                }
                $custom_lookup_ids[] = $candidateAttachmentLookup->id;
            }
            $this->recCandidateAttachmentLookupModel->where(['job_id' => $job_id])->whereNotIn('id', $custom_lookup_ids)->delete();
            $this->model->required_attachments = json_encode($mandatory_attachements);
            $this->model->save();
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
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

     /**
     * To get Difference between two days and color code
     * @param $dateRequired $dateEntered
     * @return $array
     */
    public function NoticePeriod($required, $entered)
    {
        $dateDiff = Carbon::parse($required)->diffInDays($entered->toDateString());

        $jobTicketSettingsQuery = RecJobTicketSetting::find([1,2])->pluck('value', 'setting')->toArray();
        $min = $jobTicketSettingsQuery['minNoticePeriodDays'];
        $max = $jobTicketSettingsQuery['maxNoticePeriodDays'];


        if ($dateDiff <= $min) {
            $colorCode = 'red';
        } elseif ($dateDiff <= $max) {
            $colorCode = 'yellow';
        } else {
            $colorCode = 'green';
        }

        return ['dateDiff' => $dateDiff, 'colorCode' => $colorCode ];
    }

    /**
     * Update HR Tracking step
     *
     * @param [type] $job_id
     * @param [type] $request
     * @return void
     */
    public function saveHrTrackingStep($job_id, $request)
    {
        try {
            \DB::beginTransaction();
            if ($request->get('completion_date') != null) {
                $process_dates = $request->get('completion_date');
                $process_notes = $request->get('notes');
                $entered_by_ids = $request->get('entered_by_id');
                $job = $this->model->find($job_id)->load('approver', 'assignee');
                foreach ($process_dates as $process_id => $process_date) {
                    if (isset($process_date) || isset($entered_by_ids[$process_id]) || isset($process_notes[$process_id])) {
                        if (!isset($process_date)) {
                            return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["completion_date." . $process_id => ["Please select the date"]]], 422);
                        }
                        if (!isset($entered_by_ids[$process_id])) {
                            return response()->json(['success' => false, "message" => "The given data was invalid.", "errors" => ["entered_by_id." . $process_id => ["Please select the person"]]], 422);
                        }
                        $data['job_id'] = $job_id;
                        $data['process_id'] = $process_id;
                        $data['user_id'] = auth()->id();
                        $data['process_date'] = $process_dates[$process_id];
                        $data['process_note'] = isset($process_notes[$process_id]) ? $process_notes[$process_id] : '--';
                        $data['entered_by_id'] = $entered_by_ids[$process_id];
                        RecJobProcess::updateOrCreate(array('job_id' => $job_id, 'process_id' => $process_id), $data);
                    }
                }
                if (RecJobProcess::where('job_id', '=', $job_id)->count() == RecJobProcessLookup::count()) {
                    $this->model->where('id', '=', $job_id)->update(['status' => 'completed']);
                }
                \DB::commit();
                try {
                    // /* mail to AM,COO */
                    $to = $ccs = [];
                    $arr_email_excludes = null != config("globals.email_excludes") ? config("globals.email_excludes") : [];
                    if (!in_array($job->approver->email, $arr_email_excludes)) {
                        $to = $job->approver->email;
                    }
                    if (!in_array($job->am_email, $arr_email_excludes)) {
                        $ccs[] = $job->am_email;
                    }
                    if (null != $job->assignee) {
                        if (!in_array($job->assignee->email, $arr_email_excludes)) {
                            $ccs[] = $job->assignee->email;
                        }
                    }

                    $this->sendNotification($job, $to, 'mail.job.jobprocess', $ccs);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()]);
                }
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true]);
    }

    public function getJobDocumentAllocation($customerId, $jobId)
    {
        $documentQuery = RecDocumentAllocation::where('customer_id', $customerId);
        if (empty($documentQuery->get()->toArray())) {
            $documentQuery = RecDocumentAllocation::where('customer_id', 0);
        }
        $doc = $documentQuery->with(['processTab', 'jobDocumentAllocation' => function ($q) use ($jobId) {
    // Query the name field in status table
            $q->where('job_id', '=', $jobId); // '=' is optional
        }])->get()->toArray();

        return $doc;
    }

    public function getDocumentJobsAllocation($customerId, $jobId)
    {
        $documentQuery = RecJobDocumentAllocation::where('job_id', $jobId);
      //  if (empty($documentQuery->get()->toArray())) {
      //      $documentQuery = RecDocumentAllocation::where('customer_id', 0);
      //  }
        $doc = $documentQuery->with(['processTab','documentAllocationWithTrashed'])->get()->toArray();
        return $doc;
    }

    public function getJobsBasedOnCustomer($customer_id = null)
    {
        return $this->model->when($customer_id, function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })->get()->pluck('unique_key', 'id')->toArray();
    }
}
