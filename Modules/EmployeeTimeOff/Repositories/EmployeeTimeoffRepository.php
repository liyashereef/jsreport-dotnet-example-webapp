<?php

namespace Modules\EmployeeTimeOff\Repositories;

use App\Repositories\AttachmentRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Mail;
use Modules\Admin\Models\TimeOffRequestTypeLookup;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\LeaveReasonRepository;
use Modules\Admin\Repositories\OperationCentreEmailRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\TimeOffCategoryLookupRepository;
use Modules\Admin\Repositories\TimeOffRequestTypeLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\EmployeeTimeOff\Emails\SendPendingApprovalEmail;
use Modules\EmployeeTimeOff\Models\EmployeeTimeOff;
use Modules\EmployeeTimeOff\Models\TimeoffAttachment;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffWorkflowRepository;

class EmployeeTimeoffRepository
{

    /**
     * The Model instance.
     * @var \Illuminate\Database\Eloquent\Model
     * @var \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffWorkflowRepository
     * @var \Modules\Admin\Repositories\UserRepository
     * @var \Modules\Admin\Repositories\CustomerRepository
     * @var \App\Repositories\AttachmentRepository
     * @var \Modules\Admin\Repositories\TimeOffRequestTypeLookupRepository
     * @var \Modules\Admin\Repositories\TimeOffCategoryLookupRepository
     * @var \Modules\Admin\Repositories\LeaveReasonRepository
     * @var \Modules\Admin\Repositories\PayPeriodRepository
     */
    protected $model, $employeeTimeOff, $attachmentRepository, $timeoffRequestLookup, $userRepository, $customerRepository, $timeOffRequestTypeLookupRepository, $timeOffCategoryLookupRepository, $leaveReasonRepository, $payPeriodRepository, $employeeAllocationRepository;

    /**
     * Create Repository instance.
     * @param  \Modules\EmployeeTimeOff\Models\EmployeeTimeOff $employeeTimeOff
     * @param  \Modules\Admin\Models\TimeOffRequestTypeLookup $timeoffRequestLookup
     * @param  \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffWorkflowRepository $employeeTimeoffWorkflowRepository
     * @param  \App\Repositories\AttachmentRepository $attachmentRepository
     * @param  \Modules\Admin\Repositories\UserRepository $userRepository
     * @param  \Modules\Admin\Repositories\CustomerRepository $customerRepository
     * @param  \Modules\Admin\Repositories\TimeOffRequestTypeLookupRepository $timeOffRequestTypeLookupRepository
     * @param  \Modules\Admin\Repositories\TimeOffCategoryLookupRepository $timeOffCategoryLookupRepository
     * @param  \Modules\Admin\Repositories\LeaveReasonRepository $leaveReasonRepository
     * @param  \Modules\Admin\Repositories\PayPeriodRepository $payPeriodRepository
     * @return void
     */
    public function __construct(EmployeeTimeoffWorkflowRepository $employeeTimeoffWorkflowRepository, EmployeeTimeOff $employeeTimeOff, AttachmentRepository $attachmentRepository, TimeOffRequestTypeLookup $timeoffRequestLookup, UserRepository $userRepository, CustomerRepository $customerRepository, TimeOffRequestTypeLookupRepository $timeOffRequestTypeLookupRepository, TimeOffCategoryLookupRepository $timeOffCategoryLookupRepository, LeaveReasonRepository $leaveReasonRepository, PayPeriodRepository $payPeriodRepository, OperationCentreEmailRepository $operationCentreEmailRepository, EmployeeAllocationRepository $employeeAllocationRepository)
    {
        $this->model = $employeeTimeOff;
        $this->userRepository = $userRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->timeoffRequestLookup = $timeoffRequestLookup;
        $this->employeeTimeoffWorkflowRepository = $employeeTimeoffWorkflowRepository;
        $this->customerRepository = $customerRepository;
        $this->timeOffRequestTypeLookupRepository = $timeOffRequestTypeLookupRepository;
        $this->timeOffCategoryLookupRepository = $timeOffCategoryLookupRepository;
        $this->leaveReasonRepository = $leaveReasonRepository;
        $this->payPeriodRepository = $payPeriodRepository;
        $this->operationCentreEmailRepository = $operationCentreEmailRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;

    }

    /**
     * Function to get the lookup list
     * @param  [integer] $id [employee_time_off id]
     * @return [array]     [return lookup list]
     */
    public function getLookupList($id = null)
    {
        $employee_list = $this->userRepository->getUserLookup(['guard', 'supervisor'], null, true, false, null, false);
        $employee_list = Arr::sort($employee_list);
        $project_list = $this->customerRepository->getList(ALL_CUSTOMER);
        $project_list = Arr::sort($project_list);
        $request_type = $this->timeOffRequestTypeLookupRepository->getLookupList();
        $logged_in_user = $this->userRepository->getFormatedUserDetails(\Auth::user()->id);
        $category = $this->timeOffCategoryLookupRepository->getLookupList();
        $pay_period = $this->payPeriodRepository->getPastCurrentFuturePayPeriod(PAST_FUTURE_PAYPERIOD, 1);
        $leave_reason = $this->leaveReasonRepository->getLookupList();
        $oc_email = $this->operationCentreEmailRepository->getLookupList();
        $leave_reason[0] = 'Others';
        $timestamp['date'] = date("Y-m-d");
        $timestamp['time'] = date("h:i:s a");
        $time_off_edit_details = null;
        $employee = null;
        $supervisor = null;
        $area_manager = null;
        $hr = null;
        if ($id != null) {
            $time_off_edit_details = $this->model->with('customer', 'employee', 'hr', 'leave_reason', 'category', 'attachments.attachment')->find($id);
            $timestamp['date'] = $time_off_edit_details->created_at->format('Y-m-d');
            $timestamp['time'] = $time_off_edit_details->created_at->format('h:i:s a');
            $employee = $this->userRepository->getFormatedUserDetails($time_off_edit_details->employee_id);
            $supervisor = $this->userRepository->getFormatedUserDetails($time_off_edit_details->supervisor_id);
            $area_manager = $this->userRepository->getFormatedUserDetails($time_off_edit_details->areamanager_id);
            $hr = $this->userRepository->getFormatedUserDetails($time_off_edit_details->hr_id);

        }
        return array('employee_list' => $employee_list, 'project_list' => $project_list, 'request_type' => $request_type, 'logged_in_user' => $logged_in_user, 'timestamp' => $timestamp, 'category' => $category, 'pay_period' => $pay_period, 'leave_reason' => $leave_reason, 'area_manager' => $area_manager, 'supervisor' => $supervisor, 'hr' => $hr, 'employee' => $employee, 'oc_email' => $oc_email, 'time_off_edit_details' => $time_off_edit_details);
    }

    /**
     * Function to store and update employee Time Off
     * @param  $request
     * @param  $module
     * @return array
     */
    public function store($request, $module)
    {
        $current_level = 0;
        if ($request->get('id') == null) {
            $approver = $this->employeeTimeoffWorkflowRepository->getRoleWorkflowLevel($request->employee_role_id, $current_level + 1);
            $approvalUserColumn = $this->employeeTimeoffWorkflowRepository->getRoleWorkflowApproverEmployee($approver->approver_role_id);
            $request->request->add(['pending_with_emp' => $request->get($approvalUserColumn), 'current_level' => $current_level, 'created_by' => $request->get('hr_id')]);
        } else {
            $request->request->add(['updated_by' => $request->get('hr_id')]);
        }
        $employeeTimeOffStore = $this->model->updateOrCreate(array('id' => $request->get('id')), $request->all());

        $employeeTimeOffId = $employeeTimeOffStore->id;
        $time_off_attachments = $request->time_off_attachment;
        if (!empty($time_off_attachments)) {
            foreach ($time_off_attachments as $key => $time_off_attachment) {
                $file = $this->attachmentRepository->saveAttachmentFile($module, $request, 'time_off_attachment.' . $key);
                $attachment_id = $file['file_id'];
                $storeAttachment = TimeOffAttachment::create(['timeoff_id' => $employeeTimeOffId, 'attachment_id' => $attachment_id, 'created_by' => $request->get('hr_id')]);
            }
        }
        return $employeeTimeOffStore;
    }

    /**
     * Employee leave list
     *
     * @param empty
     * @return array
     */
    function list() {
        $data = $employeeTimeOffList = $this->model->with(['employee.trashedUser', 'request_type', 'leave_reason', 'hr.trashedUser', 'latestLog.created_by.trashedUser', 'attachments.attachment', 'category']);

        if (\Auth::user()->can('view_all_timeoff')) {
            $data = $data;
        } else if (\Auth::user()->can('view_allocated_timeoff')) {
            $allocated_arr = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id)->toArray();
            $data = $data->whereIn('pending_with_emp', $allocated_arr);
        } else {
            $data = $data->where('pending_with_emp', \Auth::user()->id);
        }
        $data = $data->orderBy('id', 'desc')
            ->get();

        $data = $data->map(function ($item) {

            if ($item->attachments) {
                $inner_data = $item->attachments;
                $inner_data = $inner_data->map(function ($inner_data_item) {

                    $file_id = $inner_data_item->attachment->id;
                    $inner_data_item->at_details2 = route('filedownload', ['file_id' => $file_id, 'module' => 'employeeTimeoff']);

                    // $inner_data_item->at_details = $this->attachmentRepository->downloadDetails(null,$file_id,'employee_timeoff');
                    return $inner_data_item;
                });
                // $file_id = $item->attachments->attachment->id;
                // $item->at_details = $this->attachmentRepository->downloadDetails(null,$file_id,5);
            }
            return $item;
        });
        return $data;
    }

    /**
     * Employee leave list
     *
     * @param integer employee_id
     * @return array
     */
    public function listSingle($employee_id, $request_type = null)
    {

        $data = $employeeTimeOffList = $this->model->with(['employee.trashedUser', 'leave_reason', 'hr.trashedUser', 'latestLog.created_by.trashedUser', 'attachments.attachment', 'category', 'payperiod'])->where('employee_id', $employee_id)
            ->when($request_type != null, function ($data) use ($request_type) {
                $data->where('request_type_id', $request_type);
            })
            ->orderBy('created_at', 'desc')->get();

        $data = $data->map(function ($item) {

            if ($item->attachments) {
                $inner_data = $item->attachments;
                $inner_data = $inner_data->map(function ($inner_data_item) {

                    $file_id = $inner_data_item->attachment->id;
                    $inner_data_item->at_details2 = route('filedownload', ['file_id' => $file_id, 'module' => 'employeeTimeoff']);

                    // $inner_data_item->at_details = $this->attachmentRepository->downloadDetails(null,$file_id,'employee_timeoff');
                    return $inner_data_item;
                });
                // $file_id = $item->attachments->attachment->id;
                // $item->at_details = $this->attachmentRepository->downloadDetails(null,$file_id,5);
            }
            return $item;
        });
        return $data;
    }

    /**
     * Remove the specified Employee Leave from storage.
     *
     * @param  $ids
     * @return object
     */
    public function delete($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Function to prepare and give attachment path array
     * @param $request
     * @return array
     */
    public static function getAttachmentPathArr($request)
    {
        return array(config('globals.employee_timeoff'), $request->employee_id);
    }

    /**
     * Static function to return path as an array when file name is given
     * @param $file_id
     * @return array
     */
    public static function getAttachmentPathArrFromFile($file_id)
    {
        $attachment = TimeoffAttachment::with('employee_time_off')->where('attachment_id', $file_id)->first();
        if (isset($attachment)) {
            $employee_id = $attachment->employee_time_off->employee_id;
        }
        return array(config('globals.employee_timeoff'), $employee_id);
    }

    /**
     * Function to get all Timeoff data
     * @param $employee_id  array[optional]  Array of employee id
     * @param $customer_id array[optional]  Array of customer id
     * @param $grouping_param array[optional] Array with groupby parameters
     * @return array
     */
    public function getTimeOff(
        $employee_id = null,
        $customer_id = null,
        $grouping_param = ['employee_id', 'customer_id', 'request_type_id']) {

        $select_array = $grouping_param;

        array_push($select_array, \DB::raw('sum(days_requested) as days_requested, sum(days_approved) as days_approved,sum(days_rejected) as days_rejected,sum(days_remaining) as days_remaining'));

//        $ordered_result = $this->model->orderBy('created_at','desc');
        $query_result = $this->model->select($select_array)
            ->groupBy($grouping_param)
            ->with('employee.trashedUser', 'leave_reason', 'request_type', 'payperiod', 'customer');
//        ->orderBy('created_at','DESC');
        if (isset($employee_id) || (isset($customer_id))) {
            $query_result->wherehas('employee', function ($query_result) use ($employee_id) {
                $query_result->when($employee_id != null, function ($query_result) use ($employee_id) {
                    $query_result->whereIn('employee_id', $employee_id);
                }, function ($query_result) use ($employee_id) {
                    $query_result->where('employee_id');
                });

            });
            $query_result->orWhereHas('customer', function ($query_result) use ($customer_id) {
                $query_result->when($customer_id != null, function ($query_result) use ($customer_id) {
                    $query_result->whereIn('customer_id', $customer_id);
                }, function ($query_result) use ($customer_id) {
                    $query_result->where('customer_id');
                });
            });
        }
        $query_results = $query_result->get();
        $id_array = $result = array();
        foreach ($query_results as $key => $each_list) {
            $id_array[$key]['employee_id'] = $each_list['employee_id'];
            $id_array[$key]['customer_id'] = $each_list['customer_id'];
        }
        $unique_array = array_unique($id_array, SORT_REGULAR);
        foreach ($unique_array as $each_array) {
            $result[] = $query_results->where('employee_id', $each_array['employee_id'])->where('customer_id', $each_array['customer_id']);
        }

//        dd($result,array_reverse($result,true));
        return $this->prepareData(array_reverse($result, true));
//        return $this->prepareData($result);
    }

    /**
     * Function to prepare Timeoff data array
     * @param $data
     * @return array
     */
    public function prepareData($data)
    {

        $datatable_rows = array();
        foreach ($data as $key => $each_data) {
            $requesttype = $this->timeoffRequestLookup->orderBy('request_type', 'asc')->pluck('request_type', 'id')->toArray();
            $each_row1 = array();
            $each_row = array();
            $type_array = array();
            foreach ($each_data as $key => $each_employee) {
                $each_row["employee_id"] = $each_employee->employee_id;
                $each_row["employee_number"] = $each_employee->employee->employee_no;
                $each_row["project_number"] = $each_employee->customer->project_number;
                $each_row["client_name"] = $each_employee->customer->client_name;
                $each_row["employee_name"] = $each_employee->employee->trashedUser->first_name . ' ' . $each_employee->employee->trashedUser->last_name;
                $type = $each_employee->request_type->request_type ?? '';
                $each_row1[$key]["days_requested"] = $each_employee->days_requested ?? 0;
                $each_row1[$key]["days_approved"] = $each_employee->days_approved ?? 0;
                $each_row1[$key]["days_rejected"] = $each_employee->days_rejected ?? 0;
                $each_row1[$key]["days_remaining"] = $each_employee->days_remaining ?? 0;
                $each_row1[$key]["type"] = $type;
                $each_row['timeoff'][$type] = $each_row1[$key];
                $type_array[] = $type;
            }
            $result = array_diff($requesttype, $type_array);
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $each_row1[$key]["days_requested"] = 0;
                    $each_row1[$key]["days_approved"] = 0;
                    $each_row1[$key]["days_rejected"] = 0;
                    $each_row1[$key]["days_remaining"] = 0;
                    $each_row1[$key]["type"] = $value;
                    $each_row['timeoff'][$value] = $each_row1[$key];

                }

            }
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function timeoffInitialArray()
    {
        $requesttype = $this->timeoffRequestLookup->orderBy('request_type', 'asc')->pluck('request_type', 'id')->toArray();
        foreach ($requesttype as $key => $value) {
            $each_row1[$key]["days_requested"] = 0;
            $each_row1[$key]["days_approved"] = 0;
            $each_row1[$key]["days_rejected"] = 0;
            $each_row1[$key]["days_remaining"] = 0;
            $each_row1[$key]["type"] = $value;
            $each_row[$value] = $each_row1[$key];

        }
        return ($each_row);
    }

    /**
     * Function to update time off entry according to action
     * @param $id
     * @return array
     */
    public function getSingle($id)
    {
        return $this->model->find($id);
    }

    /**
     * Function to update time off entry according to action
     * @param $log
     * @return array
     */
    public function approveOrReject($log)
    {
        $employee_time_off = $this->getSingle($log['time_off_id']);
        $workflow = $this->employeeTimeoffWorkflowRepository->getRoleWorkflow($employee_time_off->employee_role_id);
        if ($workflow->count() <= 0) {
            return 'sda';
        } else {
            $employee_time_off->start_date = $log['start_date'];
            $employee_time_off->end_date = $log['end_date'];
            $employee_time_off->days_requested = $log['days_requested'];
            $employee_time_off->days_approved = $log['days_approved'];
            $employee_time_off->days_rejected = $log['days_rejected'];
            $employee_time_off->days_remaining = $log['days_remaining'];
            $employee_time_off->save();

        }
        if ($log['approved'] == 1) {
            //Get workflow to check if final level is reached
            $total_level = $workflow->count();

            $employee_time_off->current_level = $employee_time_off->current_level + 1;
            $employee_time_off->approved = null;
            $employee_time_off->save();
            if ($employee_time_off->current_level >= $total_level) {
                //The time off entry is approved by the final level
                $employee_time_off->approved = 1;
                $employee_time_off->approved_by = \Auth::id();

                //$employee_time_off->status = 'Approved by ';
                $employee_time_off->save();

            } else {
                //Has not reached the final level

                $approver = $this->employeeTimeoffWorkflowRepository->getRoleWorkflowLevel($employee_time_off->employee_role_id, $employee_time_off->current_level + 1);
                $approvalUserColumn = $this->employeeTimeoffWorkflowRepository->getRoleWorkflowApproverEmployee($approver->approver_role_id);
                $employee_time_off->pending_with_emp = $employee_time_off->$approvalUserColumn;
                $employee_time_off->save();
                $next_level = $employee_time_off->current_level + 1;
                if ($approver->email_notification == 1) {
                    $approver_email = User::find($employee_time_off->pending_with_emp)->email;
                    $this->sendNotification($employee_time_off, $approver_email, 'Mail.create');
                }
                return $current_workflow_level = $workflow->where('level', $next_level);

            }

        } else {
            //Save rejected status
            $employee_time_off->approved = 0;
            $employee_time_off->save();
            return 1;
        }
    }

    /**
     * To send mail notification
     *
     * @param [type] $time_off
     * @param [type] $to
     * @param [type] $cc
     * @param [type] $template
     * @return void
     */
    public function sendNotification($time_off, $to, $template, $cc = null)
    {
        $mail = Mail::to($to);
        if ($cc != null) {
            $mail->cc($cc);
        }
        $mail->queue(new SendPendingApprovalEmail($time_off, $template));
    }

}
