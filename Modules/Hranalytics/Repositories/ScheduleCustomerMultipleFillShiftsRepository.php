<?php

namespace Modules\Hranalytics\Repositories;

use App\Repositories\MailQueueRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\ScheduleAssignmentTypeLookup;
use Modules\Admin\Models\ShiftTiming;
use Modules\Admin\Models\StcThresholdSetting;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Models\ScheduleCustomerRequirement;
use Modules\Timetracker\Models\CandidateOpenshiftApplication;

class ScheduleCustomerMultipleFillShiftsRepository
{
    /**
     * The Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model, $openshiftmodel, $mailQueueRepository;

    /**
     * Create a new CandidateScheduleRepository instance.
     *
     * @param  \App\Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts $model
     */
    public function __construct(ScheduleCustomerMultipleFillShifts $model, CandidateOpenshiftApplication $openshiftmodel, MailQueueRepository $mailQueueRepository)
    {
        $this->model = $model;
        $this->openshiftmodel = $openshiftmodel;
        $this->mailQueueRepository = $mailQueueRepository;
    }

    /**
     * Function to store Schedule Multiple Fill Shifts
     * @param  $request
     * @return object
     */
    public function store($request, $scheduleCustomerRequirement)
    {
        try {
            \DB::beginTransaction();
            $shift_timings = $request->get('shift_timing_id');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $shift_length = $request->get('no_of_shifts');
            $i = 0;
            $range = $this->createDateRange($start_date, $end_date);
            if (!empty($range)) {
                foreach ($range as $each_date) {
                    foreach ($shift_timings as $each_shift_timing) {
                        if ($i == $shift_length) {
                            break 2;
                        }
                        $i++;

                        $actualDate = Carbon::createFromFormat('Y-m-d', $each_date['date']);
                        $shiftFrom = $request->get('shift_from_' . $each_shift_timing . '_' . $actualDate->format('d_m_Y'));
                        $shiftTo = $request->get('shift_to_' . $each_shift_timing . '_' . $actualDate->format('d_m_Y'));
                        $noOfPositions = (int) $request->get('no_of_positions_' . $each_shift_timing . '_' . $actualDate->format('d_m_Y'));
                        $shift_timing_formatted_arr = $this->getShiftTimeInSqlTimestamp($each_date['date'], $shiftFrom, $shiftTo);

                        if ($noOfPositions > 1) {
                            $parentId = 0;
                            for ($x = 0; $x < $noOfPositions; $x++) {
                                $multipleFillShift = $this->model->create([
                                    'schedule_customer_requirement_id' => $scheduleCustomerRequirement->id,
                                    'shift_timing_id' => $each_shift_timing,
                                    'shift_from' => $shift_timing_formatted_arr['from'],
                                    'shift_to' => $shift_timing_formatted_arr['to'],
                                    'assigned' => 0,
                                    'no_of_position' => $noOfPositions,
                                    'parent_id' => $parentId,
                                ]);

                                if ($x == 0) {
                                    $parentId = $multipleFillShift->id;
                                }
                            }
                        } else {
                            $multipleFillShift = $this->model->create([
                                'schedule_customer_requirement_id' => $scheduleCustomerRequirement->id,
                                'shift_timing_id' => $each_shift_timing,
                                'shift_from' => $shift_timing_formatted_arr['from'],
                                'shift_to' => $shift_timing_formatted_arr['to'],
                                'assigned' => 0,
                                'no_of_position' => 1,
                                'parent_id' => 0,
                            ]);
                        }
                    }
                }
            }
            \DB::commit();
            return $multipleFillShift;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Function to get time difference
     * @return [array] [From and to date]
     */
    public function getShiftTimeInSqlTimestamp($shift_date, $shift_from, $shift_to)
    {
        $shift_from = Carbon::parse($shift_date . ' ' . $shift_from);
        $shift_to = Carbon::parse($shift_date . ' ' . $shift_to);
        if ($shift_to->lessThan($shift_from)) {
            $shift_to = $shift_to->addDay(1);
        }
        $shift_timing['from'] = $shift_from->toDateTimeString();
        $shift_timing['to'] = $shift_to->toDateTimeString();
        return $shift_timing;
    }

    /**
     * Get single Schedule Multiple Fill Shifts
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->with(['latestEventLog'])->find($id);
    }

    /**
     * Get single Schedule Multiple Fill Shifts
     *
     * @param $id
     * @return object
     */
    public function getRequirementData($data)
    {

        $shift = array();
        $days_array = config("globals.array_shift_day");
        foreach ($data->scheduleCustomerAllShifts as $key => $shifts) {
            $requirement = ScheduleCustomerRequirement::find($shifts->schedule_customer_requirement_id);
            $shift[$key]['shift'] = $shifts->shiftTiming->id;
            $shift[$key]['name'] = $shifts->shiftTiming->shift_name;
            $shift[$key]['shift_id'] = $shifts->id;
            $shift[$key]['full_shift_from'] = $shifts->shift_from;
            $shift[$key]['full_shift_to'] = $shifts->shift_to;
            $datetime_from = explode(" ", $shifts->shift_from);
            $specificDate = strtotime($datetime_from[0]);
            $day_key = array_search(date('l', $specificDate), $days_array);
            $shift[$key]['day'] = $day_key;
            $shift[$key]['shift_from'] = $datetime_from[1];
            $shift[$key]['start_date'] = Carbon::parse($datetime_from[0])->format('M d, Y');
            $datetime_to = explode(" ", $shifts->shift_to);
            $shift[$key]['shift_to'] = $datetime_to[1];
            $shift[$key]['end_date'] = Carbon::parse($datetime_to[0])->format('M d, Y');
            $startTime = Carbon::parse($shifts->shift_from);
            $finishTime = (Carbon::parse($shifts->shift_to));
            $minutes_duration = $finishTime->diffInMinutes($startTime);
            $shift[$key]['hourdiff'] = date('G:i', mktime(0, $minutes_duration));
            $shift[$key]['shift_timing_id'] = $shifts->shift_timing_id;

            $nonAssignedChildFound = ScheduleCustomerMultipleFillShifts::where('parent_id', $shifts->id)->where('assigned_employee_id', null)->count();
            if ($nonAssignedChildFound > 0 && ($shifts->assigned_employee_id == null)) {
                $shift[$key]['assigned'] = '-';
            } else {
                $shift[$key]['assigned'] = isset($shifts->trashed_user) ? $shifts->trashed_user->full_name : 'Not Set';
            }

            $shift[$key]['can_unassign'] = true;
            $shift[$key]['parent_id'] = intval($shifts->parent_id);
            $assignedParentFound = ScheduleCustomerMultipleFillShifts::where('id', $shifts->parent_id)->where('assigned_employee_id', '!=', null)->count();
            if ($assignedParentFound > 0) {
                $shift[$key]['can_unassign'] = false;
            }

            $shift[$key]['site_rate'] = ($shifts->latestEventLog && $shifts->latestEventLog->status == 1) ? '$' . number_format($shifts->latestEventLog->accepted_rate, 2) : '$' . number_format($data->site_rate, 2);
            $shift[$key]['security_clearance_level'] = $data->security_clearance_level;
            $shift[$key]['security_clearance_level_name'] = isset($data->security_clearance) ? $data->security_clearance->security_clearance : '--';
            $shift[$key]['length_of_shift'] = isset($data->length_of_shift) ? $data->length_of_shift : '--';
            $shift[$key]['parent_id'] = $shifts->parent_id;
            $shift[$key]['no_of_position'] = $shifts->no_of_position;
            $shift[$key]['no_of_shifts'] = $requirement->no_of_shifts;
        }

        return $shift;
    }

    /**
     * Returns every date between two dates as an array
     * @param string $startDate the start of the date range
     * @param string $endDate the end of the date range
     * @return array returns every date between $startDate and $endDate, formatted as "Y-m-d"
     */
    public function createDateRange($startDate, $endDate)
    {

        $from_date = new DateTime($startDate);
        $to_date = new DateTime($endDate);
        $arr = array();
        $i = 0;
        for ($date = $from_date; $date <= $to_date; $date->modify('+1 day')) {
            $arr[$i]['date'] = $date->format('Y-m-d');
            $arr[$i]['day'] = $date->format('l');
            $i++;
        }
        return $arr;
    }

    /**
     * Delete allotted users
     * @param $id
     */
    public function deleteAllocated($id)
    {
        $multipleFillObject = $this->model->with(['scheduleCustomerRequirement', 'user', 'latestEventLog'])->find($id);
        $multipleFillStatus = $this->model->find($id)->update(['assigned' => 0, 'assigned_by' => null]);
        EventLogEntry::where('user_id', $multipleFillObject->assigned_employee_id)->where('multiple_shift_id', $id)->update(['status' => 3, 'score' => 1]);
        $deleteAllocatedUser = $this->model->where('id', $id)
            ->update(['assigned_employee_id' => null, 'assigned' => 0, 'assigned_by' => null]);
        CandidateOpenshiftApplication::where('userid', $multipleFillObject->assigned_employee_id)->where('multifillid', $id)->update(['status' => 0, 'approved_by' => null]);

        //email trigger - start
        if ($multipleFillObject->user) {
            $shiftTiming = ShiftTiming::pluck('shift_name', 'id');
            $assignmentTypeLookups = ScheduleAssignmentTypeLookup::pluck('type', 'id');

            $helper_variable = array(
                '{receiverFullName}' => ucfirst($multipleFillObject->user->first_name) . ' ' . ucfirst($multipleFillObject->user->last_name),
                '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
                '{loggedInUser}' => \Auth::user()->first_name . ' ' . \Auth::user()->last_name,
                '{client}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->client_name : '',
                '{projectNumber}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->project_number : '',
                '{candidateScheduleAssigneeName}' => ucfirst($multipleFillObject->user->first_name) . ' ' . ucfirst($multipleFillObject->user->last_name),
                '{candidateScheduleShiftStartDate}' => ($multipleFillObject) ? Carbon::parse($multipleFillObject->shift_from)->format('M d,Y') : '',
                '{candidateScheduleShiftEndDate}' => ($multipleFillObject) ? Carbon::parse($multipleFillObject->shift_to)->format('M d,Y') : '',
                '{candidateScheduleShiftStartTime}' => $multipleFillObject ? Carbon::parse($multipleFillObject->shift_from)->format('h:i A') : '',
                '{candidateScheduleShiftEndTime}' => $multipleFillObject ? Carbon::parse($multipleFillObject->shift_to)->format('h:i A') : '',
                '{candidateScheduleAssigneeName}' => ($multipleFillObject && $multipleFillObject->user) ? $multipleFillObject->user->first_name . ' ' . $multipleFillObject->user->last_name . ' (' . $multipleFillObject->user->employee->employee_no . ')' : '',
                '{candidateScheduleSiteRate}' => (($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->site_rate : 0),
                '{candidateScheduleNoOfShifts}' => ($multipleFillObject) ? $multipleFillObject->no_of_position : 0,
                '{candidateScheduleShiftTiming}' => ($multipleFillObject && $shiftTiming) ? ucfirst($shiftTiming[$multipleFillObject->shift_timing_id]) : '',
                '{candidateScheduleSiteAddress}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->address : '',
                '{candidateScheduleCity}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->city : '',
                '{candidateSchedulePostalCode}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->postal_code : '',
                '{candidateScheduleAssignmentType}' => ($assignmentTypeLookups) ? $assignmentTypeLookups[$multipleFillObject->scheduleCustomerRequirement->fill_type] : '',
            );

            $this->mailQueueRepository->prepareMailTemplate("candidate_schedule_employee_unassigned", $multipleFillObject->scheduleCustomerRequirement->customer->id, $helper_variable, "Modules\Timetracker\Models\ScheduleCustomerMultipleFillShifts", 0, $multipleFillObject->user->id);
        }
        //email trigger - end

        return $deleteAllocatedUser;
    }

    /**
     * Delete allotted users
     * @param $id
     */
    public function deleteShift($id)
    {
        $result = ['status' => false, 'delete_icon_parent' => null];
        $multipleFillObject = $this->model->with(['scheduleCustomerRequirement', 'user', 'latestEventLog'])->find($id);
        if ($multipleFillObject->assigned_employee_id != null) {
            return ['status' => false, 'delete_icon_parent' => null];
        } else {
            $currentCount = $multipleFillObject->no_of_position;
            if ($currentCount > 0) {
                $currentCount = ($currentCount - 1);
                $this->model->where('id', $multipleFillObject->parent_id)->update(['no_of_position' => $currentCount]);
                $this->model->where('id', $id)->update(['no_of_position' => $currentCount]);
            }
            $openShiftDeleteIds = CandidateOpenshiftApplication::where('multifillid', $id)->get()->pluck('id')->toArray();
            if (!empty($openShiftDeleteIds)) {
                CandidateOpenshiftApplication::destroy($openShiftDeleteIds);
            }

            $eventEntries = EventLogEntry::where('multiple_shift_id', $id)->get()->pluck('id')->toArray();
            if (!empty($openShiftDeleteIds)) {
                EventLogEntry::destroy($eventEntries);
            }

            $childrensCount = $this->model->where('parent_id', $multipleFillObject->parent_id)->count();
            if ($childrensCount <= 1) {
                $result['delete_icon_parent'] = $multipleFillObject->parent_id;
            }
        }

        $scheduleCustomerRequirement = ScheduleCustomerRequirement::find($multipleFillObject->schedule_customer_requirement_id);
        if (!empty($scheduleCustomerRequirement)) {
            $noOfShifts = ($scheduleCustomerRequirement->no_of_shifts > 0) ? ($scheduleCustomerRequirement->no_of_shifts - 1) : 0;
            $scheduleCustomerRequirement = ScheduleCustomerRequirement::where('id', $multipleFillObject->schedule_customer_requirement_id)->update(['no_of_shifts' => $noOfShifts]);
        }

        $shiftTiming = ShiftTiming::pluck('shift_name', 'id');
        $assignmentTypeLookups = ScheduleAssignmentTypeLookup::pluck('type', 'id');

        $helper_variable = array(
            '{receiverFullName}' => $multipleFillObject->user ? ucfirst($multipleFillObject->user->first_name) . ' ' . ucfirst($multipleFillObject->user->last_name) : '',
            '{loggedInUserEmployeeNumber}' => \Auth::user()->employee->employee_no,
            '{loggedInUser}' => \Auth::user()->first_name . ' ' . \Auth::user()->last_name,
            '{client}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->client_name : '',
            '{projectNumber}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->project_number : '',
            '{candidateScheduleAssigneeName}' => $multipleFillObject->user ? ucfirst($multipleFillObject->user->first_name) . ' ' . ucfirst($multipleFillObject->user->last_name) : '-',
            '{candidateScheduleShiftStartDate}' => ($multipleFillObject) ? Carbon::parse($multipleFillObject->shift_from)->format('M d,Y') : '',
            '{candidateScheduleShiftEndDate}' => ($multipleFillObject) ? Carbon::parse($multipleFillObject->shift_to)->format('M d,Y') : '',
            '{candidateScheduleShiftStartTime}' => $multipleFillObject ? Carbon::parse($multipleFillObject->shift_from)->format('h:i A') : '',
            '{candidateScheduleShiftEndTime}' => $multipleFillObject ? Carbon::parse($multipleFillObject->shift_to)->format('h:i A') : '',
            '{candidateScheduleSiteRate}' => (($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->site_rate : 0),
            '{candidateScheduleNoOfShifts}' => ($multipleFillObject) ? $multipleFillObject->no_of_position : 0,
            '{candidateScheduleShiftTiming}' => ($multipleFillObject && $shiftTiming) ? ucfirst($shiftTiming[$multipleFillObject->shift_timing_id]) : '',
            '{candidateScheduleSiteAddress}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->address : '',
            '{candidateScheduleCity}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->city : '',
            '{candidateSchedulePostalCode}' => ($multipleFillObject) ? $multipleFillObject->scheduleCustomerRequirement->customer->postal_code : '',
            '{candidateScheduleAssignmentType}' => ($assignmentTypeLookups) ? $assignmentTypeLookups[$multipleFillObject->scheduleCustomerRequirement->fill_type] : '',
            '{candidateScheduleAssigneeName}' => ($multipleFillObject && $multipleFillObject->user) ? $multipleFillObject->user->first_name . ' ' . $multipleFillObject->user->last_name . ' (' . $multipleFillObject->user->employee->employee_no . ')' : '-',
        );
        $this->mailQueueRepository->prepareMailTemplate("candidate_schedule_shift_removal", $multipleFillObject->scheduleCustomerRequirement->customer->id, $helper_variable, "Modules\Timetracker\Models\ScheduleCustomerMultipleFillShifts", 0, (($multipleFillObject->user) ? $multipleFillObject->user->id : 0));

        $this->model->destroy($id);
        $result['status'] = true;
        return $result;
    }

    public function checkForAnyApprovedShiftWithinRestrictedHours($multipleShiftId, $userId)
    {
        $shiftCount = 0;
        $thresholdHours = 0;
        $multipleFillShiftObject = $this->model->find($multipleShiftId);
        $stcThresholdSettings = StcThresholdSetting::where('deleted_at', null)->first();
        if (!empty($stcThresholdSettings)) {
            $thresholdHours = $stcThresholdSettings->stc_threshold_hours;
        }
        if (!empty($multipleFillShiftObject)) {
            $shiftFrom = $multipleFillShiftObject->shift_from;
            $shiftTo = $multipleFillShiftObject->shift_to;

            //fetch time restriction start from
            $shiftStart = \Carbon::parse($shiftFrom);
            $timeRestrictionStart = $shiftStart->subHours($thresholdHours)->addMinutes(1);

            //fetch time restriction end upto
            $shiftEnd = \Carbon::parse($shiftTo);
            $timeRestrictionEnd = $shiftEnd->addHours($thresholdHours)->subMinutes(1);

            $statusGroup = EventLogEntry::select('multiple_shift_id', DB::raw('MAX(id) as latest_id'))
                ->where('user_id', $userId)
                ->groupBy('multiple_shift_id')
                ->get();

            $multipleShiftIds = [];
            if (!empty($statusGroup)) {
                foreach ($statusGroup as $statusEntry) {
                    $eventObj = EventLogEntry::find($statusEntry->latest_id);
                    if ($eventObj->status == 1) {
                        $multipleShiftIds[] = $eventObj->multiple_shift_id;
                    }
                }
            }

            if (($key = array_search($multipleShiftId, $multipleShiftIds)) !== false) {
                unset($multipleShiftIds[$key]);
            }

            //check for any other approved shifts
            $shiftCount = $this->model
                ->whereIn('id', $multipleShiftIds)
                ->where('assigned', 1)
                ->where('assigned_employee_id', $userId)
                ->whereBetween('shift_from', [$timeRestrictionStart, $timeRestrictionEnd])
                ->count();

            if ($shiftCount == 0) {
                $shiftCount = $this->model
                    ->whereIn('id', $multipleShiftIds)
                    ->where('assigned', 1)
                    ->where('assigned_employee_id', $userId)
                    ->whereBetween('shift_to', [$timeRestrictionStart, $timeRestrictionEnd])
                    ->count();
            }
        }
        return $shiftCount;
    }
}
