<?php

namespace Modules\Recruitment\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Recruitment\Repositories\RecExperienceLookupRepository;
use Modules\Recruitment\Repositories\RecCriteriaLookupRepository;
use Modules\Recruitment\Repositories\RecJobRepository;
use Modules\Recruitment\Models\RecJobRequisitionReasonLookup;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Recruitment\Repositories\RecCandidateAssignmentTypeLookupRepository;
use Modules\Recruitment\Repositories\RecTrainingLookupRepository;
use Modules\Recruitment\Repositories\RecTrainingTimingLookupRepository;
use Modules\Hranalytics\Repositories\EmployeeExitInterviewRepository;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\Recruitment\Http\Requests\RecJobRequest;
use Modules\Admin\Repositories\UserRepository;
use Modules\Recruitment\Models\RecCandidateAttachmentLookup;
use Modules\Recruitment\Repositories\RecJobProcessLookupRepository;
use Modules\Recruitment\Http\Requests\RecHrTrackingRequest;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class RecJobController extends Controller
{

    protected $jobRepository,
        $recExperienceLookupRepository,
        $recCriteriaLookupRepository,
        $recJobRepository,
        $trainingLookupRepository,
        $recTrainingLookupRepository,
        $recTrainingTimingLookupRepository,
        $userRepository,
        $positionLookupRepository,
        $recJobProcessLookupRepository;

    public function __construct(
        RecExperienceLookupRepository $recExperienceLookupRepository,
        RecCriteriaLookupRepository $recCriteriaLookupRepository,
        RecJobRepository $recJobRepository,
        CustomerRepository $customerRepository,
        RecCandidateAssignmentTypeLookupRepository $recCandidateAssignmentTypeLookupRepository,
        RecTrainingLookupRepository $recTrainingLookupRepository,
        RecTrainingTimingLookupRepository $recTrainingTimingLookupRepository,
        EmployeeExitInterviewRepository $employeeExitInterviewRepository,
        PositionLookupRepository $positionLookupRepository,
        UserRepository $userRepository,
        RecJobProcessLookupRepository $recJobProcessLookupRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {

        $this->recExperienceLookupRepository = $recExperienceLookupRepository;
        $this->recCriteriaLookupRepository = $recCriteriaLookupRepository;
        $this->recJobRepository = $recJobRepository;
        $this->customerRepository = $customerRepository;
        $this->recCandidateAssignmentTypeLookupRepository = $recCandidateAssignmentTypeLookupRepository;
        $this->recTrainingLookupRepository = $recTrainingLookupRepository;
        $this->recTrainingTimingLookupRepository = $recTrainingTimingLookupRepository;
        $this->employeeExitInterviewRepository = $employeeExitInterviewRepository;
        $this->positionLookupRepository = $positionLookupRepository;
        $this->userRepository = $userRepository;
        $this->recJobProcessLookupRepository = $recJobProcessLookupRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$hr_reps = User::permission('hr-tracking')->get(); // Returns only users with the permission 'edit articles'
        // dd($this->jobRepository->getJobs()->toJson());
        $job_onboarded = RecCandidateJobDetails::whereIn('status', [1, 3])->whereHas('candidate')->with('candidate')->get();
        $jobs_onboard = array();
        foreach ($job_onboarded as $key => $job) {
            $jobs_onboard[$job->job_id][] = $job;
        }
        $hr_reps = $this->userRepository->getUserLookup(null, ['admin', 'super_admin'], null, null, ['assign_job_ticket']);
        $user = \Auth::user();
        if ($user->hasAnyPermission(['rec-list-jobs-from-all','admin', 'super_admin'])) {
            $customerList = $this->customerRepository->getProjectsDropdownList('all');
        }else if ($user->hasAnyPermission(['rec-view-allocated-job-requisitions'])) {
            $customerList = $this->customerRepository->getProjectsDropdownList('allocated');
        }else{
            $customerList = [];
        }
        return view('recruitment::job.index', compact('hr_reps', 'jobs_onboard', 'customerList'));
    }

    /**
     * Get list of jobs
     *
     * @return datatable object
     */

    public function getList($job_status = null, Request $request)
    {
        return datatables()->of($this->recJobRepository->getJobs($job_status, false, false, $request))->toJson();
    }

    /**
     * To create a new job requisition
     *
     * @return void
     */
    public function create()
    {
        $lookups = $this->getLookups();
        $job = null;
        $criterias = null;
        return view('recruitment::job.create', compact('job', 'lookups', 'criterias'));
    }

    /**
     * Get all lookup values
     *
     * @return lookups
     */
    public function getLookups()
    {
        $default = [null => 'Please Select'];
        $lookups['job_requisition_reason_lookups'][] = $default + RecJobRequisitionReasonLookup::where('parent_id', '0')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + RecJobRequisitionReasonLookup::where('parent_id', '1')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + RecJobRequisitionReasonLookup::where('parent_id', '2')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + RecJobRequisitionReasonLookup::where('parent_id', '11')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + RecJobRequisitionReasonLookup::where('parent_id', '12')->pluck('reason', 'id')->toArray();
        $lookups['positions_lookups'] = $default + $this->positionLookupRepository->getList();
        $lookups['assignment_type_lookups'] = $default + $this->recCandidateAssignmentTypeLookupRepository->getList();
        $lookups['training_lookups'] = $default + $this->recTrainingLookupRepository->getList();
        $lookups['training_timing_lookups'] = $default + $this->recTrainingTimingLookupRepository->getList();
        $lookups['criteria_lookups'] = $default + $this->recCriteriaLookupRepository->getList();
        $lookups['experience_lookups'] =  $default + $this->recExperienceLookupRepository->getList();
        $lookups['customers'] = $default + $this->customerRepository->getList();
        $lookups['resignation_list'] = $default + $this->employeeExitInterviewRepository->getResignationList();
        $lookups['termination_list'] = $default + $this->employeeExitInterviewRepository->getTerminationList();
        return $lookups;
    }
    /**
     * To store a newly created job
     *
     * @param JobRequest $request
     * @return void
     */
    public function store(RecJobRequest $request)
    {
        return $this->recJobRepository->save($request);
    }
    /**
     * To edit a job requisition
     *
     * @return void
     */
    public function edit($id)
    {
        $lookups = $this->getLookups();
        $job = $this->recJobRepository->get($id);
        $shifts = json_decode(html_entity_decode($job->shifts), true);
        $days_required = json_decode(html_entity_decode($job->days_required), true);
        $criterias = json_decode(html_entity_decode($job->criterias), true);
        $jobDocument = $this->recJobRepository->getDocumentJobsAllocation($job->customer_id, $id);
        $sortCollection = collect($jobDocument);
        $grouped = $sortCollection->groupBy('process_tab.display_name');
        $jobDocumentAllocation = $grouped->toArray();
        // dd($jobDocumentAllocation);
        return view('recruitment::job.edit', compact('job', 'lookups', 'shifts', 'days_required', 'criterias', 'jobDocumentAllocation'));
    }



    /**
     * Change Status of Job
     *
     * @param Request $request
     * @return json
     */
    public function changeStatus($job_id, Request $request)
    {
        return $this->recJobRepository->updateJobStatus([$job_id], $request->get('status'), $request->get('hr_rep_id'), $request->get('status_reason'));
    }

    /**
     * View job description
     *
     * @param [type] $id
     * @return void
     */
    public function viewJob($id)
    {
        $lookups = $this->getLookups();
        $job = $this->recJobRepository->get($id);
        $candidateAttachment_default_Lookups = RecCandidateAttachmentLookup::where('job_id', null)
            ->orderby('id', 'asc')
            ->pluck('attachment_name', 'id')
            ->toArray();
        $candidateAttachment_custom_Lookups = RecCandidateAttachmentLookup::where('job_id', $id)
            ->orderby('id', 'asc')
            ->pluck('attachment_name', 'id')
            ->toArray();
        $custom_lookup_count = count($candidateAttachment_custom_Lookups);
        $mandatory_attachment_ids = json_decode($job->required_attachments);

        return view('recruitment::job.view', compact('job', 'candidateAttachment_default_Lookups', 'candidateAttachment_custom_Lookups', 'mandatory_attachment_ids', 'custom_lookup_count', 'lookups'));
    }


    /**
     * Set mandatory file uploads for the job
     *
     * @param [type] $job_id
     * @param Request $request
     * @return void
     */
    public function setMandatoryAttachements($job_id, Request $request)
    {
        return $this->recJobRepository->saveMandatoryAttachements($job_id, $request);
    }

    /**
     * Plot all jobs in google map
     *
     * @return void
     */
    public function jobMapping(Request $request)
    {
        $jobs = $this->recJobRepository->getJobsForMapping($request);
        return view('recruitment::job.jobs-in-map', compact('jobs', 'request'));
    }


    /**
     *Load Status Summary of Jobs Page
     *
     * @return view
     */
    public function hrTrackingSummary()
    {
        $customerList = $this->customerRepository->getProjectsDropdownList('all');
        return view('recruitment::job.hr-tracking-summary', compact('customerList'));
    }

    /**
     * HR tracking update
     *
     * @param Request $request
     * @return void
     */
    public function hrTrackingStore($job_id, RecHrTrackingRequest $request)
    {
        return $this->recJobRepository->saveHrTrackingStep($job_id, $request);
    }

    /**
     * HR Tracking view
     *
     * @param [type] $id
     * @return void
     */
    public function hrTracking($job_id)
    {
        $job = $this->recJobRepository->get($job_id);
        $already_processed_process_ids = array();
        foreach ($job->processes as $each_process) {
            $already_processed_process_ids[$each_process->process_id] = $each_process;
        }
        $process_lookups = $this->recJobProcessLookupRepository->getAll();

        $users = $this->userRepository->getUserLookupByPermission(['rec-hr-tracking']);
        return view('recruitment::job.hr-tracking', compact('job', 'process_lookups', 'users', 'already_processed_process_ids'));
    }

    /**
     * Remove an HR tracking step
     *
     * @param [type] $job_id
     * @param [type] $step_id
     * @return void
     */
    public function hrTrackingRemove($job_id, $step_id)
    {
        return $this->recJobRepository->deleteHrTracking($job_id, $step_id);
    }


    /**
     * Load datatable with Status Summary
     *
     * @return void
     */
    public function hrTrackingSummaryList(Request $request)
    {
        $client_id = $request->get('client_id');
        $job_list = $this->recJobRepository->getJobs($job_status = null, $filter = false, $customer_session = true, $client_id);
        $datatable_rows = $this->recJobRepository->prepareDataForJobTrackingSummary($job_list);
        return datatables()->of($datatable_rows)->make(true);
    }

    /**
     * Archive Jobs
     *
     * @param Request $request
     * @return json
     */
    public function archiveJob(Request $request)
    {
        $job_ids = json_decode($request->get('job_ids'));
        return $this->recJobRepository->updateJobStatus($job_ids, 'archive');
    }

    public function getJobsUnderCustomer($customer_id = null)
    {
        $result = $this->recJobRepository->getJobsBasedOnCustomer($customer_id);
        return response()->json(array('success' => true, 'result' => $result));
    }

    /**
     * To vew job description html content
     *
     * @param [type] $job_id
     * @return void
     */
    public function viewJobDescription($job_id)
    {
        $job = $this->recJobRepository->get($job_id);
        return view('recruitment::job.job-view-description', compact('job'));
    }

    public function customerDocumentAllocation($cid) {
        return $this->recDocumentAllocationRepository->singleCustomerDocuments($cid);
     }
}
