<?php

namespace Modules\Employeescheduling\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Contracts\Repositories\ContractsRepository;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Models\EmployeeScheduleAveragePayperiodHours;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;
use Modules\Employeescheduling\Models\ScheduledEmployeeWorkHour;

class InheritScheduleRepository
{

    protected $payPeriodRepository, $employeeScheduleTimeLog, $schedulingRepository, $userRepository, $contractsRepository, $customerRepository;

    public function __construct(
        PayPeriodRepository $payPeriodRepository,
        EmployeeScheduleTimeLog $employeeScheduleTimeLog,
        UserRepository $userRepository,
        SchedulingRepository $schedulingRepository,
        ContractsRepository $contractsRepository,
        CustomerRepository $customerRepository
    ) {
        $this->payPeriodRepository = $payPeriodRepository;
        $this->employeeScheduleTimeLog = $employeeScheduleTimeLog;
        $this->schedulingRepository = $schedulingRepository;
        $this->userRepository = $userRepository;
        $this->contractsRepository = $contractsRepository;
        $this->customerRepository = $customerRepository;
    }

    public function fetchSourcePayperiods($customerId)
    {
        $otherPayperiodIdArray = $this->employeeScheduleTimeLog->select('payperiod_id')
            ->whereHas('schedule', function ($qry) use ($customerId) {
                $qry->where('customer_id', $customerId)->where('status', 1);
            })->groupBy('payperiod_id')->pluck('payperiod_id');

        if (empty($otherPayperiodIdArray)) {
            return [];
        } else {
            return $this->payPeriodRepository->getPayperioddetailsfromarray($otherPayperiodIdArray);
        }
    }

    public function getSchedulesByParam(
        $customerId,
        $payperiod,
        $approvedOnly = true,
        $sourceScheduleID = null,
        $employeeID = null
    ) {
        $qry = $this->employeeScheduleTimeLog->when(($approvedOnly == true), function ($q) use ($approvedOnly) {
            $q->whereHas('schedule', function ($qry) use ($approvedOnly) {
                $qry->where('status', 1);
            });
        }, function ($q) {
            $q->whereHas('schedule', function ($qry) {
                $qry->whereIn('status', [0, 1]);
            });
        });

        if ($customerId != null) {
            $qry->whereHas('schedule', function ($qry) use ($customerId) {
                $qry->where('customer_id', $customerId);
            });
        } else {
            $qry->whereHas('schedule.customer', function ($qry) {
                $qry->where('active', 1);
            });
        }
        if (is_array($payperiod)) {
            $qry->whereIn('payperiod_id', $payperiod);
        } else {
            $qry->where('payperiod_id', $payperiod);
        }
        if ($employeeID != null) {
            $qry->whereIn('user_id', $employeeID);
        }

        $qry = $qry->with('schedule')->orderBy('schedule_date', 'ASC');
        return $qry->get()->toArray();
    }

    public function crossCheck($request)
    {
        $exist = false;
        $customerId = $request->get('customer_id');
        $sourcePayPeriod = $request->get('source_payperiod');
        $destinationPayPeriod = $request->get('destination_payperiod');
        if (!is_array($destinationPayPeriod)) {
            $destinationPayPeriodArray[0] = $destinationPayPeriod;
        } else {
            $destinationPayPeriodArray = $destinationPayPeriod;
        }
        $employeeIdfromSourceSchedule = EmployeeScheduleTimeLog::where([
            "payperiod_id" => $sourcePayPeriod,
        ])->whereHas("schedule", function ($q) use ($customerId) {
            return $q->where("customer_id", $customerId);
        })->pluck("user_id")->toArray();
        $employeeIds = collect($this->userRepository->allocationUserList($customerId, ['client'], true, false, true))
            ->sortBy("first_name")->pluck('id')->toArray();
        if (count($employeeIdfromSourceSchedule) > 0) {
            $sparesPoolUser = User::whereIn("id", $employeeIdfromSourceSchedule)->whereHas("roles", function ($q) {
                return $q->where("name", "Spares Pool");
            })->pluck("id")->toArray();
            if (count($sparesPoolUser) > 0) {
                $exist = true;
            }
        }

        $intersectEmployees = array_values(array_intersect($employeeIdfromSourceSchedule, $employeeIds));
        if (count($intersectEmployees)) {
            $exist = true;
        }

        return $exist;
    }

    public function approvedPayperiodExist($request)
    {
        $customerId = $request->get('customer_id');
        $destinationPayPeriod = $request->get('destination_payperiod');
        if (!is_array($destinationPayPeriod)) {
            $destinationPayPeriodArray[0] = $destinationPayPeriod;
        } else {
            $destinationPayPeriodArray = $destinationPayPeriod;
        }
        $approvedScheduleCount = EmployeeScheduleTimeLog::whereIn('payperiod_id', $destinationPayPeriodArray)
            ->whereHas('schedule', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId)->where('status', 1);
            })->count();

        if ($approvedScheduleCount > 0) {
            return true;
        }
        return false;
    }

    public function inheritProcess($customerId,$sourcePayPeriod,$destinationPayPeriod)
    {
        $outArray = $dateIndex = [];
        
        $sourceScheduleID = null;

        if (!is_array($destinationPayPeriod)) {
            $destinationPayPeriodArray[0] = $destinationPayPeriod;
        } else {
            $destinationPayPeriodArray = $destinationPayPeriod;
        }

        //fetch payperiods by source payperiod param
        $srcPayperiods = $this->payPeriodRepository->getPayperioddetailsfromarray([$sourcePayPeriod]);
        //process destination pay periods
        foreach ($srcPayperiods as $srcPayperiod) {
            $payPeriodId = $srcPayperiod['id'];
            $startDte = Carbon::parse($srcPayperiod['start_date']);
            $endDte = Carbon::parse($srcPayperiod['end_date']);

            $i = 0;
            $j = 0;
            while ($startDte <= $endDte) {
                $dateIndex['source'][$startDte->format('d-m-Y')] = $j . "_" . $startDte->format('l');
                $startDte = $startDte->addDay();
                $i++;

                if ($i % 7 == 0) {
                    $j++;
                }
            }
        }
        //fetch source schedule by customer and pay period
        $sparePoolUserList = User::whereHas('roles.permissions', function ($query) {
            $query->where('name', "Spares Pool");
        })->pluck('id')->toArray();

        //fetch allocated employees list
        $employeeIds = collect($this->userRepository->allocationUserList($customerId, ['client'], true, false, true))
            ->sortBy("first_name")->pluck('id')->toArray();
        $employeeIdfromSourceSchedule = EmployeeScheduleTimeLog::where([
            "payperiod_id" => $sourcePayPeriod,
        ])->whereHas("schedule", function ($q) use ($customerId) {
            return $q->where("customer_id", $customerId);
        })->get();
        if ($employeeIdfromSourceSchedule->count() > 0) {
            $sourceScheduleID = $employeeIdfromSourceSchedule[0]->employee_schedule_id;
        }
        $employeeIdfromSourceSchedule =     $employeeIdfromSourceSchedule->pluck("user_id")->toArray();
        $employeeIdfromSourceSchedule = array_unique($employeeIdfromSourceSchedule);
        $intersectEmployees = array_values(array_intersect($employeeIdfromSourceSchedule, $employeeIds));
        if (count($intersectEmployees) > 0) {
            $employeeIds = $intersectEmployees;
        } else {
            $employeeIds = [];
        }
        $intersectSpareEmployees = array_values(array_intersect($sparePoolUserList, $employeeIdfromSourceSchedule));
        if (count($intersectSpareEmployees) > 0) {
            $intersectSpareEmployees_arr = array_merge($employeeIds, $intersectSpareEmployees);
            $employeeIds = $intersectSpareEmployees_arr;
        }

        //fetch payperiods by destination payperiod param
        $destPayperiods = $this->payPeriodRepository->getPayperioddetailsfromarray($destinationPayPeriodArray);
        //for user fetch from destination payperiod
        $destScheduleObjects = $this->getSchedulesByParam($customerId, $destinationPayPeriodArray, false);
        $destEmpArray = collect($destScheduleObjects)->pluck('user_id')->toArray();



        //fetch spare pool employees
        $sparePoolUsers = [];
        $sourceScheduleForSparePools = $this->getSchedulesByParam($customerId, $sourcePayPeriod);
        foreach ($sourceScheduleForSparePools as $sourceScheduleForSparePool) {
            if (in_array($sourceScheduleForSparePool['user_id'], $sparePoolUserList)) {
                $sparePoolUsers[] = $sourceScheduleForSparePool['user_id'];
            }
        }
        //fetch destination schedule by pay period
        $destinationSchedule = $this->getSchedulesByParam($customerId, $destinationPayPeriodArray, false, $sourceScheduleID, $employeeIds);
        if (!empty($employeeIds)) {
            $employeeIds = array_merge($employeeIds, $destEmpArray, $sparePoolUsers);
        }
        $empArray = array_fill_keys($employeeIds, null);
        //process destination pay periods
        $startDatesArray = $dateIndexArray = [];
        foreach ($destPayperiods as $destPayperiod) {
            $payPeriodId = $destPayperiod['id'];
            $startDte = Carbon::parse($destPayperiod['start_date']);
            $startDatesArray[$payPeriodId] = $startDte;
            $endDte = Carbon::parse($destPayperiod['end_date']);

            $i = 0;
            $j = 0;
            while ($startDte <= $endDte) {
                $dateIndex['destination'][$startDte->format('d-m-Y')] = $j . "_" . $startDte->format('l');
                $outArray[$payPeriodId][$dateIndex['destination'][$startDte->format('d-m-Y')]] = $empArray;
                $dateIndexArray[$payPeriodId . '_' . $dateIndex['destination'][$startDte->format('d-m-Y')]] = $startDte->format('d-m-Y');
                $startDte = $startDte->addDay();
                $i++;

                if (($i % 7) == 0) {
                    $j++;
                }
            }
        }
        $scheduleDaysHoursArray = $scheduleForRejection = [];
        //fetch already existing schedule from the destination
        if (!empty($destinationSchedule)) {

            foreach ($destinationSchedule as $destSchedule) {
                $scheduleSelected = EmployeeSchedule::find($destSchedule['employee_schedule_id']);
                if (($scheduleSelected->status != 1) && ((!isset($destScheduleObjects[0])) || ($destSchedule['employee_schedule_id'] != $destScheduleObjects[0]['employee_schedule_id']))) {
                    continue;
                }

                if (!in_array($destSchedule['user_id'], $employeeIds)) {
                    continue;
                }

                $payPeriodId = $destSchedule['payperiod_id'];
                $scheduleDate = Carbon::parse($destSchedule['schedule_date']);

                if ((!array_key_exists($scheduleDate->format('d-m-Y'), $dateIndex['destination'])) || (!array_key_exists($dateIndex['destination'][$scheduleDate->format('d-m-Y')], $outArray[$payPeriodId]))) {
                    continue;
                }
                $scheduleFormatedDate = Carbon::parse($destSchedule['schedule_date'])->format('Y-m-d');
                $startTime = Carbon::parse($destSchedule['start_datetime'])->format('H:i');
                $endTime = Carbon::parse($destSchedule['end_datetime'])->format('H:i');
                $processedData = $this->schedulingRepository->processDates($scheduleFormatedDate, $startTime, $endTime);
                if (
                    array_key_exists($destSchedule['user_id'], $outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]])
                    && ($outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']] != null)
                ) {
                    $outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']]['overlaps'] = true;
                    $outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']]['start_datetime'] = Carbon::parse($destSchedule['start_datetime'])->format('H:i:s');
                    $outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']]['end_datetime'] = Carbon::parse($destSchedule['end_datetime'])->format('H:i:s');
                    $outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']]['hours'] = $processedData[2];
                } else {
                    $outArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']] = [
                        'employee_schedule_id' => $destSchedule['employee_schedule_id'],
                        'week' => $destSchedule['week'],
                        'schedule_date' => $destSchedule['schedule_date'],
                        'start_datetime' => Carbon::parse($destSchedule['start_datetime'])->format('H:i:s'),
                        'end_datetime' => Carbon::parse($destSchedule['end_datetime'])->format('H:i:s'),
                        'hours' => $processedData[2],
                        'overlaps' => false,
                    ];
                }

                $scheduleForRejection[$payPeriodId] = $destSchedule['employee_schedule_id'];
                $rejectStatus = $this->schedulingRepository->rejectScheduleById($destSchedule['employee_schedule_id'], "System auto-rejected through inherit process");
                $scheduleDaysHoursArray[$payPeriodId][$dateIndex['destination'][$scheduleDate->format('d-m-Y')]][$destSchedule['user_id']] = $processedData[2];
            }
        }

        //fetch source schedule by pay period
        $sourceSchedule = $this->getSchedulesByParam($customerId, $sourcePayPeriod);

        if (!empty($sourceSchedule)) {
            foreach ($outArray as $payPeriodId => $outVal) {
                foreach ($sourceSchedule as $srcSchedule) {
                    if ((!in_array($srcSchedule['user_id'], $employeeIds)) && (!in_array($srcSchedule['user_id'], $sparePoolUsers))) {
                        continue;
                    }

                    $scheduleDate = Carbon::parse($srcSchedule['schedule_date']);

                    if ((!array_key_exists($scheduleDate->format('d-m-Y'), $dateIndex['source'])) || (!array_key_exists($dateIndex['source'][$scheduleDate->format('d-m-Y')], $outArray[$payPeriodId]))) {
                        continue;
                    }
                    $scheduleFormatedDate = Carbon::parse($srcSchedule['schedule_date'])->format('Y-m-d');
                    $startTime = Carbon::parse($srcSchedule['start_datetime'])->format('H:i');
                    $endTime = Carbon::parse($srcSchedule['end_datetime'])->format('H:i');
                    $processedData = $this->schedulingRepository->processDates($scheduleFormatedDate, $startTime, $endTime);
                    if (
                        array_key_exists($srcSchedule['user_id'], $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]])
                        && $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']] != null
                    ) {
                        $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']]['overlaps'] = true;
                        $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']]['start_datetime'] = Carbon::parse($srcSchedule['start_datetime'])->format('H:i:s');
                        $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']]['end_datetime'] = Carbon::parse($srcSchedule['end_datetime'])->format('H:i:s');
                        $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']]['hours'] = $processedData[2];
                    } else {
                        $outArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']] = [
                            'employee_schedule_id' => $srcSchedule['employee_schedule_id'],
                            'week' => $srcSchedule['week'],
                            'schedule_date' => $srcSchedule['schedule_date'],
                            'start_datetime' => Carbon::parse($srcSchedule['start_datetime'])->format('H:i:s'),
                            'end_datetime' => Carbon::parse($srcSchedule['end_datetime'])->format('H:i:s'),
                            'hours' => $processedData[2],
                            'overlaps' => false,
                        ];
                    }
                    $scheduleDaysHoursArray[$payPeriodId][$dateIndex['source'][$scheduleDate->format('d-m-Y')]][$srcSchedule['user_id']] = $processedData[2];
                }
            }
        }

        //calculate average hours per week, weekly hours,weekly hours per users
        $avgHoursPerWeekArray = [];
        if (!empty($scheduleDaysHoursArray)) {
            foreach ($scheduleDaysHoursArray as $ky => $valArr) {
                $averagePerWeek = 0;
                $weekOneHours = 0;
                $weekTwoHours = 0;
                $userHoursWeekOne = [];
                $userHoursWeekTwo = [];
                foreach ($valArr as $dayInd => $val) {
                    $dateIndexExplode = explode("_", $dayInd);
                    $week = (int) $dateIndexExplode[0];
                    foreach ($val as $usr => $hours) {
                        if ($week > 0) {
                            if (array_key_exists($usr, $userHoursWeekOne)) {
                                $userHoursWeekOne[$usr] += $hours;
                            } else {
                                $userHoursWeekOne[$usr] = $hours;
                            }
                        } else {
                            if (array_key_exists($usr, $userHoursWeekTwo)) {
                                $userHoursWeekTwo[$usr] += $hours;
                            } else {
                                $userHoursWeekTwo[$usr] = $hours;
                            }
                        }
                    }

                    $averagePerWeek += array_sum($val);

                    if ($week > 0) {
                        $weekTwoHours += array_sum($val);
                    } else {
                        $weekOneHours += array_sum($val);
                    }
                }

                $avgHoursPerWeekArray[$ky] = [
                    'averageHoursPerWeek' => ($averagePerWeek / 2),
                    'weekOneHours' => $weekOneHours,
                    'weekTwoHours' => $weekTwoHours,
                    'weekOneUserHours' => $userHoursWeekOne,
                    'weekTwoUserHours' => $userHoursWeekTwo,
                ];
            }
        }
        //insert employee scheduling, time log
        foreach ($outArray as $payPeriodId => $scheduleLog) {
            if (!array_key_exists($payPeriodId, $avgHoursPerWeekArray)) {
                continue;
            }
            //fetch average hours per week
            $averageHoursPerWeek = $avgHoursPerWeekArray[$payPeriodId]['averageHoursPerWeek'];
            //fetch contractual hours
            $contractHours = null;
            try {
                $contractHours = ($this->contractsRepository->getContractsBetweenTwoDatesByCustomerId(
                    $customerId,
                    null != ($startDatesArray[$payPeriodId]->format("Y-m-d")) ? $startDatesArray[$payPeriodId]->format("Y-m-d") : null
                ))["total_hours_perweek"];
            } catch (\Throwable $th) {
                //throw $th;
            }


            //save employee schedule
            $lastInsertScheduleId = $this->schedulingRepository->saveSchedule(
                '',
                $customerId,
                'System auto-generated through inherit process',
                ($averageHoursPerWeek - $contractHours),
                (($averageHoursPerWeek == $contractHours) ? 1 : 0),
                $averageHoursPerWeek,
                $contractHours
            );

            //weekly hours user wise entry
            if (!empty($avgHoursPerWeekArray[$payPeriodId]["weekOneUserHours"])) {
                foreach ($avgHoursPerWeekArray[$payPeriodId]["weekOneUserHours"] as $usrId => $userHours) {
                    $workHoursEntry = ScheduledEmployeeWorkHour::insert([
                        "employee_schedule_id" => $lastInsertScheduleId,
                        "payperiod_id" => $payPeriodId,
                        "user_id" => $usrId,
                        "week" => 1,
                        "workhours" => $userHours,
                    ]);
                }
            }

            if (!empty($avgHoursPerWeekArray[$payPeriodId]["weekTwoUserHours"])) {
                foreach ($avgHoursPerWeekArray[$payPeriodId]["weekTwoUserHours"] as $usrId => $userHours) {
                    $workHoursEntry = ScheduledEmployeeWorkHour::insert([
                        "employee_schedule_id" => $lastInsertScheduleId,
                        "payperiod_id" => $payPeriodId,
                        "user_id" => $usrId,
                        "week" => 2,
                        "workhours" => $userHours,
                    ]);
                }
            }
            // dd($scheduleLog);
            //employee schedule time log entries
            $overlaps = false;
            foreach ($scheduleLog as $dateIndex => $logs) {
                $dateIndexExplode = explode("_", $dateIndex);
                $week = (int) $dateIndexExplode[0];
                foreach ($logs as $userId => $log) {
                    if ($log != null) {
                        $overlaps = (!$overlaps) ? $log['overlaps'] : true;

                        $dateIndexKey = $payPeriodId . '_' . $dateIndex;
                        if (!array_key_exists($dateIndexKey, $dateIndexArray)) {
                            continue;
                        }
                        $scheduleDate = Carbon::parse($dateIndexArray[$dateIndexKey]);
                        $scheduleStartDate = Carbon::parse($dateIndexArray[$dateIndexKey] . ' ' . $log['start_datetime']);
                        $scheduleEndDate = Carbon::parse($dateIndexArray[$dateIndexKey] . ' ' . $log['end_datetime']);
                        $this->employeeScheduleTimeLog::insert([
                            "employee_schedule_id" => $lastInsertScheduleId,
                            "user_id" => $userId,
                            "payperiod_id" => $payPeriodId,
                            "week" => ($week > 0) ? 2 : 1,
                            "schedule_date" => $scheduleDate,
                            "start_datetime" => $scheduleStartDate,
                            "end_datetime" => $scheduleEndDate,
                            "hours" => $log['hours'],
                            "approved_by" => null,
                            "approved_Date" => null,
                            "created_by" => null!==(Auth::user())?Auth::user()->id:0,
                            "approved" => 0,
                            'overlaps' => $log['overlaps'],
                        ]);
                    }
                }
            }

            $scheduleObject = $this->schedulingRepository->getScheduleById($lastInsertScheduleId);
            if (!empty($scheduleObject)) {
                if (array_key_exists($payPeriodId, $scheduleForRejection)) {
                    $scheduleObject->initial_schedule_id = $scheduleForRejection[$payPeriodId];
                }
                $scheduleObject->schedule_overlaps = $overlaps;
                $scheduleObject->save();
            }

            //average payperiod hours entries, for week1 and week2
            $averagePayperiodEntries = EmployeeScheduleAveragePayperiodHours::insert([[
                'employee_schedule_id' => $lastInsertScheduleId,
                'payperiod_id' => $payPeriodId,
                'average_hours' => $avgHoursPerWeekArray[$payPeriodId]['weekOneHours'],
                'contractual_hours' => 0.0,
                'week' => 1,
                'created_at' => Carbon::now(),
            ], [
                'employee_schedule_id' => $lastInsertScheduleId,
                'payperiod_id' => $payPeriodId,
                'average_hours' => $avgHoursPerWeekArray[$payPeriodId]['weekTwoHours'],
                'contractual_hours' => 0.0,
                'week' => 2,
                'created_at' => Carbon::now(),
            ]]);
        }
    }

    public function getCustomerList()
    {
        if (\Auth::user()->can('create_schedule_all_customer')) {
            $customersArray = null;
        } elseif (\Auth::user()->can('create_schedule_allocated_customer')) {
            $customersArray = $this->schedulingRepository->getAllocatedCustomerIds();
        } else {
            return [];
        }

        return $this->customerRepository->getCustomerList('ALL_CUSTOMER', 1, $customersArray);
    }
}
