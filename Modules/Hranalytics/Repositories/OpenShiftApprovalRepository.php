<?php

namespace Modules\Hranalytics\Repositories;

use App\Services\HelperService;
use DB;
use Mail;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\StatusLogLookupRepository;
use Modules\Hranalytics\Mail\OpenShiftApproval;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Models\ScheduleCustomerRequirement;
use Modules\Hranalytics\Repositories\ScheduleCustomerMultipleFillShiftsRepository;
use Modules\Timetracker\Models\CandidateOpenshiftApplication;

class OpenShiftApprovalRepository
{

    protected $candidateOpenshiftApplication, $statusLogLookupRepository, $requirementModel, $eventLogModel;

    /**
     * Create a new CandidateOpenshiftApplication instance.
     *
     * @param  \Modules\Timetracker\Models\CandidateOpenshiftApplication $candidateOpenshiftApplication
     */
    public function __construct(CandidateOpenshiftApplication $candidateOpenshiftApplication, StatusLogLookupRepository $statusLogLookupRepository, ScheduleCustomerMultipleFillShiftsRepository $scheduleCustomerMultipleFillShiftsRepository, ScheduleCustomerRequirement $requirementModel, EventLogEntry $eventLogModel)
    {
        $this->candidateOpenshiftApplication = $candidateOpenshiftApplication;
        $this->helperService = new HelperService();
        $this->statusLogLookupRepository = $statusLogLookupRepository;
        $this->scheduleCustomerMultipleFillShiftsRepository = $scheduleCustomerMultipleFillShiftsRepository;
        $this->requirementModel = $requirementModel;
        $this->eventLogModel = $eventLogModel;
    }

    /**
     * Get single job
     *
     * @param $id
     * @return object
     */
    public function getAll($checked, $client_id = null)
    {
        $data = $this->requirementModel->whereHas('openshifts')
            ->with([
                'customer' => function ($query) {
                    $query->select();
                },
                'openshifts' => function ($query) {
                    $query->orderBy('approved_by', 'desc');
                },

            ])->when($client_id != null && $client_id != 0, function ($q) use ($client_id) {
                return $q->whereHas('customer', function ($query) use ($client_id) {
                    $query->where('id', '=', $client_id);
                });
            });

        $query_result = $data->select()
            ->addselect(DB::raw('(select count(id) from `candidate_openshift_applications` where
        shiftid=schedule_customer_requirements.id and readflag=0
        and (select parent_id from schedule_customer_multiple_fill_shifts where id=candidate_openshift_applications.multifillid)=0
        and deleted_at is null ) as unread'))
            ->addselect(DB::raw('(select count(id) from `schedule_customer_multiple_fill_shifts` where schedule_customer_requirement_id=schedule_customer_requirements.id ) as multifill'))->get();
        return $this->prepareDataArray($query_result, $checked);
    }

    public function prepareDataArray($query_result, $checked)
    {
        $checked_ids = explode(',', $checked);
        $datatable_rows = array();
        foreach ($query_result as $key => $each_record) {
            $statusGroup = $this->eventLogModel
                ->select('multiple_shift_id', DB::raw('MAX(id) as latest_id'))
                ->where('schedule_customer_requirement_id', $each_record->id)
                ->groupBy('multiple_shift_id')
                ->get();
            $statsCount = count($statusGroup);
            $acceptedCount = 0;
            if ($statsCount > 0) {
                foreach ($statusGroup as $statusEntry) {
                    $eventObj = $this->eventLogModel->find($statusEntry->latest_id);
                    if ($eventObj->status == 1) {
                        $acceptedCount++;
                    }
                }
            }

            $status = 'Open';
            if ($each_record->no_of_shifts == null) {
                if ($acceptedCount != 0) {
                    $status = 'Closed';
                    $remainingShifts = 0;
                } else {
                    $remainingShifts = 1;
                }
            } else {
                if ($each_record->no_of_shifts == $acceptedCount) {
                    $status = 'Closed';
                    $remainingShifts = 0;
                } else {
                    $remainingShifts = ($each_record->no_of_shifts - $acceptedCount);
                }
            }

            $each_row["status"] = $status;
            $each_row["remaining_shifts"] = $remainingShifts;
            $each_row["id"] = $each_record->id;
            $each_row["project_number"] = $each_record->customer ? $each_record->customer->project_number : '';
            $each_row["client_name"] = $each_record->customer ? $each_record->customer->client_name : '';
            $each_row["site_rate"] = '$' . $each_record->site_rate;
            $each_row["start_date"] = 'Start : ' . $each_record->start_date . ',  End : '
                . $each_record->end_date;
            $each_row["no_of_shifts"] = isset($each_record->no_of_shifts) ? $each_record->no_of_shifts : 1;
            $each_row["notes"] = $each_record->notes;
            $each_row["unread"] = $each_record->unread;
            array_push($datatable_rows, $each_row);
        }
        $filterArray = array_filter($datatable_rows, function ($datatable_each_row) use ($checked_ids) {
            if ($this->in_array_all([1, 2], $checked_ids)) {
                return $datatable_each_row;
            } else if ($this->in_array_any([2], $checked_ids)) {
                return ($datatable_each_row['status'] == 'Closed');
            } else if ($this->in_array_any([1], $checked_ids)) {
                return ($datatable_each_row['status'] == 'Open');
            } else {
                return ($datatable_each_row['status'] !== 'Open' && $datatable_each_row['status'] !== 'Closed');
            }
        });
        return $filterArray;
    }
    public function in_array_all($needles, $haystack)
    {
        return empty(array_diff($needles, $haystack));
    }
    public function in_array_any($needles, $haystack)
    {
        return !empty(array_intersect($needles, $haystack));
    }

    public function get($id)
    {
        return $this->candidateOpenshiftApplication->find($id);
    }

    public function changeStatus($id)
    {
        return $this->candidateOpenshiftApplication->where('id', $id)->update(array('status' => 1, 'approved_by' => \Auth::user()->id));
    }
    public function sendMail($requirement_id, $shift_id, $user_id)
    {
        $email_ids = User::where('id', $user_id)->select('email')->first();
        $mail = Mail::to($email_ids);
        $shift_id = ($shift_id == 0) ? null : $shift_id;
        $openshift_details = $this->candidateOpenshiftApplication->with('customer', 'user', 'requirement')->where('multifillid', $shift_id)->where('shiftid', $requirement_id)->where('userid', $user_id)->first();
        $mail->queue(new OpenShiftApproval($openshift_details, 'mail.openshift.openshift-approval'));
        return;
    }

    public function getOpenshifts($id)
    {
        $arr = array();
        $data = $this->candidateOpenshiftApplication
            ->select('*')
            ->with('approvedUser', 'user.employee', 'scheduleCustomerMultipleFillShift', 'requirement')
            ->where('shiftid', $id)
            ->orderBy('created_at', 'asc')
            ->get();
        CandidateOpenshiftApplication::where("shiftid", $id)->update(['readflag' => 1]);

        $datatable_rows = array();
        foreach ($data as $key => $each_record) {
            if ($each_record->scheduleCustomerMultipleFillShift != null) {
                $parentIds = ScheduleCustomerMultipleFillShifts::where('parent_id', $each_record->multifillid)->pluck('id')->toArray();
                array_push($parentIds, $each_record->multifillid);
                $already_contacted_array = $this->eventLogModel
                    ->whereIn('multiple_shift_id', $parentIds)
                    ->pluck('user_id');

                $assignedEmplyeeId = $each_record->scheduleCustomerMultipleFillShift->assigned_employee_id;
                $assigned = $each_record->scheduleCustomerMultipleFillShift->assigned;
                $parentId = $each_record->scheduleCustomerMultipleFillShift->parent_id;
                $noOfPosition = $each_record->scheduleCustomerMultipleFillShift->no_of_position;
                $each_row["approved_by"] = (null != $each_record->approvedUser) ? $each_record->approvedUser : null;
                $each_row["approved"] = (null != $each_record->approvedUser) ? $each_record->approvedUser->full_name : '--';
                $parentchilarrays = [];
                $approveduser = null;

                if ($parentId < 1) {
                    array_push($parentchilarrays, $each_record->multifillid);
                    if ($each_record->scheduleCustomerMultipleFillShift->no_of_position > 1) {

                        $childrows = ScheduleCustomerMultipleFillShifts::where("parent_id", $each_record->multifillid)->get();
                        foreach ($childrows as $childrow) {
                            array_push($parentchilarrays, $childrow->id);
                        }
                    }

                    $eventLogStatus = EventLogEntry::whereIn('multiple_shift_id', $parentchilarrays)
                        ->where('user_id', $each_record->userid)
                        ->latest()
                        ->first();

                    if (($eventLogStatus != null) && ($eventLogStatus->status != 1)) {
                        $each_row["approved_by"] = null;
                        $each_row["approved"] = '--';
                    } else {
                        $approveduser = ScheduleCustomerMultipleFillShifts::whereIn("id", $parentchilarrays)
                            ->where("assigned_employee_id", $each_record->userid)
                            ->first();

                        if ($approveduser) {
                            $assigneduser = $approveduser->assigned_by;
                            $user = User::find($assigneduser);
                            $each_row["approved_by"] = $user->getFullNameAttribute();
                            $each_row["approved"] = $user->getFullNameAttribute();
                        }
                    }

                    $assignedEmployees = ScheduleCustomerMultipleFillShifts::where('id', $each_record->multifillid)
                        ->orWhere('parent_id', $each_record->multifillid)
                        ->get()->pluck('assigned_employee_id')->toArray();

                    $countShifts = ScheduleCustomerMultipleFillShifts::where('id', $each_record->multifillid)
                        ->whereNull('assigned_employee_id')
                        ->count();
                    $countShifts += ScheduleCustomerMultipleFillShifts::where('parent_id', $each_record->multifillid)
                        ->whereNull('assigned_employee_id')
                        ->count();

                    $statusGroup = EventLogEntry::select('multiple_shift_id', DB::raw('MAX(id) as latest_id'))
                        ->whereIn('multiple_shift_id', $parentchilarrays)
                        ->groupBy('multiple_shift_id')
                        ->get();

                    $eventLogEntriesCount = 0;
                    if (!empty($statusGroup)) {
                        foreach ($statusGroup as $statusEntry) {
                            $eventObj = EventLogEntry::find($statusEntry->latest_id);
                            if ($eventObj->status == 1) {
                                $eventLogEntriesCount++;
                            }
                        }
                    }

                    $each_row["editable"] = false;
                    if (($countShifts > 0) || ($eventLogEntriesCount < count($parentchilarrays)) || in_array($each_record->userid, $assignedEmployees)) {
                        $each_row["editable"] = true;

                        $userDetails = ScheduleCustomerMultipleFillShifts::where('id', $each_record->multifillid)->first();
                        if ($userDetails && $userDetails->assigned_by > 0) {
                            $each_row["approved_by"] = $userDetails->assigned_by;
                        }
                    }

                    $each_row["id"] = $each_record->id;
                    $each_row["multifillemployeeid"] = null;
                    $each_row["startdate"] = $each_record->startdate;
                    $each_row["enddate"] = $each_record->enddate;
                    $each_row["starttime"] = $each_record->starttime;
                    $each_row["endtime"] = $each_record->endtime;
                    $each_row["sitenote"] = isset($each_record->sitenotes) ? $each_record->sitenotes : '--';
                    $each_row["address"] = isset($each_record->user->employee) ? (isset($each_record->user->employee->employee_address) ? $each_record->user->employee->employee_address . ',  ' : '') . (isset($each_record->user->employee->employee_city) ? $each_record->user->employee->employee_city . ', ' : '') . $each_record->user->employee->employee_postal_code : '--';

                    if ($noOfPosition > 1) {
                        $each_row["actions"] = '<a onclick="assignToEmployee(' . $id . ',' . $each_record->customerid . ',' . $each_record->multifillid . ',' . $each_record->userid . ',' . $already_contacted_array . ')" title="Event Log"  href="javascript:;"  class="fa fa-calendar" id="event-log"></a>&nbsp;';
                    } else {
                        $each_row["actions"] = '<a onclick="gatewayCheckEventLog(' . $id . ',' . $each_record->customerid . ',' . (isset($each_record->multifillid) ? $each_record->multifillid : 0) . ',' . $each_record->userid . ',' . $already_contacted_array . ')" title="Event Log"  href="javascript:;"  class="fa fa-calendar" id="event-log"></a>&nbsp;';
                    }
                    $each_row["employee"] = $each_record->user->full_name;
                    $each_row["employee_id"] = $each_record->user->employee ? $each_record->user->employee->employee_no : '';
                    $each_row["phone"] = $each_record->user->employee ? $each_record->user->employee->phone : '';
                    $each_row["email"] = $each_record->user ? $each_record->user->email : '';
                    $each_row["role"] = $each_record->user->roles ? $each_record->user->roles[0]->name : '';
                    $each_row["city"] = $each_record->user->employee ? $each_record->user->employee->employee_city : '';
                    $each_row["created_at"] = date("Y-m-d", strtotime($each_record->created_at));
                    $each_row["created_time"] = date("h:i A", strtotime($each_record->created_at));
                    array_push($datatable_rows, $each_row);
                }
            } else {
                $each_row["approved_by"] = (null != $each_record->requirement->event_log_entry_latest && ($each_record->requirement->event_log_entry_latest->status == 1) && ($each_record->requirement->event_log_entry_latest->user_id == $each_record->userid)) ? $each_record->requirement->event_log_entry_latest->dutyofficer : null;
                $each_row["approved"] = (null != $each_record->requirement->event_log_entry_latest && ($each_record->requirement->event_log_entry_latest->status == 1) && ($each_record->requirement->event_log_entry_latest->user_id == $each_record->userid)) ? $each_record->requirement->event_log_entry_latest->dutyofficer->full_name : '--';
                $each_row["editable"] = true;
                $each_row["id"] = $each_record->id;
                $each_row["multifillemployeeid"] = null;
                $each_row["startdate"] = $each_record->startdate;
                $each_row["enddate"] = $each_record->enddate;
                $each_row["starttime"] = $each_record->starttime;
                $each_row["endtime"] = $each_record->endtime;
                $each_row["sitenote"] = isset($each_record->sitenotes) ? $each_record->sitenotes : '--';
                $each_row["address"] = isset($each_record->user->employee) ? (isset($each_record->user->employee->employee_address) ? $each_record->user->employee->employee_address . ',  ' : '') . (isset($each_record->user->employee->employee_city) ? $each_record->user->employee->employee_city . ', ' : '') . $each_record->user->employee->employee_postal_code : '--';
                $each_row["actions"] = '<a onclick="assignToEmployee(' . $id . ',' . $each_record->customerid . ',' . (isset($each_record->multifillid) ? $each_record->multifillid : 0) . ',' . $each_record->userid . ')" title="Event Log"  href="javascript:;"  class="fa fa-calendar" id="event-log"></a>&nbsp;';
                $each_row["employee"] = $each_record->user->full_name;
                $each_row["employee_id"] = $each_record->user->employee ? $each_record->user->employee->employee_no : '';
                $each_row["phone"] = $each_record->user->employee ? $each_record->user->employee->phone : '';
                $each_row["email"] = $each_record->user ? $each_record->user->email : '';
                $each_row["role"] = $each_record->user->roles ? $each_record->user->roles[0]->name : '';
                $each_row["city"] = $each_record->user->employee ? $each_record->user->employee->employee_city : '';
                $each_row["created_at"] = date("Y-m-d", strtotime($each_record->created_at));
                $each_row["created_time"] = date("h:i A", strtotime($each_record->created_at));
                array_push($datatable_rows, $each_row);
            }
        }
        return $datatable_rows;
    }

    public function getRequirementDetails($id)
    {
        return $this->requirementModel->with([
            'customer' => function ($query) {
                $query->select();
            },
            'openshifts' => function ($query) {
                $query->orderBy('approved_by', 'desc');
            }, 'openshifts.user.employee' => function ($query) {
                $query->select();
            },
        ])->where('id', $id)->first();
    }

    public function getemployeeArray($employee_list)
    {
        $list_data = array();
        $employee_arr = array();
        foreach ($employee_list as $key => $data) {
            $value['rating'] = round($data->employee_rating, 2);

            if (in_array($data->user_id, $employee_arr)) {
                continue;
            }
            $employee_arr[] = $data->user_id;
            $value['employee_id'] = $data->user_id;
            $value['employee_no'] = $data->employee_no;
            $value['first_name'] = $data->user->first_name;
            $value['last_name'] = $data->user->last_name;
            $value['full_name'] = $data->user->first_name . ' ' . $data->user->last_name;
            $value['address'] = $data->employee_address;
            $value['city'] = $data->employee_city;
            $value['postal_code'] = $data->employee_postal_code;
            $value['phone_number'] = $data->phone;
            $value['phone_ext'] = $data->phone_ext;
            $value['work_email'] = $data->employee_work_email;
            $value['latitude'] = $data->geo_location_lat;
            $value['longitude'] = $data->geo_location_long;
            $value['date_of_birth'] = $data->employee_dob;
            $value['veteran_status'] = $data->employee_vet_status;
            $value['current_wage'] = $data->current_project_wage;
            $value['position'] = isset($data->employeePosition) ? $data->employeePosition->position : '--';
            $value["security_clearance"] = !($data->user->securityClearanceUser)->isEmpty() ? $data->user->securityClearanceUser->pluck('securityClearanceLookups.security_clearance')->toArray() : '--';
            $value["clearance_expiry"] = !($data->user->securityClearanceUser)->isEmpty() ? $data->user->securityClearanceUser->pluck('valid_until')->toArray() : '--';
            $value["project_number"] = isset($data->user->allocation->last()->customer) ? $data->user->allocation->last()->customer->project_number : '--';
            $value["project_name"] = isset($data->user->allocation->last()->customer) ? $data->user->allocation->last()->customer->client_name : '--';
            $value["start_date"] = $data->employee_doj;
            $today = date('Y-m-d');
            $value["length_of_service"] = isset($data->employee_doj) ? $this->dateDifference($today, $value["start_date"]) : '--';
            $value["age"] = isset($data->employee_dob) ? $this->dateDifference($today, $value["date_of_birth"]) : '--';
            array_push($list_data, $value);
        }
        return $list_data;
    }

    /**
     * Function to calculate date difference in years
     *
     * @param $id
     * @return value
     */
    public function dateDifference($date_1, $date_2, $differenceFormat = '%y')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);
        $interval = date_diff($datetime1, $datetime2);
        return $interval->format($differenceFormat);
    }
}
