<?php

namespace Modules\Supervisorpanel\Repositories;

use Carbon\Carbon;
use Modules\Admin\Models\SiteSettings;
use Modules\Hranalytics\Models\EventLogEntry;
use Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts;
use Modules\Hranalytics\Models\ScheduleCustomerRequirement;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class StcScheduleGeoMappingRepository
{

    protected $scheduleCustomerRequirementModel, $scheduleCustomerMultipleFillShiftsModel, $eventLog;

    public function __construct(EventLogEntry $eventLog, ScheduleCustomerRequirement $scheduleCustomerRequirementModel, ScheduleCustomerMultipleFillShifts $scheduleCustomerMultipleFillShiftsModel)
    {
        $this->scheduleCustomerRequirementModel = $scheduleCustomerRequirementModel;
        $this->scheduleCustomerMultipleFillShiftsModel = $scheduleCustomerMultipleFillShiftsModel;
        $this->eventLog = $eventLog;
    }

    private function queryStcSiteDetailsByParam($filterInputs = [], $isStc = 0)
    {
        $user = \Auth::user();
        $query = $this->scheduleCustomerRequirementModel->with('multifill.latestEventLog')
            ->whereHas('customer', function ($query) use ($filterInputs, $isStc) {
                if ($isStc != 0) {
                    $customerType = ($isStc == 1) ? 1 : 0;
                    $query->where('stc', $customerType);
                }

                if (!empty($filterInputs['city'])) {
                    $customerCity = array_map(function ($value) {
                        return $value;
                    }, $filterInputs['city']);
                    $query->whereIn('city', $customerCity);
                }
            })
            ->when(($user->role == 'duty_officer' || $user->role == 'operator'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if (!empty($filterInputs['assignment_type'])) {
            $assignmentTypes = array_map(function ($value) {
                return intval($value);
            }, $filterInputs['assignment_type']);
            $query->whereIn('fill_type', $assignmentTypes);
        }

        if (!empty($filterInputs['security_clearance'])) {
            $securityClearance = array_map(function ($value) {
                return intval($value);
            }, $filterInputs['security_clearance']);
            $query->whereIn('security_clearance_level', $securityClearance);
        }

        if (!empty($filterInputs['customerId'])) {
            $query->where('customer_id', $filterInputs['customerId']);
        }

        $query->whereHas('multifill', function ($query) use ($filterInputs) {
            $startDay = Carbon::createFromFormat('Y-m-d h:i A', $filterInputs['start_date'] . ' ' . $filterInputs['start_time']);
            $endDay = Carbon::createFromFormat('Y-m-d h:i A', $filterInputs['end_date'] . ' ' . $filterInputs['end_time']);
            $query->whereBetween('shift_from', [$startDay, $endDay])
                ->where('assigned_employee_id', '!=', null);
        });

        if (!empty($filterInputs['wage_from'])) {
            $query->whereHas('multifill.latestEventLog', function ($query) use ($filterInputs) {
                return $query->where('accepted_rate', '>=', $filterInputs['wage_from'])->where('status', 1);
            });
        }

        if (!empty($filterInputs['wage_to'])) {
            $query->whereHas('multifill.latestEventLog', function ($query) use ($filterInputs) {
                return $query->where('accepted_rate', '<=', $filterInputs['wage_to'])->where('status', 1);
            });
        }

        return $query;
    }

    public function getStcSiteDetails($filterInputs = [])
    {
        $stcCustomerSchedules = null;
        $stcCustomerSchedules = $this->queryStcSiteDetailsByParam($filterInputs, $filterInputs['customer_type'])->with(['customer', 'multifill', 'multifill.latestEventLog'])->get();

        if (empty($stcCustomerSchedules)) {
            return [];
        }

        $resultArray = [];
        $resultOut = [];
        $shiftFrom = Carbon::createFromFormat('Y-m-d h:i A', $filterInputs['start_date'] . ' ' . $filterInputs['start_time']);
        $shiftTo = Carbon::createFromFormat('Y-m-d h:i A', $filterInputs['end_date'] . ' ' . $filterInputs['end_time']);
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $filterInputs['start_date'] . ' 00:00:00');
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $filterInputs['end_date'] . ' 23:59:59');

        //for stc customers
        if (!empty($stcCustomerSchedules)) {
            foreach ($stcCustomerSchedules as $customerSchedule) {
                $customerId = $customerSchedule->customer->id;
                $multiShifts = $customerSchedule->multifill;

                $i = false;
                foreach ($multiShifts as $multiShift) {
                    $eventLog = $multiShift->latestEventLog;
                    if (($eventLog == null) || ($eventLog->status != 1)) {
                        continue;
                    }
                    $wageFromCondition = ((!empty($filterInputs['wage_from']) && ($eventLog) && ($eventLog->accepted_rate != null) && ($eventLog->accepted_rate >= $filterInputs['wage_from'])) || empty($filterInputs['wage_from']));
                    $wageToCondition = ((!empty($filterInputs['wage_to']) && ($eventLog) && ($eventLog->accepted_rate != null) && ($eventLog->accepted_rate <= $filterInputs['wage_to'])) || empty($filterInputs['wage_to']));
                    $multiShiftFrom = Carbon::parse($multiShift->shift_from);
                    $multiShiftTo = Carbon::parse($multiShift->shift_to);
                    if (
                        ($multiShift->assigned_employee_id != null) &&
                        ($multiShiftFrom >= $shiftFrom) &&
                        ($multiShiftTo <= $shiftTo) && $wageFromCondition && $wageToCondition) {
                        $i = true;
                        $signInStatus = $this->getSignInSignOutDetailsByParam($customerSchedule->id, $customerSchedule->customer->id, $multiShift->assigned_employee_id, $multiShiftFrom, $multiShiftTo, true);
                        $resultArray[$customerId][] = $signInStatus;
                    }
                }

                if (!$i) {
                    continue;
                }
                $resultOut[$customerId]['customer'] = $customerSchedule->customer;
            }
        }

        if (!empty($resultArray)) {
            foreach ($resultArray as $ky => $result) {
                $maxValue = max($result);
                if ($maxValue == 2) {
                    $resultOut[$ky]['color'] = 'red';
                } elseif ($maxValue == 1) {
                    $resultOut[$ky]['color'] = 'yellow';
                } else {
                    $resultOut[$ky]['color'] = 'green';
                }
            }
        }

        return $resultOut;
    }

    public function getStcGeoMappingDetailsByCustomer($filterInputs = [])
    {
        $user = \Auth::user();
        $query = $this->eventLog->where('status', 1)->whereHas('shift', function ($query) use ($filterInputs) {
            $query->where('assigned_employee_id', '!=', null);
        })
            ->where('user_id', '!=', null)
            ->whereHas('requirement.customer', function ($query) use ($filterInputs) {
                if (!empty($filterInputs['city'])) {
                    $customerCity = explode(',', $filterInputs['city']);
                    $query->whereIn('city', $customerCity);
                }
            })->whereHas('requirement', function ($query) use ($filterInputs) {
            if (!empty($filterInputs['security_clearance'])) {
                $securityClearance = explode(',', $filterInputs['security_clearance']);
                $query->whereIn('security_clearance_level', $securityClearance);
            }

            if (!empty($filterInputs['assignment_type'])) {
                $assignmentTypes = explode(',', $filterInputs['assignment_type']);
                $query->whereIn('fill_type', $assignmentTypes);
            }
            $query->where('customer_id', $filterInputs['customerId']);
        })->whereHas('requirement', function ($query) use ($user) {
            $query->when(($user->role == 'duty_officer' || $user->role == 'operator'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        });

        if (!empty($filterInputs['wage_from'])) {
            $query->where('accepted_rate', '>=', $filterInputs['wage_from']);
        }

        if (!empty($filterInputs['wage_to'])) {
            $query->where('accepted_rate', '<=', $filterInputs['wage_to']);
        }

        $records = $query->with(['requirement', 'requirement.customer', 'shift', 'trashed_user', 'trashed_user.trashedEmployee', 'user.eventlog_score'])->get();
        return $this->prepareRecords($records, $filterInputs);
    }

    public function prepareRecords($records, $filterInputs = [])
    {
        if (empty($records)) {
            return [];
        }

        $results = [];
        foreach ($records as $record) {
            $employeeAssigned = $record->user_id;
            if ($employeeAssigned == null) {
                continue;
            }

            // if (($record->requirement->event_log_entry_latest == null) || (($record->requirement->event_log_entry_latest != null) && (($record->requirement->event_log_entry_latest->status != 1) || ($record->requirement->event_log_entry_latest->user_id != $record->trashed_user->id)))) {
            //     continue;
            // }

            $multipleShift = $record->shift;
            if (!empty($multipleShift)) {
                $startDay = Carbon::createFromFormat('Y-m-d h:i A', $filterInputs['start_date'] . ' ' . $filterInputs['start_time']);
                $endDay = Carbon::createFromFormat('Y-m-d h:i A', $filterInputs['end_date'] . ' ' . $filterInputs['end_time']);
                $shiftFrom = Carbon::parse($multipleShift->shift_from);
                $shiftTo = Carbon::parse($multipleShift->shift_to);

                if (!(($shiftFrom >= $startDay) && ($shiftTo <= $endDay))) {
                    continue;
                }
            } else {
                continue;
            }

            $results[$employeeAssigned]['employee_no'] = ($record->trashed_user && $record->trashed_user->trashedEmployee) ? $record->trashed_user->trashedEmployee->employee_no : '';
            $results[$employeeAssigned]['full_name'] = $record->trashed_user ? $record->trashed_user->full_name : '';
            $results[$employeeAssigned]['first_name'] = $record->trashed_user ? $record->trashed_user->first_name : '';
            $results[$employeeAssigned]['last_name'] = $record->trashed_user ? $record->trashed_user->last_name : '';
            $results[$employeeAssigned]['score'] = ((count($record->user->eventlog_score) > 0) ? (int) $record->user->eventlog_score[0]->avg_score : 0);
            $results[$employeeAssigned]['phone'] = ($record->trashed_user && $record->trashed_user->trashedEmployee) ? $record->trashed_user->trashedEmployee->phone : '';
            $results[$employeeAssigned]['email'] = $record->trashed_user ? $record->trashed_user->email : '';
            $results[$employeeAssigned]['image'] = ($record->trashed_user && $record->trashed_user->trashedEmployee) ? $record->trashed_user->trashedEmployee->image : '';
            $results[$employeeAssigned]['id'] = $record->id;

            $signInOutArray = $this->getSignInSignOutDetailsByParam($record->requirement->id, $record->requirement->customer->id, $employeeAssigned, $shiftFrom, $shiftTo);
            $result['sign_in'] = $signInOutArray['sign_in'];
            $result['sign_out'] = $signInOutArray['sign_out'];
            $result['sign_in_color'] = $signInOutArray['sign_in_color'];
            $result['sign_out_color'] = $signInOutArray['sign_out_color'];
            $result['project_number'] = $record->requirement->customer->project_number;
            $result['project_number'] = $record->requirement->customer->project_number;
            $result['client_name'] = $record->requirement->customer->client_name;
            $result['shift_from'] = $shiftFrom->format('Y-m-d H:i:s');
            $result['shift_to'] = $shiftTo->format('Y-m-d H:i:s');
            $result['customer_id'] = $record->requirement->customer->id;
            $result['schedule_customer_requirement_id'] = $record->requirement->id;
            $result['stc'] = $record->requirement->customer->stc;
            $result['site_rate'] = '$ ' . ($record->requirement->site_rate);
            $result['accepted_rate'] = '$ ' . number_format($record->accepted_rate, 2);
            $result['security_clearance_level'] = ($record->requirement->customer->security_clearance_level != null) ? $record->requirement->customer->security_clearance_level : '';

            $results[$employeeAssigned]['details'][] = $result;
        }

        return $results;
    }

    public function getSignInSignOutDetailsByParam($requirementId, $customerId, $employeeId, $scheduleTimeFrom, $scheduleTimeTo, $returnStatusCodeOnly = false)
    {
        $resultArray = ['sign_in' => null, 'sign_out' => null, 'sign_in_color' => 'color-red', 'sign_out_color' => 'color-red'];
        //start,end time calculations
        $signInStartDate = \Carbon::createFromFormat('Y-m-d H:i:s', $scheduleTimeFrom)->subHours(8);
        $signOutEndDate = \Carbon::createFromFormat('Y-m-d H:i:s', $scheduleTimeTo)->addHours(8);
        $qry = EmployeeShiftPayperiod::where('customer_id', $customerId);
        if (!empty($employeeId)) {
            $qry->where('employee_id', $employeeId);
        } else {
            $employeeIds = ScheduleCustomerMultipleFillShifts::where('schedule_customer_requirement_id', $requirementId)
                ->where('assigned_employee_id', '!=', null)
                ->where('shift_from', '>=', $signInStartDate)
                ->where('shift_to', '<=', $signOutEndDate)
                ->pluck('assigned_employee_id')->toArray();
            $qry->whereIn('employee_id', $employeeIds);
        }
        $shiftPayperiods = $qry->pluck('id')->toArray();
        if (!empty($shiftPayperiods)) {

            //sign in
            $maximumShiftInTolerance = (SiteSettings::find(1)->shift_start_time_tolerance) + 1;
            $signInQry = EmployeeShift::whereIn('employee_shift_payperiod_id', $shiftPayperiods)
                ->where('start', '>=', $signInStartDate)->where('start', '<=', $signOutEndDate)
                ->orderBy(\DB::raw('abs(TIMESTAMPDIFF(SECOND,start,"' . $scheduleTimeFrom . '"))'), 'asc')
                ->take(1);
            $signIn = $signInQry->get();
            $time = new \DateTime($scheduleTimeFrom);
            $time->add(new \DateInterval('PT' . $maximumShiftInTolerance . 'M'));
            $timeFromWithToleranceHours = $time->format('Y-m-d H:i');
            $resultArray['sign_in_color'] = ((isset($signIn[0])) ? (($signIn[0]->start <= $scheduleTimeFrom) ? 'color-green' : (($signIn[0]->start <= $timeFromWithToleranceHours) ? 'color-yellow' : 'color-red')) : 'color-red');
            $resultArray['sign_in'] = (isset($signIn[0])) ? $signIn[0]->start : null;

            //sign out
            $maximumShiftOutTolerance = (SiteSettings::find(1)->shift_end_time_tolerance) + 1;
            $signOutQry = EmployeeShift::whereIn('employee_shift_payperiod_id', $shiftPayperiods)
                ->where('end', '>=', $signInStartDate)->where('end', '<=', $signOutEndDate)
                ->orderBy(\DB::raw('abs(TIMESTAMPDIFF(SECOND,end,"' . $scheduleTimeTo . '"))'), 'asc')
                ->take(1);
            $signOut = $signOutQry->get();
            $time = new \DateTime($scheduleTimeTo);
            $time->sub(new \DateInterval('PT' . $maximumShiftOutTolerance . 'M'));
            $timeToWithToleranceHours = $time->format('Y-m-d H:i');
            $resultArray['sign_out_color'] = ((isset($signOut[0])) ? (($signOut[0]->end >= $scheduleTimeTo) ? 'color-green' : (($signOut[0]->end >= $timeToWithToleranceHours) ? 'color-yellow' : 'color-red')) : 'color-red');
            $resultArray['sign_out'] = (isset($signOut[0])) ? $signOut[0]->end : null;

            if ($returnStatusCodeOnly) {
                $signInStatus = ((isset($signIn[0])) ? (($signIn[0]->start <= $scheduleTimeFrom) ? 0 : (($signIn[0]->start <= $timeFromWithToleranceHours) ? 1 : 2)) : 2);
                $signOutStatus = ((isset($signOut[0])) ? (($signOut[0]->end >= $scheduleTimeTo) ? 0 : (($signOut[0]->end >= $timeToWithToleranceHours) ? 1 : 2)) : 2);
                // dd('f');
                return max([$signInStatus, $signOutStatus]);
            }
        }

        if ($returnStatusCodeOnly) {
            return 2;
        }

        return $resultArray;
    }

}
