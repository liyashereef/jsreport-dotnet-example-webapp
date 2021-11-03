<?php

namespace Modules\Supervisorpanel\Repositories;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Services\HelperService;
use Auth;
use Carbon\Carbon;
use Config;
use DB;
use Exception;
use File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mail;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\CustomerIncidentPriority;
use Modules\Admin\Models\CustomerIncidentSubjectAllocation;
use Modules\Admin\Models\PayPeriod;
use Modules\Admin\Models\IncidentRecipient;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerIncidentSubjectAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\IncidentPriorityLookupRepository;
use Modules\Admin\Repositories\IncidentReportSubjectRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\SupervisorPanel\Mail\IncidentReported;
use Modules\Supervisorpanel\Models\IncidentAttachment;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Supervisorpanel\Models\IncidentStatusList;
use Modules\Supervisorpanel\Models\IncidentStatusLog;
use Modules\Timetracker\Repositories\ImageRepository;
use PDF;
use App\Repositories\MailQueueRepository;
use Modules\ClientApp\Repositories\NotificationRepository;

class IncidentReportRepository
{

    private $directory_seperator;
    private $extension_seperator;
    private static $payperiod_id;
    private static $customer_id;
    protected $attachment_repository;
    protected $userRepository;
    protected $imageRepository;
    protected $subjectRepository;
    protected $customerEmployeeAllocationModel;
    protected $customerRepository;
    protected $customer_employee_allocation_repository;
    protected $priorityLookUpRepository;
    protected $helper_service;
    protected $notificationRepository;

    /**
     * IncidentReportRepository constructor.
     * @param \App\Repositories\AttachmentRepository $attachment_repository
     */
    public function __construct(
        AttachmentRepository $attachment_repository,
        UserRepository $userRepository,
        ImageRepository $imageRepository,
        IncidentReportSubjectRepository $subjectRepository,
        CustomerEmployeeAllocation $customerEmployeeAllocationModel,
        CustomerRepository $customerRepository,
        CustomerEmployeeAllocationRepository $customer_employee_allocation_repository,
        IncidentPriorityLookupRepository $priorityLookUpRepository,
        CustomerIncidentSubjectAllocationRepository $customerIncidentSubjectAllocationRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->directory_seperator = "/";
        $this->extension_seperator = ".";
        $this->attachment_repository = $attachment_repository;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
        $this->subjectRepository = $subjectRepository;
        $this->customerEmployeeAllocationModel = $customerEmployeeAllocationModel;
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
        $this->customerRepository = $customerRepository;
        $this->coo_email = Config::get('globals.coo_email');
        $this->coo_valid_email = Config::get('globals.coo_mail_valid');
        $this->priorityLookUpRepository = $priorityLookUpRepository;
        $this->helper_service = new HelperService();
        $this->model = new IncidentReport();
        $this->incident_status_log_model = new IncidentStatusLog();
        $this->customerIncidentSubjectAllocationRepository = $customerIncidentSubjectAllocationRepository;
        $this->mailQueueRepository = new MailQueueRepository();
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Init function to render table and returns view without data
     * @param $customer_id
     * @param $payperiod_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function incidentReportInit($customer_id, $payperiod_id)
    {
        $payperiod_name_obj = PayPeriod::select('pay_period_name')->find($payperiod_id);
        $payperiod_name = $payperiod_name_obj->pay_period_name;
        $sitename = Customer::select('client_name', 'project_number', 'address', 'city', 'province')->where('id', $customer_id)->first();
        $current_date = Carbon::now();
        for ($m = 1; $m <= 12; $m++) {
            $month_array[$m] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
        }
        $formatted_date = $current_date->format('F d, Y');
        $user_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $status_list = $this->getStatusList();
        $subject_list = $this->subjectRepository->getList();
        $priorityLookUpRepository = $this->priorityLookUpRepository->getList();
        return view('supervisorpanel::partials.incident-report', compact('payperiod_name', 'payperiod_id', 'customer_id', 'formatted_date', 'user_name', 'status_list', 'subject_list', 'sitename', 'current_date', 'month_array', 'priorityLookUpRepository'));
    }

    /**
     * Get the path including file name to incident report attachment
     * @param $incident_report_id
     * @return string
     */
    public function incidentReportAttachment($incident_report_id)
    {
        $path = array();
        $incident_attachment = IncidentReport::select('attachment', 'customer_id', 'payperiod_id')->find($incident_report_id);
        $customer_id = $incident_attachment->customer_id;
        $payperiod_id = $incident_attachment->payperiod_id;
        if (!empty($incident_attachment->attachment)) {
            $path['path'] = storage_path('app') . $this->directory_seperator . config('globals.incident_attachment_folder') . $this->directory_seperator . $customer_id . $this->directory_seperator . $payperiod_id . $this->directory_seperator . $incident_attachment->attachment;
            $file_name_arr = explode(".", $incident_attachment->attachment);
            if (isset($file_name_arr) && count($file_name_arr) >= 2) {
                $path['file'] = "Incident_Report" . $this->extension_seperator . $file_name_arr[(count($file_name_arr) - 1)];
            }
            //$path = storage_path($file_location);
        }
        return $path;
    }

    /**
     * Function to prepare array for incident report listing
     * @param $customer_id
     * @param $payperiod_id
     * @return array
     */
    public function incidentReportList($customer_id, $payperiod_id)
    {
        $datavalues = array();
        $index = 1;
        $final_status = '';
        $incident_list = IncidentReport::with('incidentStatusLogWtihList', 'incidentAttachment', 'priority')
            ->where('customer_id', $customer_id)
            ->where('payperiod_id', $payperiod_id)
            //->where('amendment', 0)
            // ->orderby('id', 'desc')
            ->get();

        foreach ($incident_list as $each_incident) {
            $each_row['id'] = $each_incident->id;
            $each_row['index'] = $index;
            $each_row['description'] = $each_incident->subject_name_with_fallback;
            $each_row['title'] = $each_incident->title;
            $priority = ' -- ';
            if (!empty($each_incident->priority)) {
                $priority = $each_incident->priority->value;
            }
            $each_row['priority'] = $priority;
            if (!empty($each_incident->attachment)) {
                $each_row['attachment'] = route('incident.attachement', ['incident_report_id' => $each_incident->id]);
            } else {
                $each_row['attachment'] = '';
            }
            $status_arr = array();
            $attachment_arr = array();
            foreach ($each_incident->incidentAttachment as $each_attachment) {
                $att_arr = array();
                $att_arr['url'] = route('filedownload', ['id' => $each_attachment->attachment_id, 'module' => 'incident']);
                $att_arr['name'] = $each_attachment['short_description'];
                $attachment_arr[] = $att_arr;
            }

            $each_row['attachment_arr'] = $attachment_arr;

            foreach ($each_incident->incidentStatusLogWtihList as $each_status) {
                $status = $each_status->incidentStatusList->status;
                $user_fullname = (isset($each_status->user)) ? $each_status->user->first_name . ' ' . $each_status->user->last_name : "";
                $user_empno = (isset($each_status->user) && isset($each_status->user->employee->employee_no)) ? $each_status->user->employee->employee_no : "--";
                if (isset($each_status->created_at)) {
                    $date_obj = Carbon::parse($each_status->created_at);
                    $date = $date_obj->format('F d, Y');
                    $time = $date_obj->format('h:i A');
                } else {
                    $date = "";
                    $time = "";
                }
                $notes = ($each_status->incident_status_list_id != 1) && ($each_status->notes) ? $each_status->notes : "--";
                $status_arr[] = array("status" => $status, "user_name" => $user_fullname, "date" => $date, "time" => $time, "employee_no" => $user_empno, "notes" => $notes);
                $final_status = $status;
                $status_updated = $each_status->updated_at;
            }
            $each_row['status'] = $status_arr;
            $each_row['final_status'] = $final_status;
            $each_row['action'] = '';
            $status_updated = $each_incident->incidentStatusLogWtihList->last()->updated_at;
            $each_row['updated_at_time'] = $status_updated->format('Y-m-d H:i:s');
            array_push($datavalues, $each_row);
            $index += 1;
        }
        return $datavalues;
    }

    /**
     * Function handle save of incident report along with attachments
     * @param $request
     * @return bool
     */
    public function storeIncidentReport($request)
    {
        //dd($request->all());
        /*$incident_report_subject_list = $this->subjectRepository->getList() + ['Others'];*/
        $incident_report_subject_list = $this->subjectRepository->getList();

        IncidentReportRepository::$payperiod_id = $request->payperiod_id;
        IncidentReportRepository::$customer_id = $request->customer_id;

        $incident_report_id = $request->id;
        $incidentReport['customer_id'] = $request->customer_id;
        $incidentReport['payperiod_id'] = $request->payperiod_id;
        if ($incident_report_subject_list[$request->subject] == 'Others') {
            $custom_subject['id'] = null;
            $custom_subject['subject'] = $request->custom_subject;
            $last_inserted_custom_subject = $this->subjectRepository->save($custom_subject);
            $incidentReport['subject_id'] = $last_inserted_custom_subject->id;
            $incidentReport['description'] = $last_inserted_custom_subject->subject;
        } else {
            $incidentReport['subject_id'] = $request->subject;
            $incidentReport['description'] = $incident_report_subject_list[$request->subject];
        }

        $incidentReport['occurance_datetime'] = $request->yearvalue . '-' . $request->month . '-' . $request->date . ' ' . $request->time;
        $incidentReport['incident_report_uploaded'] = $request->upload_incident_report;
        $incidentReport['time_of_day'] = $request->time_of_day;
        $incidentReport['source'] = 'Web';
        $incidentReport['notes'] = $request->incident_detail;
        $incidentReport['title'] = $request->title;
        $incidentReport['priority_id'] = $request->priority_id;
        $status_id = 1;
        $file_obj = $request->file('report_attachment');
        $attachment_arr = $attachment_id_array = $all_attachment_arr = array();
        $all_attachment_arr = $request->all_attachments;
        $file_name = null;

        if (isset($request->attachment_list) && count($request->attachment_list) > 0) {
            foreach ($request->attachment_list as $each_attachment) {
                $each_attachment_obj = json_decode($each_attachment);
                array_push($attachment_arr, array('attachment_id' => $each_attachment_obj->id, 'attachment_name' => $each_attachment_obj->name));
            }
        }
        if (isset($file_obj)) {
            $file = $this->attachment_repository->saveAttachmentFile('incident', $request, 'report_attachment');
            array_push($attachment_arr, array('attachment_id' => $file['file_id'], 'attachment_name' => 'Manual Incident Report'));
            if (!$file) {
                return false;
            }
        }

        $incidentReport['updated_by'] = Auth::user()->id;
        $incident_report = $request;
        $customer = $this->customerRepository->getCustomerWithMangers($incidentReport['customer_id']);

        $incident_report->request->add(['area_manager' => isset($customer['areamanager']['full_name']) ? $customer['areamanager']['full_name'] : "--"]);
        $incident_report->request->add(['supervisor' => isset($customer['supervisor']['full_name']) ? $customer['supervisor']['full_name'] : "--"]);
        $incident_report->request->add(['fullname' => Auth::user()->full_name]);
        $incident_report->request->add(['details' => $request->incident_detail]);
        // $incident_report->request->add(['site' => ' (' . $customer['details']['project_number'] . ') ' . $customer['details']['client_name'] . ',' . $customer['details']['address'] . ',' . $customer['details']['city']]);
        $customer_details = $this->customerRepository->getSingleCustomer((int) ($incidentReport['customer_id']));
        $incident_report_result = $this->incidentSave($incident_report_id, $incidentReport);

        if (isset($attachment_arr) && count($attachment_arr) > 0) {
            $all_attachment_arr = array();
            foreach ($attachment_arr as $each_attachment) {
                $this->storeIncidentAttachments($incident_report_result->id, $each_attachment['attachment_id'], $each_attachment['attachment_name']);
                $this->attachment_repository->setFilePersistant($each_attachment['attachment_id']);
                //remove attachments that are saved from all attachments array
                $all_attachment_arr = array_diff($all_attachment_arr, [$each_attachment['attachment_id']]);
            }
        }
        //delete all other temporary files from the system
        if (isset($all_attachment_arr) && count($all_attachment_arr) > 0) {
            foreach ($all_attachment_arr as $removed_file_id) {
                $this->attachment_repository->removeTempFile('incident', $removed_file_id);
            }
        }
        $allowed = array('gif', 'png', 'jpg', 'jpeg', 'svg', 'bmp');
        foreach ($attachment_arr as $key => $arr) {
            $attachment = Attachment::where('id', $arr['attachment_id'])->first();
            if (in_array($attachment->assumed_ext, $allowed)) {
                $attachment_id_array[$key]['name'] = $attachment->hash_name;
            }
        }
        $data['subject_id'] = $incidentReport['subject_id'];
        $data['priority_id'] = $incidentReport['priority_id'];
        $data['customer_id'] = $incidentReport['customer_id'];
        $data['payperiod_id'] = $incidentReport['payperiod_id'];
        $data['attachment_id_array'] = $attachment_id_array;
        $data['site'] = ' (' . $customer['details']['project_number'] . ') ' . $customer['details']['client_name'] . ', ' . $customer['details']['address'] . ', ' . $customer['details']['city'];
        $data['month'] = date("F", mktime(0, 0, 0, $request->month, 1));
        $data['year'] = $request->yearvalue;
        $incidentReportAttachmentFilename = $this->pdfGenerate($incident_report, $customer_details, $data);
        IncidentReport::where('id', $incident_report_result->id)->update(['attachment' => $incidentReportAttachmentFilename]);
        $this->storeIncidentReportLog($incident_report_result->id, $status_id, $incidentReport['notes'], Auth::user()->id, Auth::user()->id);
        return $incident_report_result;
    }

    /**
     * To send notificaion to area managers
     *
     * @param [type] $incident_report
     * @return void
     * @throws Exception
     */

    public function sendNotification($incident_report)
    {
        try {
            /*send mail - start */
            $email_ids = $email_id = array();
            $incidentReport = IncidentReport::where('id', $incident_report->id)->first();
            $file_path = $this->getAttachmentPath($incidentReport->customer_id, $incidentReport->payperiod_id);
            $file_url = storage_path('app') . $this->directory_seperator . $file_path . $this->directory_seperator . $incidentReport->attachment;
            $area_managers = $this->userRepository->allocationUserList($incidentReport->customer->id, ['area_manager', 'supervisor']);
            $recipient_email = IncidentRecipient::where('customer_id', $incidentReport->customer_id)->where('priority_id', $incidentReport->priority_id)->pluck('email')->toArray();
            $email_ids = data_get($area_managers, '*.email');
            $email_id = array_merge($email_ids, $recipient_email);
            if ($this->coo_valid_email == true) {
                array_push($email_id, $this->coo_email);
                try {
                    if ($this->coo_valid_email == true) {
                        array_push($email_id, $this->coo_email);
                    }
                } catch (\Exception $e) {
                    Log::channel('customlog')->info('-----IncidentReport---send-notification-------- ' . $e->getMessage());
                }
            }

            $mail = Mail::to($email_id);
            try {
                $mail->send(new IncidentReported($incidentReport, 'mail.incident-report.create', $file_url));
            } catch (\Throwable $th) {
                //throw $th;
            }
            /*send mail - end */
        } catch (Exception $e) {
            Log::channel('customlog')->info('-----IncidentReport---send-notification-------- ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Function to store incident status
     * @param $request
     */
    public function storeIncidentStatus($request)
    {
        $incident_report_id = $request->id;
        $notes = $request->notes;
        $status_id = $request->status;
        $created_by = Auth::user()->id;
        $updated_by = Auth::user()->id;
        $diff_in_minutes = null;
        if ($request->status == config('globals.closed_incident_report_status')) { //Closed status-3
            $report = IncidentReport::where('id', $incident_report_id)->first(); //Fetch open status-1 result
            $now = Carbon::now();
            if ($report->occurance_datetime != null) {
                $start_time = Carbon::parse($report->occurance_datetime);
            } else {
                $start_time = Carbon::parse($report->created_at);
            }
            $diff_in_minutes = $start_time->diffInMinutes($now);
        }
        $this->storeIncidentReportLog($incident_report_id, $status_id, $notes, $created_by, $updated_by, $diff_in_minutes);
    }

    /**
     * Update Closed time for all Incident Reports
     * @param $request
     */
    public function updateClosedTimeForAllIncidentReports()
    {
        $response = 0;
        $occurance = null;
        $all_reports = IncidentReport::get();
        foreach ($all_reports as $eachreport) {
            if ($eachreport->occurance_datetime == null) {
                IncidentReport::where('id', $eachreport->id)->update(['occurance_datetime' => $eachreport->created_at]);
                $occurance = $eachreport->created_at;
            } else {
                $occurance = $eachreport->occurance_datetime;
            }
            $status_log = $this->incident_status_log_model->where('incident_status_list_id', config('globals.closed_incident_report_status'))
                ->where('incident_report_id', $eachreport->id)
                ->orderBy('created_at', 'desc')->first();
            if (($occurance != null) && (!empty($status_log))) {
                $diff_in_minutes = $status_log->created_at->diffInMinutes($occurance);
                IncidentStatusLog::where('id', $status_log->id)->update(['closed_time' => $diff_in_minutes]);
                $response++;
            }
        }
        return $response;
    }

    /**
     *
     * Function to incident status log
     * @param $incident_id
     * @param $status_id
     * @param $notes
     * @param $created_bywhre
     * @param $updated_by
     */
    public function storeIncidentReportLog($incident_id, $status_id, $notes, $created_by, $updated_by, $closed_time = null, $suggested_incident_status_list_id = null, $amendment = null)
    {
        $incident_status_log = new IncidentStatusLog;
        $incident_status_log->incident_report_id = $incident_id;
        $incident_status_log->incident_status_list_id = $status_id;
        $incident_status_log->notes = $notes;
        $incident_status_log->created_by = $created_by;
        $incident_status_log->updated_by = $updated_by;
        $incident_status_log->closed_time = isset($closed_time) ? $closed_time : null;
        $incident_status_log->suggested_incident_status_list_id = isset($suggested_incident_status_list_id) ? $suggested_incident_status_list_id : 1;
        $incident_status_log->amendment = isset($amendment) ? 1 : 0;
        $incident_status_log->save();
    }

    /**
     * Function to store incident attachment
     * @param $incident_id
     * @param $attachment_id
     * @param $attachment_name
     */
    public function storeIncidentAttachments($incident_id, $attachment_id, $attachment_name)
    {
        $incident_attachments = new IncidentAttachment;
        $incident_attachments->incident_id = $incident_id;
        $incident_attachments->attachment_id = $attachment_id;
        $incident_attachments->short_description = $attachment_name;
        $incident_attachments->save();
    }

    /**
     * Function to get Attachment path for incident report
     * @param $customer_id
     * @param $payperiod_id
     * @return string
     */
    public function getAttachmentPath($customer_id, $payperiod_id)
    {
        return config('globals.incident_attachment_folder') . $this->directory_seperator . $customer_id . $this->directory_seperator . $payperiod_id;
    }

    /**
     * Function to get customer id
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Getter for payperiod id
     * @return mixed
     */
    public function getPayperiodId()
    {
        return $this->payperiod_id;
    }

    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $incident_attachment = IncidentAttachment::with('incident_report')->where('attachment_id', $file_id)->first();
        if (isset($incident_attachment->incident_report)) {
            $customer_id = $incident_attachment->incident_report->customer_id;
            $payperiod_id = $incident_attachment->incident_report->payperiod_id;
        } else {
            $customer_id = IncidentReportRepository::$customer_id;
            $payperiod_id = IncidentReportRepository::$payperiod_id;
        }

        return array(config('globals.incident_attachment_folder'), $customer_id, $payperiod_id);
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.incident_attachment_folder'), $request->customer_id, $request->payperiod_id);
    }

    /**
     * Function to get all incident status
     * If as status text is provided, corresponding id is returned
     * @param null $status_text
     * @return mixed
     */
    private function getStatusList($status_text = null)
    {

        $status_list_obj = IncidentStatusList::select('id', 'status')->get();
        if (isset($status_text)) {
            foreach ($status_list_obj as $each_status) {
                if (strtolower($status_text) == strtolower($each_status->status)) {
                    return $each_status->id;
                }
            }
        } else {
            return $status_list_obj;
        }
    }

    /**
     * Function to save attachment file - Incident Report
     * @param $file_obj
     * @param $customer_id
     * @param $payperiod_id
     * @param $request
     * @return bool|string
     */
    private function saveAttachmentFile($file_obj, $customer_id, $payperiod_id, $request)
    {

        try {
            $file_name = "";
            $file_path = $this->getAttachmentPath($customer_id, $payperiod_id); //config('globals.incident_attachment_folder') . $this->directory_seperator . $customer_id . $this->directory_seperator . $payperiod_id;
            //$file_name = pathinfo($file_obj->getClientOriginalName())['filename'] . date("U") . $this->extension_seperator . $file_obj->getClientOriginalExtension();
            if ($request->file('report_attachment')->guessExtension() == null) {
                $file_name = $request->file('report_attachment')->hashName() . $this->extension_seperator . $file_obj->getClientOriginalExtension();
            } else {
                $file_name = $request->file('report_attachment')->hashName();
            }

            Storage::putFileAs($file_path, $request->file('report_attachment'), $file_name);
            //$request->file('report_attachment')->store($file_path);
            return $file_name;
        } catch (Exception $ex) {
            return false;
        }

        //$request->file('report_attachment')->storeAs($file_path, $file_name);
        //return $file_name;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getIncidentReportDashList($customer_session = false, $widgetRequest = false, $request = null)
    {
        $customers = [];
        $statusList = incidentStatusList::get()->pluck("id")->toArray();
        $selected_customer_ids = $this->helper_service->getCustomerIds();
        if ($customer_session && !empty($selected_customer_ids)) {
            $customer_details = Customer::whereIn('id', $selected_customer_ids)->with(
                'supervisorCustomer.trashedUser',
                'supervisorCustomer.trashedUser.userHierarchy',
                'supervisorCustomer.trashedUser.userHierarchy.trashed_user_reported',
                'supervisorCustomer.trashedUser.trashedEmployee'
            );
        } else {
            $customer_details = Customer::with(
                'supervisorCustomer.trashedUser',
                'supervisorCustomer.trashedUser.userHierarchy',
                'supervisorCustomer.trashedUser.userHierarchy.trashed_user_reported',
                'supervisorCustomer.trashedUser.trashedEmployee'
            );
        }

        if (Auth::user()->hasAnyPermission(['view_all_incident_report', 'admin', 'super_admin'])) {
            $customers = $customer_details->pluck('id');
        } else {
            if ($customer_session && !empty($selected_customer_ids)) {
                $customers = $this->customerEmployeeAllocationModel
                    ->where('user_id', Auth::user()->id)
                    ->where('customer_id', $selected_customer_ids)
                    ->pluck('customer_id');
            } else {
                $customers = $this->customerEmployeeAllocationModel
                    ->where('user_id', Auth::user()->id)
                    ->pluck('customer_id');
            }
        }
        if (isset($request->customer)) {
            $customers = $request->customer;
        }
        /*if (Auth::user()->role_id == 4) {
        // Area manager
        $usersObj = DB::table('user_hierarchies')->where('report_to', Auth::user()->id)->where('start_date', '<=', Carbon::today())->where('end_date', '>=', Carbon::today())->whereNull('deleted_at')->pluck('user_id');
        $users = $usersObj->toArray();
        array_push($users, Auth::user()->id);
        $customer_details->whereHas('employeeCustomer', function ($q) use ($users) {
        $q->whereIn('user_id', $users);
        });
        } else if (Auth::user()->role_id == 2) {
        // Supervisor
        $users = [Auth::user()->id];
        $customer_details->whereHas('employeeCustomer', function ($q) use ($users) {
        $q->whereIn('user_id', $users);
        });
        }
        $customers = $customer_details->pluck('id');*/

        $incident_list = IncidentReport::whereHas('payperiod')->with(
            [
                'customer',
                'payperiod',
                'priority',
                'latestStatus.incidentStatusList',
                'reporter',
                'incident_report_subject',
                'reporter.trashedEmployee',
                'latestStatus' => function ($query) {
                    $query->select(
                        DB::raw('DATE_FORMAT(created_at, "%M %d,%Y") as updated_at_date'),
                        'id',
                        'created_at',
                        'updated_at',
                        'incident_report_id',
                        'incident_status_list_id',
                        'closed_time'
                    );
                },
            ]
        )
            ->when(count($customers) > 0, function ($q) use ($customers) {
                return $q->whereIn('customer_id', $customers);
            })
            ->when($request != null, function ($q) use ($request) {
                return $q->when($request->date_from != "", function ($query) use ($request) {
                    return $query->whereBetween('created_at', [$request->date_from, Carbon::parse($request->date_till)->endOfDay()]);
                });
            })->orderby('created_at', 'desc');
        if ($widgetRequest) {
            $incident_list->limit(config('dashboard.incident_report_row_limit'));
        }

        //Filter by url attributes
        if ($request != null) {
            $from = $request->input('from');
            $to = $request->input('to');

            if (!empty($from) && !empty($to)) {
                $incident_list = $incident_list->whereDate('created_at', '<=', Carbon::parse($to));
                $incident_list = $incident_list->whereDate('created_at', '>=', Carbon::parse($from));
            }
        }

        //filter by status
        
        if (isset($request) && $request->input('status') != null) {
            $status = $request->input('status');
            $filterString = "";
            $statusArray = [];
            foreach ($status as $key => $value) {
                $statusArray[] = intval($value);
                if ($key === 0) {
                    $filterString = $value;
                } else {
                    $filterString .= "," . $value;
                }
            }
            $incident_list = $incident_list->whereIn(\DB::raw("(select `incident_status_list_id` from incident_status_logs where `incident_report_id`=incident_reports.`id` order by created_at desc limit 0,1)"), $statusArray);
        } else {
            $incident_list = $incident_list->whereHas("latestStatus", function ($q) use ($statusList) {
                $q->whereIn('incident_status_list_id', $statusList);
            });
        }
        return $incident_list->get();
    }

    public function getCustomerList($status = ACTIVE, $areamanager = [], $supervisor = [])
    {
        $customers = [];
        $customerarray = [];
        $perm = 1;
        if (\Auth::user()->can('view_all_incident_report') || \Auth::user()->hasAnyPermission('admin', 'super_admin')) {
            $perm = 1;
            $customers = Customer::with('employeeCustomerAreaManager')
                ->where('active', $status)->when($areamanager, function ($q) use ($areamanager) {
                    $q->whereHas('employeeCustomerAreaManager', function ($query) use ($areamanager) {
                        return $query->whereIn('user_id', $areamanager);
                    });
                })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })->get();
        } elseif (\Auth::user()->can('view_allocated_incident_report')) {
            $perm = 2;
            $customers = CustomerEmployeeAllocation::where(['user_id' => \Auth::user()->id])->when($areamanager, function ($q) use ($areamanager) {
                $q->whereHas('customer.employeeCustomerAreaManager', function ($query) use ($areamanager) {
                    return $query->whereIn('user_id', $areamanager);
                });
            })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('customer.employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })->get();
        } else {
            $perm = 2;
            $customers = CustomerEmployeeAllocation::where(['user_id' => \Auth::user()->id])
                ->when($areamanager, function ($q) use ($areamanager) {
                    $q->whereHas('customer.employeeCustomerAreaManager', function ($query) use ($areamanager) {
                        return $query->whereIn('user_id', $areamanager);
                    });
                })
                ->when($supervisor, function ($q) use ($supervisor) {
                    $q->whereHas('customer.employeeCustomerSupervisor', function ($query) use ($supervisor) {
                        return $query->whereIn('user_id', $supervisor);
                    });
                })
                ->get();
        }

        $i = 0;
        foreach ($customers as $customer) {
            if ($perm == 1) {
                if ($customer->active == 1) {
                    $customerarray[$i]["id"] = $customer->id;
                    $customerarray[$i]["project_number"] = $customer->project_number;
                    $customerarray[$i]["client_name"] = $customer->client_name;
                    try {
                        $managername = "";
                        foreach ($customer->employeeCustomerAreaManager as $aremanagersarray) {
                            if ($managername == "") {
                                $managername .= $aremanagersarray->trashedUser->getFullNameAttribute();
                            } else {
                                $managername .= " , " . $aremanagersarray->trashedUser->getFullNameAttribute();
                            }
                        }

                        $full_name = $managername;
                        $customerarray[$i]["areamanager"] = $full_name;
                    } catch (\Throwable $th) {
                        $customerarray[$i]["areamanager"] = "";
                    }

                    try {
                        $supervisorname = "";
                        foreach ($customer->employeeCustomerSupervisor as $supervisorarray) {
                            if ($supervisorname == "") {
                                $supervisorname .= $supervisorarray->trashedUser->getFullNameAttribute();
                            } else {
                                $supervisorname .= " , " . $supervisorarray->trashedUser->getFullNameAttribute();
                            }
                        }
                        $customerarray[$i]["supervisor"] = $supervisorname;
                    } catch (\Throwable $th) {
                        $customerarray[$i]["supervisor"] = "";
                    }
                }
            } elseif ($perm == 2) {
                if ($customer->customer->active == 1) {
                    $customerarray[$i]["id"] = $customer->customer->id;
                    $customerarray[$i]["project_number"] = $customer->customer->project_number;
                    $customerarray[$i]["client_name"] = $customer->customer->client_name;

                    try {
                        $customerarray[$i]["areamanager"] = $customer->customer->employeeLatestCustomerAreaManager->trashedUser->getFullNameAttribute();
                    } catch (\Throwable $th) {
                        $customerarray[$i]["areamanager"] = "";
                    }

                    try {
                        $customerarray[$i]["supervisor"] = $customer->customer->employeeLatestCustomerSupervisor->trashedUser->getFullNameAttribute();
                    } catch (\Throwable $th) {
                        $customerarray[$i]["supervisor"] = "";
                    }
                }
            }
            $i++;
        }
        return ($customerarray);
    }

    /**
     * Return datatable values as array
     *
     * @param empty
     */
    public function prepareIncidentReportList($incident_list)
    {

        $datatable_rows = array();
        foreach ($incident_list as $key => $each_job) {
            if (!empty($each_job->latestStatus)) {
                $each_row["updated_at"] = $each_job->latestStatus->created_at->format('Y-m-d');
                $each_row["updated_time"] = $each_job->latestStatus->created_at->format('h:i A');
            }
            $each_row["id"] = $each_job->id;
            $each_row["client_name"] = $each_job->customer->client_name;
            $each_row["created_at"] = Carbon::parse($each_job->created_at)->format("F d,Y");
            $each_row["customer_id"] = $each_job->customer->id;
            $each_row["payperiod_id"] = $each_job->payperiod->id;
            $each_row["pay_period_name"] = $each_job->payperiod->pay_period_name;
            $each_row["title"] = $each_job->title;
            $each_row["subject"] = (null != $each_job->incident_report_subject) ? $each_job->incident_report_subject->subject : $each_job->description;
            if (!empty($each_job->attachment)) {
                $each_row['attachment'] = route('incident.attachement', ['incident_report_id' => $each_job->id]);
            } else {
                $each_row['attachment'] = '';
            }
            $each_row["value"] = (null != $each_job->priority) ? $each_job->priority->value : '--';
            $each_row["reporter_first_name"] = ($each_job->reporter->first_name != null) ? $each_job->reporter->first_name : '';
            $each_row["reporter_last_name"] = ($each_job->reporter->last_name != null) ? $each_job->reporter->last_name : '';
            $each_row["employee_no"] = $each_job->reporter->trashedEmployee->employee_no;
            if (!empty($each_job->latestStatus)) {
                $each_row["updated_at_date"] = date_format(date_create($each_job->latestStatus->updated_at_date), "F d, Y");
                $each_row["status"] = $each_job->latestStatus->incidentStatusList->status;
                if ($each_job->latestStatus->closed_time != 0) {
                    $each_row["closed_time"] = $this->helper_service->convertToHoursMins($each_job->latestStatus->closed_time);
                } else {
                    $each_row["closed_time"] = '';
                }
            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    /**
     * IncidentReport Store-Mobile
     * @param $reportDetails
     * @param $user
     * @param $current_payperiod
     * @param $customer_details
     * @param string $source
     * @param bool $returnObj
     * @return bool|\Illuminate\Support\Collection
     */
    public function storeReport(
        $reportDetails,
        $user,
        $current_payperiod,
        $customer_details,
        $source = 'Mobile',
        $returnObj = false
    ) {
        // $incident_report_subject_list = $this->subjectRepository->getList();

        try {
            foreach ($reportDetails as $key => $incident_report) {
                $id = $incident_report->incident_id;
                if ($id != null) {
                    $report_id = $id;
                }
                // $incidentReport['description'] = $incident_report_subject_list[$incident_report->subject_id];
                $incidentReport['subject_id'] = (int) ($incident_report->subject_id);
                $incidentReport['customer_id'] = (int) ($incident_report->customer_id);
                $incidentReport['payperiod_id'] = (int) ($current_payperiod->id);
                $incidentReport['created_by'] = $user->id;
                $incidentReport['updated_by'] = $user->id;
                $incidentReport['source'] = $source;
                $incidentReport['time_of_day'] = $incident_report->time_of_day;
                $incidentReportTime = $incident_report->time;
                $incidentReportTimeArr = explode(":", $incident_report->time);
                if (sizeof($incidentReportTimeArr) < 3) {
                    $incidentReportTime = $incidentReportTime . ':00';
                }
                $incidentReport['occurance_datetime'] = $incident_report->year . '-' . $incident_report->month . '-' . $incident_report->date . ' ' . $incidentReportTime;
                $incidentReport['status'] = $incident_report->status;
                $incidentReport['notes'] = $incident_report->details;
                $incidentReport['title'] = $incident_report->title;
                $priority = $this->customerIncidentSubjectAllocationRepository->getPriorityId($incident_report->subject_id, $incident_report->customer_id);
                if (!empty($priority)) {
                    $incidentReport['priority_id'] = $priority->priority_id;
                } else {
                    $incidentReport['priority_id'] = $incident_report->priority;
                }
                $subject_id = $incident_report->subject_id;
                /* if ($subject_id == null) {
                $data['id'] = $incident_report->subject_id;
                $data['subject'] = $incident_report->subject_name;
                $subjectSave = $this->subjectRepository->save($data);
                $subject_id = $subjectSave->id;
                }*/
                $img = $incident_report->incident_attachments;
                $attachment = array();
                $attachment_id_array = array();
                foreach ($img as $key => $incidentImage) {
                    if ($incidentImage->image != "") {
                        $attachment[$key]['name'] = $this->saveIncidentFile($incidentReport['customer_id'], $incidentReport['payperiod_id'], $incidentImage->image);
                        $attachment[$key]['description'] = $incidentImage->description;
                        $attachment_id_array[$key]['name'] = $attachment[$key]['name'];
                    }
                }
                $data['subject_id'] = $subject_id;
                $data['priority_id'] = $incidentReport['priority_id'];
                $data['customer_id'] = $incidentReport['customer_id'];
                $data['payperiod_id'] = $incidentReport['payperiod_id'];
                $data['attachment_id_array'] = $attachment_id_array;
                $data['month'] = date("F", mktime(0, 0, 0, $incident_report->month, 1));
                $data['year'] = $incident_report->year;
                $data['site'] = ' (' . $customer_details->project_number . ') ' . $customer_details->client_name . ', ' . $customer_details->address . ', ' . $customer_details->city;
                $incidentReportAttachmentFilename = $this->pdfGenerate($incident_report, $customer_details, $data);

                $incidentReport['attachment'] = $incidentReportAttachmentFilename;
                $incidentReports = $this->incidentSave($id, $incidentReport);
                if ($incidentReports->id != null) {
                    $subject_code = $this->customerIncidentSubjectAllocationRepository->getSubjectCategoryCode($incident_report->subject_id, $incident_report->customer_id);
                    $incident_report_id_code = $subject_code . '-' . $incidentReports->id;
                    IncidentReport::where('id', $incidentReports->id)
                        ->update(['incident_report_id' => $incident_report_id_code]);
                }

                if ($id == null) {
                    $report_id = $incidentReports->id;
                }

                foreach ($attachment as $key => $value) {
                    $attach['hash_name'] = $value['name'];
                    $attach['file_module'] = 'incident';
                    $attach['persistent'] = 1;
                    $file_split = explode(".", $value['name']);
                    $file_ext = (sizeof($file_split) > 1) ? ($file_split[sizeof($file_split) - 1]) : "";
                    $attach['assumed_ext'] = $file_ext;
                    $attach['original_name'] = $value['name'];
                    $attach['original_ext'] = $file_ext;
                    $attachments_list = Attachment::create($attach);
                    $incident_attachment['incident_id'] = $incidentReports->id;
                    $incident_attachment['attachment_id'] = $attachments_list->id;
                    $incident_attachment['short_description'] = $value['description'];
                    IncidentAttachment::create($incident_attachment);
                }

                $this->storeIncidentReportLog($report_id, $incidentReport['status'], $incidentReport['notes'], Auth::user()->id, Auth::user()->id);
                $this->sendNotification($incidentReports);
                $this->notificationRepository->incidentNotification($incidentReport);

            }
            if ($returnObj) {
                $returnVal = (object) ['success' => true, 'incident' => $incidentReports];
                return $returnVal;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            if ($returnObj) {
                $returnVal = (object) ['success' => false, 'error' => $ex];
                return $returnVal;
            } else {
                return false;
            }
        }
    }

    /**
     * PDF generation through web and mobile end
     * @param $incident_report
     * @param $customer_details
     * @param $data
     * @return $filename
     */
    public function pdfGenerate($incident_report, $customer_details, $data)
    {

        $subjectData = $this->subjectRepository->get($data['subject_id']);

        $priorityData = $this->priorityLookUpRepository->get($data['priority_id']);
        // $attachment_id_array = $data['attachment_id_array'];
        // $month = $data['month'];
        $pdf = PDF::loadView('supervisorpanel::incident-pdf', compact('incident_report', 'customer_details', 'subjectData', 'data', 'priorityData'));

        $file_path = $this->getAttachmentPath($data['customer_id'], $data['payperiod_id']);
        $incidentReportAttachmentFilename = uniqid('incident_report_') . ".pdf";
        $path = storage_path('app') . $this->directory_seperator . $file_path;
        // $files = File::allFiles($path);
        // File::delete($files);
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        $filename = $this->directory_seperator . $incidentReportAttachmentFilename;
        $pdf->save($path . $filename);
        return $incidentReportAttachmentFilename;
    }

    public function incidentSave($id, $param)
    {

        $incident_report_result = IncidentReport::updateOrCreate([
            'id' => $id,
        ], $param);
        if (empty($id)) {
            IncidentReport::where('id', $incident_report_result->id)
                ->update(['created_by' => Auth::user()->id]);
            $status_id = $this->getStatusList('open');
            return $incident_report_result;
        }
        return false;
    }

    /**
     * IncidentReport File Store-Mobile
     * @param $customer_id
     * @param $payperiod_id
     * @param $image
     * @return $filename
     */
    private function saveIncidentFile($customer_id, $payperiod_id, $image)
    {
        $filename = uniqid('incident_report_');
        $return_array = array();
        $file_path = $this->getAttachmentPath($customer_id, $payperiod_id);
        $image = $this->imageRepository->imageFromBase64($image);
        $path = storage_path('app/') . $this->directory_seperator . $file_path . $this->directory_seperator . $filename . "." . $image['extension'];
        if (!file_exists(storage_path('app/') . $this->directory_seperator . $file_path)) {
            mkdir(storage_path('app/') . $this->directory_seperator . $file_path, 0777, true);
        }
        $entry = file_put_contents($path, $image['image']);
        return $filename . "." . $image['extension'];
    }

    /**FM Dashboard Map* Incident Report Counts For  and Count */

    /**
     * Incident Summary Counts For FM Dashboard Map and Count
     * @param $customer_ids
     * @param $from_date & $to_date
     * @return $data
     */

    public function getIncidentPriority($inputs)
    {

        $inputs = $this->helper_service->getFMDashboardFilters();

        /***
         * SELECT subject_id, irl.incident_status, count(irl.incident_status) FROM qa_cgl360.incident_reports ir
         * join (SELECT incident_report_id, max(incident_status_list_id) incident_status FROM
         * qa_cgl360.incident_status_logs group by incident_report_id) irl
         * on ir.id = irl.incident_report_id group by irl.incident_status,subject_id;
         */
        return $this->model
            ->join((\DB::raw("(SELECT incident_report_id, max(incident_status_list_id) as incident_status FROM
        incident_status_logs group by incident_report_id) irl")), 'id', '=', 'irl.incident_report_id')
            ->select('priority_id', 'irl.incident_status', DB::raw('count(irl.incident_status) as incident_status_count'))
            ->groupBy('irl.incident_status', 'priority_id')
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {
                    //For From date
                    if (!empty($inputs['from_date'])) {
                        $query->where('created_at', '>=', $inputs['from_date']);
                    }
                    //For to date
                    if (!empty($inputs['to_date'])) {
                        $query->where('created_at', '<=', $inputs['to_date']);
                    }
                    //For customer_ids
                    $query->whereIn('customer_id', $inputs['customer_ids']);
                }
            })
            ->orderBy('priority_id')
            ->whereNotNull('subject_id')
            ->whereNotNull('priority_id')
            ->with('priority')
            ->get();
    }

    public function getIncidentSummmory($inputs)
    {

        $inputs = $this->helper_service->getFMDashboardFilters();

        $queryresult = $this->model
            ->join((\DB::raw("(SELECT incident_report_id, max(incident_status_list_id) as incident_status FROM
        incident_status_logs group by incident_report_id) irl")), 'id', '=', 'irl.incident_report_id')
            ->select('subject_id', 'irl.incident_status', DB::raw('count(irl.incident_status) as incident_status_count'))
            ->groupBy('irl.incident_status', 'subject_id')
            ->where(function ($query) use ($inputs) {
                if (!empty($inputs)) {
                    //For From date
                    if (!empty($inputs['from_date'])) {
                        $query->where('created_at', '>=', $inputs['from_date']);
                    }
                    //For to date
                    if (!empty($inputs['to_date'])) {
                        $query->where('created_at', '<=', $inputs['to_date']);
                    }

                    //For customer_ids
                    $query->whereIn('customer_id', $inputs['customer_ids']);
                }
            })
            ->whereNotNull('subject_id')
            ->whereNotNull('priority_id')
            ->whereHas('incident_report_subject', function ($q) {
                $q->whereNotNull('incident_category_id');
            })
            ->with('incident_report_subject')
            ->orderBy('subject_id')
            ->get();
    }

    public function Kpi($request)
    {
        $startday = date('Y-m', strtotime("-365 days")) . "-01";
        $endday = date('Y-m', strtotime("+1 days")) . "-30";

        $customers = $request->get("customer-id");
        $subjectallocation = CustomerIncidentSubjectAllocation::with([
            "incidentReport" => function ($qry) use ($customers, $startday, $endday) {
                return $qry->with(["incidentStatusLog" => function ($q) {
                    return $q->where("incident_status_list_id", 3)->latest();
                }])->where("customer_id", $customers)
                    ->whereBetween("occurance_datetime", [$startday, $endday]);
            },
            "subject" => function ($q) {
                //return $q->orderBy("subject", "desc");
            },
        ])
            ->whereHas("subject", function ($q) {
                return $q->orderBy("subject", "asc");
            })->where("customer_id", $customers)->get();
        //dd($subjectallocation);
        $namearray = [];
        $subjectids = [];
        $reportsarray = [];
        if (isset($subjectallocation)) {
            foreach ($subjectallocation as $value) {
                $namearray[$value->subject_id] = $value->subject->subject;
                if (isset($value->incidentReport)) {
                    $reportsarray[$value->subject_id]["id"] = $value->subject_id;
                    $reportsarray[$value->subject_id]["name"] = $value->subject->subject;
                    $reportsarray[$value->subject_id]["response_time"] = $value->incident_response_time;

                    // $reportsarray[$value->subject_id]["fullrep"] = $value->incidentReport;
                    foreach ($value->incidentReport as $increport) {
                        if (isset(($increport->incidentStatusLog)[0]->closed_time)) {
                            $reportsarray[$value->subject_id]["incidents"][date(
                                "M-Y",
                                strtotime($increport->occurance_datetime)
                            )][$increport->id] = ($increport->incidentStatusLog)[0]->closed_time;
                        } else {
                            $reportsarray[$value->subject_id]["incidents"][date(
                                "M-Y",
                                strtotime($increport->occurance_datetime)
                            )][$increport->id] = "Nil";
                        }
                    }
                } else {
                    $reportsarray[$value->subject_id] = [];
                }

                array_push($subjectids, $value->subject_id);
            }
        }
        $sortedarray = [];
        try {
            array_multisort(array_column($reportsarray, 'name'), SORT_ASC, $reportsarray);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $incarray = [];
        $incarray["headermonths"] = [];
        //dd($reportsarray);
        foreach ($reportsarray as $key => $value) {
            $begin = new \DateTime($startday);
            $end = new \DateTime($endday);
            //$end = $end->modify('+1 month');
            $interval = \DateInterval::createFromDateString('1 month');
            $incid = $value["id"];

            $period = new \DatePeriod($begin, $interval, $end);
            $incarray[$incid]["name"] = $value["name"];
            $incarray[$incid]["response_time"] = $value["response_time"] / 60;
            foreach ($period as $dt) {
                $monthyearcomb = $dt->format("M-Y");
                $incarray["headermonths"][$monthyearcomb] = $monthyearcomb;
                if (isset($value["incidents"])) {
                    if (isset($value["incidents"][$monthyearcomb])) {
                        $happenedincidents = $value["incidents"][$monthyearcomb];
                        $noofincident = collect($happenedincidents)->filter(function ($item) {
                            return $item != "Nil";
                        })->count();
                        $incidentscount = (count($happenedincidents));

                        $totalclosetime = array_sum($happenedincidents);
                        if ($totalclosetime > 0 && $noofincident > 0) {
                            $averageclosetime = $totalclosetime / $noofincident;
                        } else {
                            $averageclosetime = 0;
                        }
                        // $incarray[$incid]["name"] = $averageclosetime;
                        $incarray[$incid]["months"][$monthyearcomb] = round($averageclosetime / 60, 2);
                    } else {
                        $incarray[$incid]["months"][$monthyearcomb] = 0;
                    }
                } else {
                    $incarray[$incid]["months"][$monthyearcomb] = 0;
                }
            }
        }
        return $incarray;
    }

    public function getCompliance($request)
    {
        $startday = date('Y-m', strtotime("-365 days")) . "-01";
        $endday = date('Y-m', strtotime("+1 days")) . "-30";
        $customers = $request->get("customer-id");
        $priority_id = $request->get("selected-priority");
        $response = CustomerIncidentPriority::with('priority')->where('priority_id', $priority_id)->where('customer_id', $customers)->first();
        if (empty($response)) {
            return [];
        }
        $response_range = [];
        if ($response->priority->value == 'Low') {
            $response_hrs = ($response->response_time * 2) / 60;
            $response_double = $response_hrs * 2;
            $response_hrs_double = $response_hrs * 2;
        } else {
            $response_hrs = $response->response_time / 60;
            $response_double = $response->response_time * 2;
            $response_hrs_double = $response_double / 60;
        }
        $response_range[1] = 'Under ' . $response_hrs . ' Hours';
        $response_range[2] = $response_hrs . ' to ' . $response_hrs_double . ' Hours';
        $response_range[3] = 'Over ' . $response_hrs_double . ' Hours';

        $subjectallocation = CustomerIncidentSubjectAllocation::with([
            "subject" => function ($q) {
                return $q->orderBy("subject", "desc");
            },

        ])->where("priority_id", $priority_id)->where('customer_id', $customers)->get();

        $result = [];
        $result['headers'] = $response_range;
        foreach ($subjectallocation as $each_subject) {
            foreach ($response_range as $key => $range) {
                $reports = 0;
                if ($key == 1) {
                    $start = 0;
                    $end = $response->response_time;
                } elseif ($key == 2) {
                    $start = $response->response_time;
                    $end = $response_double;
                } elseif ($key == 3) {
                    $start = $response_double;
                    $end = 9999;
                }
                $reports = IncidentReport::whereHas("incidentStatusLog", function ($query) use ($start, $end) {
                    return $query->where('incident_status_list_id', 3)->whereBetween('closed_time', [$start, $end]);
                })->where('priority_id', $priority_id)->where('subject_id', $each_subject->subject_id)->where('customer_id', $customers)->whereBetween("created_at", [$startday, $endday])->count();
                $result[$each_subject->subject->subject][$key] = ($reports == 0) ? '' : $reports;
            }
        }
        return $result;
    }

    public function getCustomerIncidentCompliance($customerId, $processDate)
    {
        //Get total incident count
        $totalIncidents = IncidentReport::whereHas('payperiod')
            ->where('customer_id', $customerId)
            ->whereDate('created_at', '<=', $processDate)
            ->count();

        //Get completed incidents
        $result = IncidentReport::whereHas('payperiod')
            ->where('customer_id', $customerId)
            ->whereHas('incidentStatusLog', function ($query) {
                return $query->whereNotNull('closed_time');
            })->whereDate('created_at', '<=', $processDate);

        $completedIncidents = $result->count();
        $percentage = 0;

        if ($totalIncidents != 0) {
            $percentage = ($completedIncidents / $totalIncidents) * 100;
        }

        return [
            "completed" => $completedIncidents,
            "total" => $totalIncidents,
            "percentage" => $percentage
        ];
    }

    public function getCustomerIncidentComplianceFullData($requests)
    {
        //Get total incident count
        $totalIncidents = IncidentReport::whereHas('payperiod')
            ->select(
                'customer_id',
                DB::raw('DATE(created_at) as createdAt'),
                DB::raw('count(*) as totalIncidents')
            )
            ->groupBy('customer_id', 'createdAt')
            ->get();

        //Get completed incidents
        $completedIncidents = IncidentReport::whereHas('payperiod')
            ->whereHas('incidentStatusLog', function ($query) {
                return $query->whereNotNull('closed_time');
            })
            ->orderBy('created_at')
            ->get();

        return [
            "completed" => $completedIncidents,
            "total" => $totalIncidents
        ];
    }

    public function getIncidentStatusLog($id)
    {
        $report = IncidentReport::with('incidentStatusLogWtihList', 'reporter', 'amendmentList', 'incidentAttachment')->find($id);
        if (count($report->incidentAttachment)) {
            $attchment_url = route('filedownload', ['id' => $report->incidentAttachment[0]->attachment_id, 'module' => 'incident']);
        } else {
            $attchment_url = null;
        }
        $incident_pdf_url = route('incident.attachement', ['incident_report_id' => $report->id]);
        $status_list = $this->getStatusList();
        $customer = $this->customerRepository->getCustomerWithMangers($report->customer_id);
        return view('supervisorpanel::incident-view', compact('report', 'status_list', 'customer', 'attchment_url', 'incident_pdf_url'));
    }

    public function incidentStatusList($id)
    {
        return $this->incident_status_log_model->where('incident_report_id', $id)->where('amendment', 0)->with('incidentStatusList', 'user', 'incidentSuggestedStatusList')->orderBy('created_at', 'desc')->get();
    }


    /**
     * Get Incident list
     * @param Request type=1 for user incident and type=0 for all incident
     */
    public function getIncidentUser($id, $request)
    {

        //     $notClosedIncident= IncidentReport::select('incident_reports.*')
        //     ->join('incident_status_logs', 'incident_reports.id', 'incident_status_logs.incident_report_id')
        //     ->whereIn('incident_status_logs.incident_status_list_id', [1,2])
        //     ->where('incident_status_logs.id', function ($query) {
        //         $query->select('id')
        //         ->from('incident_status_logs')
        //         ->whereColumn('incident_report_id', 'incident_reports.id')
        //         ->latest()
        //         ->limit(1);
        // })->get()->pluck('id')->toArray();
        return IncidentReport::select('id', 'incident_report_id', 'description', 'customer_id', 'title', 'subject_id', 'created_at', 'updated_at', 'deleted_at', 'created_by')
            ->with(array('incident_report_subject' => function ($query) {
                $query->select('id', 'subject', 'subject_short_name');
            }))
            ->with(array('reporter' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }))
            ->with(array('latestIncidentStatusLogWtihList' => function ($query) {
                $query->select('id', 'notes', 'amendment', 'incident_report_id', 'incident_status_list_id', 'created_at', 'updated_at', 'deleted_at');
            }))
            ->with(array('incidentSuggestedStatusLogWtihList' => function ($query) {
                $query->select('id', 'notes', 'amendment', 'incident_report_id', 'suggested_incident_status_list_id', 'created_at', 'updated_at', 'deleted_at', 'created_by');
            }))
            ->with(array('incidentSuggestedStatusLogWtihList.user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }))
            ->when(($request->type == 1), function ($query) use ($id) {
                $query->where('created_by', $id);
            })
            ->where('customer_id', $request->customerId)
            // ->when(($request->type==0), function ($query) use ($id) {
            ->where('created_at', '>=', Carbon::now()->subMonths(3)->toDateTimeString())
            //})
            // ->whereIn('id', $notClosedIncident)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getIRStatus()
    {
        return $this->getStatusList();
    }


    /**
     * Store Incident amendment
     * @param reportId: 1,notes: notes,taskStatus: 1
     */
    public function submitIncidentAmendment($request)
    {

        $incidentStatusRecentLog = IncidentStatusLog::where('incident_report_id', $request->reportId)->where('amendment', 0)->orderBy('created_at', 'desc')->first();
        $amendment = 1;
        $this->storeIncidentReportLog($request->reportId, $incidentStatusRecentLog->incident_status_list_id, $request->notes, Auth::user()->id, Auth::user()->id, null, $request->taskStatus, $amendment);
        $incidentStatusRecentLog->update(['suggested_incident_status_list_id' => $request->taskStatus]);
        $pdf = $this->createPDFIncidentAmendment($request);
        return $this->sendMail($request);
        //return $pdf;
    }

    public function createPDFIncidentAmendment($request)
    {
        $data['incident_amendment'] = IncidentStatusLog::with('user')->where('incident_report_id', $request->reportId)
            //->where('amendment', 1)
            ->orderBy('created_at', 'desc')->get();
        $incident_report = IncidentReport::with('incidentAttachment.attachment', 'latestIncidentStatusLogWtihList')->find($request->reportId);
        $customer_details = $this->customerRepository->getSingleCustomer((int) ($incident_report->customer_id));
        $incidentReport['title'] = $incident_report->title;
        $incidentReport['time_of_day'] = $incident_report->time_of_day;
        $priority = $this->customerIncidentSubjectAllocationRepository->getPriorityId($incident_report->subject_id, $incident_report->customer_id);

        if (!empty($priority)) {
            $incidentReport['priority_id'] = $priority->priority_id;
        } else {
            $incidentReport['priority_id'] = $incident_report->priority_id;
        }
        $data['subject_id'] = (int) ($incident_report->subject_id);
        $data['priority_id'] = $incidentReport['priority_id'];
        $data['year'] = Carbon::parse($incident_report->occurance_datetime)->format('Y');
        $data['payperiod_id'] =  (int) ($incident_report->payperiod_id);
        $data['month'] = date("F", mktime(0, 0, 0, Carbon::parse($incident_report->occurance_datetime)->format('m'), 1));
        $img = $incident_report->incidentAttachment;
        $incident_report->date = Carbon::parse($incident_report->occurance_datetime)->format('d');
        $incident_report->time = Carbon::parse($incident_report->occurance_datetime)->format('H:i');
        $customer = $this->customerRepository->getCustomerWithMangers($incident_report->customer_id);
        $incident_report->area_manager = isset($customer['areamanager']['full_name']) ? $customer['areamanager']['full_name'] : "--";
        $incident_report->supervisor = isset($customer['supervisor']['full_name']) ? $customer['supervisor']['full_name'] : "--";
        // $incident_report->fullname=Auth::user()->full_name;

        $incident_report->fullname = $incident_report->reporter->full_name;
        $incident_report->details = $incident_report->incident_description;
        $attachment_id_array = array();
        if ($img != null) {
            foreach ($img as $key => $incidentImage) {
                if ($incidentImage->attachment != "") {
                    $attachment_id_array[$key]['name'] = $incidentImage->attachment->hash_name;
                }
            }
        }

        $data['attachment_id_array'] = $attachment_id_array;
        $data['customer_id'] = $incident_report->customer_id;
        $data['payperiod_id'] = $incident_report->payperiod_id;
        $incidentReportAttachmentFilename = $this->pdfGenerate($incident_report, $customer_details, $data);
        IncidentReport::where('id', $request->reportId)->update(['attachment' => $incidentReportAttachmentFilename]);
        return true;
    }

    public function getIncidentReportByCustomerId($customer_id)
    {
        $incident_list = IncidentReport::with(
            [
                'customer',
                'priority',
                'latestStatus.incidentStatusList',
                'reporter',
                'incident_report_subject',
                'reporter.trashedEmployee'
            ]
        )->where('customer_id', $customer_id)
            ->whereDate('created_at', Carbon::today())->orderby('created_at', 'desc')->get();
        return $incident_list;
    }


    public function getIncidentReportByDate($date, $region_lookup_id = 0)
    {
        $incident_list = IncidentReport::whereDate('created_at', $date);

        $incident_list->when(($region_lookup_id != 0), function ($query) use ($region_lookup_id) {
            $query->whereHas('customer', function ($query) use ($region_lookup_id) {
                $query->where('region_lookup_id', $region_lookup_id);
            });
        });

        return  $incident_list->orderby('created_at', 'desc')->pluck('customer_id');
        //   $incident_list = IncidentReport::whereDate('created_at', $date)->orderby('created_at', 'desc')->pluck('customer_id');
        //    return  $incident_list;
    }
    /**
     * To send notificaion to area managers
     *
     * @param [type] $incident_report
     * @return void
     * @throws Exception
     */

    public function sendMail($request)
    {
        try {
            /*send mail - start */

            $incident_report = IncidentReport::with('incidentAttachment.attachment', 'reporter', 'customer')->find($request->reportId);
            $helper_variable = array(
                // '{receiverFullName}' => HelperService::sanitizeInput('User'),
                '{reporterFullName}' => HelperService::sanitizeInput($incident_report->reporter->full_name),
                '{client}' => HelperService::sanitizeInput($incident_report->customer->client_name),
                '{projectNumber}' => HelperService::sanitizeInput($incident_report->customer->project_number)

            );
            $emailResult = $this->mailQueueRepository
                ->prepareMailTemplate(
                    "incident_amendment_notification",
                    $incident_report->customer_id,
                    $helper_variable,
                    "Modules\Admin\Models\User"
                );
            $recipient_email = IncidentRecipient::where('customer_id', $incident_report->customer_id)->where('amendment_notification', 1)->distinct()->pluck('email')->toArray();
            if ($recipient_email) {
                $helper_variable = array(
                    '{receiverFullName}' => HelperService::sanitizeInput(''),
                    '{reporterFullName}' => HelperService::sanitizeInput($incident_report->reporter->full_name),
                    '{client}' => HelperService::sanitizeInput($incident_report->customer->client_name),
                    '{projectNumber}' => HelperService::sanitizeInput($incident_report->customer->project_number)

                );
                foreach ($recipient_email as $key => $email) {
                    $emailResult1 = $this->mailQueueRepository
                        ->prepareMailTemplate(
                            "incident_amendment_notification",
                            0,
                            $helper_variable,
                            "Modules\Admin\Models\User",
                            0,
                            0,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            $email
                        );
                }
            }
            return response()->json(['success' => true]);
            /*send mail - end */
        } catch (Exception $e) {
            Log::channel('customlog')->info('-----IncidentReport---send-notification-------- ' . $e->getMessage());
            throw $e;
        }
    }
}
