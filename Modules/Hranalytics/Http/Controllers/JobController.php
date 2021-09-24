<?php

namespace Modules\Hranalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\CandidateAttachmentLookup;
use Modules\Admin\Models\JobRequisitionReasonLookup;
use Modules\Admin\Repositories\CandidateAssignmentTypeLookupRepository;
use Modules\Admin\Repositories\CandidateExperienceLookupRepository;
use Modules\Admin\Repositories\CriteriaLookupRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\JobProcessLookupRepository;
use Modules\Admin\Repositories\PositionLookupRepository;
use Modules\Admin\Repositories\TrainingLookupRepository;
use Modules\Admin\Repositories\TrainingTimingLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Http\Requests\HrTrackingRequest;
use Modules\Hranalytics\Http\Requests\JobRequest;
use Modules\Hranalytics\Repositories\EmployeeExitInterviewRepository;
use Modules\Hranalytics\Repositories\JobRepository;

class JobController extends Controller
{

    protected $jobRepository,
    $userRepository,
    $customerRepository,
    $positionLookupRepository,
    $assignmentTypesLookupRepository,
    $trainingLookupRepository,
    $trainingTimingLookupRepository,
    $criteriaLookupRepository,
    $candidateExperienceLookupRepository,
    $employeeExitInterviewRepository,
        $jobProcessLookupRepository;

    public function __construct(
        UserRepository $userRepository, JobRepository $jobRepository,
        CustomerRepository $customerRepository, PositionLookupRepository $positionLookupRepository,
        CandidateAssignmentTypeLookupRepository $assignmentTypesLookupRepository,
        TrainingLookupRepository $trainingLookupRepository, TrainingTimingLookupRepository $trainingTimingLookupRepository,
        CriteriaLookupRepository $criteriaLookupRepository, CandidateExperienceLookupRepository $candidateExperienceLookupRepository,
        JobProcessLookupRepository $jobProcessLookupRepository, EmployeeExitInterviewRepository $employeeExitInterviewRepository
    ) {
        $this->userRepository = $userRepository;
        $this->jobRepository = $jobRepository;
        $this->positionLookupRepository = $positionLookupRepository;
        $this->assignmentTypesLookupRepository = $assignmentTypesLookupRepository;
        $this->trainingLookupRepository = $trainingLookupRepository;
        $this->trainingTimingLookupRepository = $trainingTimingLookupRepository;
        $this->criteriaLookupRepository = $criteriaLookupRepository;
        $this->candidateExperienceLookupRepository = $candidateExperienceLookupRepository;
        $this->customerRepository = $customerRepository;
        $this->jobProcessLookupRepository = $jobProcessLookupRepository;
        $this->employeeExitInterviewRepository = $employeeExitInterviewRepository;
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
        $hr_reps = $this->userRepository->getUserLookup(null, ['admin', 'super_admin'], null, null, ['assign_job_ticket']);
        return view('hranalytics::job.index', compact('hr_reps'));
    }

    /**
     * Get list of jobs
     *
     * @return datatable object
     */

    public function getList($job_status = null)
    {
        return datatables()->of($this->jobRepository->getJobs($job_status))->toJson();
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
        return view('hranalytics::job.create', compact('job', 'lookups', 'criterias'));
    }

    /**
     * To edit a job requisition
     *
     * @return void
     */
    public function edit($id)
    {
        $lookups = $this->getLookups();
        $job = $this->jobRepository->get($id);
        $shifts = json_decode(html_entity_decode($job->shifts), true);
        $days_required = json_decode(html_entity_decode($job->days_required), true);
        $criterias = json_decode(html_entity_decode($job->criterias), true);
        return view('hranalytics::job.edit', compact('job', 'lookups', 'shifts', 'days_required', 'criterias'));
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
        $job = $this->jobRepository->get($id);
        $candidateAttachment_default_Lookups = CandidateAttachmentLookup::where('job_id', null)
            ->orderby('id', 'asc')
            ->pluck('attachment_name', 'id')
            ->toArray();
        $candidateAttachment_custom_Lookups = CandidateAttachmentLookup::where('job_id', $id)
            ->orderby('id', 'asc')
            ->pluck('attachment_name', 'id')
            ->toArray();
        $custom_lookup_count = count($candidateAttachment_custom_Lookups);
        $mandatory_attachment_ids = json_decode($job->required_attachments);

        return view('hranalytics::job.view', compact('job', 'candidateAttachment_default_Lookups', 'candidateAttachment_custom_Lookups', 'mandatory_attachment_ids', 'custom_lookup_count', 'lookups'));
    }

    /**
     * To vew job description html content
     *
     * @param [type] $job_id
     * @return void
     */
    public function viewJobDescription($job_id)
    {
        $job = $this->jobRepository->get($job_id);
        return view('hranalytics::job.job-view-description', compact('job'));
    }
    /**
     * To store a newly created job
     *
     * @param JobRequest $request
     * @return void
     */
    public function store(JobRequest $request)
    {
        return $this->jobRepository->store($request);
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
        return $this->jobRepository->saveMandatoryAttachements($job_id, $request);
    }

    /**
     * Change Status of Job
     *
     * @param Request $request
     * @return json
     */
    public function changeStatus($job_id, Request $request)
    {
        return $this->jobRepository->updateJobStatus([$job_id], $request->get('status'), $request->get('hr_rep_id'), $request->get('status_reason'));
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
        return $this->jobRepository->updateJobStatus($job_ids, 'archive');
    }

    /**
     * HR Tracking view
     *
     * @param [type] $id
     * @return void
     */
    public function hrTracking($job_id)
    {
        $job = $this->jobRepository->get($job_id);
        $already_processed_process_ids = array();
        foreach ($job->processes as $each_process) {
            $already_processed_process_ids[$each_process->process_id] = $each_process;
        }
        $process_lookups = $this->jobProcessLookupRepository->getAll();
        $users = $this->userRepository->getUserLookupByPermission(['hr-tracking']);
        return view('hranalytics::job.hr-tracking', compact('job', 'process_lookups', 'users', 'already_processed_process_ids'));
    }

    /**
     * HR tracking update
     *
     * @param Request $request
     * @return void
     */
    public function hrTrackingStore($job_id, HrTrackingRequest $request)
    {
        return $this->jobRepository->saveHrTrackingStep($job_id, $request);
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
        return $this->jobRepository->deleteHrTracking($job_id, $step_id);
    }

    /**
     *Load Status Summary of Jobs Page
     *
     * @return view
     */
    public function hrTrackingSummary()
    {
        return view('hranalytics::job.hr-tracking-summary');
    }

    /**
     * Load datatable with Status Summary
     *
     * @return void
     */
    public function hrTrackingSummaryList()
    {
        $job_list = $this->jobRepository->getJobs($job_status = null, $filter = false, $customer_session = true);
        $datatable_rows = $this->jobRepository->prepareDataForJobTrackingSummary($job_list);
        return datatables()->of($datatable_rows)->make(true);
    }

    /**
     * Get all lookup values
     *
     * @return lookups
     */
    public function getLookups()
    {
        $default = [null => 'Please Select'];
        $lookups['job_requisition_reason_lookups'][] = $default + JobRequisitionReasonLookup::where('parent_id', '0')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + JobRequisitionReasonLookup::where('parent_id', '1')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + JobRequisitionReasonLookup::where('parent_id', '2')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + JobRequisitionReasonLookup::where('parent_id', '11')->pluck('reason', 'id')->toArray();
        $lookups['job_requisition_reason_lookups'][] = $default + JobRequisitionReasonLookup::where('parent_id', '12')->pluck('reason', 'id')->toArray();
        $lookups['positions_lookups'] = $default + $this->positionLookupRepository->getList();
        $lookups['assignment_type_lookups'] = $default + $this->assignmentTypesLookupRepository->getList();
        $lookups['training_lookups'] = $default + $this->trainingLookupRepository->getList();
        $lookups['training_timing_lookups'] = $default + $this->trainingTimingLookupRepository->getList();
        $lookups['criteria_lookups'] = $default + $this->criteriaLookupRepository->getList();
        $lookups['experience_lookups'] = $default + $this->candidateExperienceLookupRepository->getList();
        $lookups['customers'] = $default + $this->customerRepository->getList();
        $lookups['resignation_list'] = $default + $this->employeeExitInterviewRepository->getResignationList();
        $lookups['termination_list'] = $default + $this->employeeExitInterviewRepository->getTerminationList();
        return $lookups;
    }

    /**
     * Get customer/project details
     *
     * @param [type] $customer_id
     * @return void
     */
    public function getCustomer($customer_id)
    {
        return response()->json($this->customerRepository->getSingleCustomer($customer_id));
    }

    /**
     * Plot all jobs in google map
     *
     * @return void
     */
    public function jobMapping(Request $request)
    {
        $jobs = $this->jobRepository->getJobsForMapping($request);
        return view('hranalytics::job.jobs-in-map', compact('jobs', 'request'));
    }

}
