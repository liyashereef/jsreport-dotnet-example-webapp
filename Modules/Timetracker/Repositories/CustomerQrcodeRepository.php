<?php

namespace Modules\Timetracker\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerQrcodeLocation;
use Modules\Admin\Models\QrPatrolSetting;
use Modules\Hranalytics\Models\QrPatrolLogs;
use Modules\Hranalytics\Models\QrPatrolEmployeeLogs;
use Modules\Admin\Models\QrPatrolWidgetEntry;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Models\CustomerQrcodeSummary;
use Modules\Timetracker\Models\CustomerQrcodeWithShift;
use Modules\Timetracker\Repositories\EmployeeShiftRepository;

class CustomerQrcodeRepository
{

    protected $customerQrcodeShift, $employeeShiftRepository, $logger;

    public function __construct(
        EmployeeAllocationRepository $employeeAllocationrepository,
        UserRepository $userRepository,
        User $userModel,
        CustomerQrcodeWithShift $customerQrcodeShift,
        CustomerQrcodeSummary $customerqrsummary,
        CustomerEmployeeAllocationRepository $customerRepository,
        EmployeeShiftRepository $employeeShiftRepository
    ) {
        $this->customerqrcodeshift = $customerQrcodeShift;
        $this->customerqrsummary = $customerqrsummary;
        $this->customerrepository = $customerRepository;
        $this->usermodel = $userModel;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->userRepository = $userRepository;
        $this->employeeShiftRepository = $employeeShiftRepository;

        $this->logger = Log::channel('summaryDashboardLog');
    }

    public function employeeLookUps()
    {
        $user_list = array();
        if (\Auth::user()->can('view_all_customer_qrcode_summary')) {
            $user_list = $this->userRepository->getUserLookup(null, ['admin', 'super_admin'], null, true, null, true)
                ->orderBy('first_name', 'asc')
                ->get();
        } else {
            $employees = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id);
            $user_list = $this->usermodel
                ->whereIn('id', $employees)
                ->get();
        }

        return $user_list;
    }

    /**
     *  To List Customer QR code with shifts
     *
     */

    function list($startdate = false, $enddate = false, $emp_id = null, $cmd = false, $customerId = null)
    {
        $logged_in_user = \Auth::user();
        $list = $this->customerqrsummary
            ->when($startdate, function ($q) use ($startdate) {
                return $q->where('created_at', '>=', $startdate);
            })
            ->when($enddate, function ($q) use ($enddate) {
                return $q->where('created_at', '<=', $enddate);
            })->when($emp_id != null && $emp_id != 0, function ($q) use ($emp_id) {
                $q->whereHas('shifts.shift_payperiod.trashed_user', function ($uq) use ($emp_id) {
                    return $uq->where('id', $emp_id);
                });
            });

        $list = $list->when($customerId != null && $customerId != 0, function ($q) use ($customerId) {
            $q->whereHas('shifts.shift_payperiod.trashed_customer', function ($uq) use ($customerId) {
                return $uq->where('id', $customerId);
            });
        });
        if ($cmd) {
            $list = $list->with([
                'shifts' => function ($q1) {
                    return $q1->select('id', 'employee_shift_payperiod_id', 'start');
                },
                'shifts.shift_payperiod' => function ($q2) {
                    return $q2->select('id', 'employee_id');
                },
                'shifts.shift_payperiod.trashed_user' => function ($q3) {
                    return $q3->select('id');
                },
                'shifts.shift_payperiod.trashed_user.trashedEmployee' => function ($q4) {
                    return $q4->select(
                        'id',
                        'user_id',
                        'employee_no'
                    );
                },
                'shifts.shift_payperiod.trashed_customer' => function ($q5) {
                    return $q5->select('id', 'client_name');
                },
                'qrcodeWithTrashed' => function ($q6) {
                    return $q6->select('*');
                },
                'qrcodeshiftWithTrashed' => function ($q7) {
                    return $q7->select('id', 'time', "user_id");
                }, 'qrcodeshiftdescWithTrashed' => function ($q8) {
                    return $q8->select('id', 'time', "user_id");
                }
            ]);
        } elseif ($logged_in_user->hasPermissionTo('view_all_customer_qrcode_summary')) {
            $list = $list->with([
                'qrcode' => function ($qrCode) {
                    return $qrCode->select(
                        '*'
                    );
                },
                'shifts' => function ($q1) {
                    return $q1->select('id', 'employee_shift_payperiod_id');
                },
                'shifts.shift_payperiod' => function ($q2) {
                    return $q2->select('id', 'customer_id', 'employee_id');
                },
                'shifts.shift_payperiod.trashed_user' => function ($q3) {
                    return $q3->select('id');
                },
                'shifts.shift_payperiod.trashed_user.trashedEmployee' => function ($q4) {
                    return $q4->select(
                        'id',
                        'user_id',
                        'employee_no'
                    );
                },
                'shifts.shift_payperiod.trashed_customer' => function ($q5) {
                    return $q5->select('id', 'client_name');
                },
                'qrcodeWithTrashed' => function ($q6) {
                    return $q6->select('*');
                },
                'qrcodeshiftWithTrashed' => function ($q7) {
                    return $q7->select('id', 'user_id', 'shift_id', 'qrcode_id', 'time', "customer_id");
                }, 'qrcodeshiftdescWithTrashed' => function ($q8) {
                    return $q8->select('id', 'user_id', 'shift_id', 'qrcode_id', 'time', "customer_id");
                }
            ]);
        } else if ($logged_in_user->hasPermissionTo('view_allocated_customer_qrcode_summary')) {
            $allocatedcustomers = $this->customerrepository->getAllAllocatedCustomerId([\Auth::User()->id]);
            $list = $list->whereHas('shifts.shift_payperiod', function ($query) use ($allocatedcustomers) {
                $query->whereIn('customer_id', $allocatedcustomers);
            })
                ->with([
                    'shifts' => function ($q1) {
                        return $q1->select('id', 'employee_shift_payperiod_id');
                    },
                    'shifts.shift_payperiod' => function ($q2) {
                        return $q2->select('id', 'employee_id');
                    },
                    'shifts.shift_payperiod.trashed_user' => function ($q3) {
                        return $q3->select('id');
                    },
                    'shifts.shift_payperiod.trashed_user.trashedEmployee' => function ($q4) {
                        return $q4->select(
                            'id',
                            'user_id',
                            'employee_no'
                        );
                    },
                    'shifts.shift_payperiod.trashed_customer' => function ($q5) {
                        return $q5->select('id', 'client_name');
                    },
                    'qrcodeWithTrashed' => function ($q6) {
                        return $q6->select('*');
                    },
                    'qrcodeshiftWithTrashed' => function ($q7) {
                        return $q7->select('*');
                    }, 'qrcodeshiftdescWithTrashed' => function ($q8) {
                        return $q8->select('id', 'time', "user_id");
                    }
                ]);
        } else {
            $logged_in_user_id = $logged_in_user->id;
            $list = $list->whereHas('shifts.shift_payperiod', function ($query) use ($logged_in_user_id) {
                $query->where('employee_id', $logged_in_user_id);
            })
                ->with([
                    'shifts' => function ($q1) {
                        return $q1->select('id', 'employee_shift_payperiod_id');
                    },
                    'shifts.shift_payperiod' => function ($q2) {
                        return $q2->select('id', 'employee_id');
                    },
                    'shifts.shift_payperiod.trashed_user' => function ($q3) {
                        return $q3->select('id');
                    },
                    'shifts.shift_payperiod.trashed_user.trashedEmployee' => function ($q4) {
                        return $q4->select(
                            'id',
                            'user_id',
                            'employee_no'
                        );
                    },
                    'shifts.shift_payperiod.trashed_customer' => function ($q5) {
                        return $q5->select('id', 'client_name');
                    },
                    'qrcodeWithTrashed' => function ($q6) {
                        return $q6->select('*');
                    },
                    'qrcodeshiftWithTrashed' => function ($q7) {
                        return $q7->select('id', 'time', "user_id");
                    }, 'qrcodeshiftdescWithTrashed' => function ($q8) {
                        return $q8->select('id', 'time', "user_id");
                    }
                ]);
        }
        return $list;
    }

    /**
     *  To List Customer QR code with shifts
     *
     */

    function widgetDataentryList(
        $stack,
        $startdate = false,
        $enddate = false,
        $emp_id = null,
        $cmd = false,
        $customerId = 0
    ) {
        //dd($startdate, $enddate);
        $list = CustomerQrcodeWithShift::with([
            'qrcode', 'QrcodeWithTrashed'
        ])->where("time", '>=', $startdate)
            ->where("time", '<=', $enddate)
            // ->where("customer_id", 87)
            ->orderBy("time", "asc")->get();
        return $list;
    }

    public function prepareDataForCustomerQrcodeWithShift($cus_qacode_list)
    {

        $datatable_rows = array();
        foreach ($cus_qacode_list as $key => $each_list) {
            $each_row['id'] = $each_list->id;
            $each_row['date'] = date('F d, Y', strtotime($each_list->created_at));
            $each_row['created_date'] = strtotime($each_list->created_at);
            $each_row['checkpoint'] = isset($each_list->qrcodeWithTrashed->location) ? $each_list->qrcodeWithTrashed->location : '--';
            $each_row['employee_details'] = data_get($each_list->shifts->shift_payperiod->trashed_user, 'name_with_emp_no');
            $each_row['customer_details'] = $each_list->shifts->shift_payperiod->trashed_customer->client_name;
            $each_row['total_count'] = $each_list->total_count;
            $each_row['expected_count'] = $each_list->expected_attempts;
            $each_row['percentage'] = number_format($each_list->missed_count_percentage, 2, '.', ',');
            if (substr($each_row['percentage'], strpos($each_row['percentage'], ".") + 1) == '00') {
                $each_row['percentage'] = intval($each_row['percentage']);
            }
            array_push($datatable_rows, $each_row);
        }

        return $datatable_rows;
    }

    public function getCustomerQrPatrolDetails($customerId, $employeeId)
    {
        $result = null;
        $qrPatrolSettings = QrPatrolSetting::first();
        if (empty($qrPatrolSettings)) {
            return [
                'rowData' => [],
                'headerData' => [],
            ];
        }

        $users = User::where("active", 1)->get();
        $userArray = [];
        foreach ($users as $user) {
            $userArray[$user->id] = $user->getFullNameAttribute();
        }

        $endDate = Carbon::today()->subDays(1)->addHours("23")->addMinutes(59);
        $startDate = Carbon::today()->subDays($qrPatrolSettings->days_prior);
        // $records = $this->list($startDate, $endDate, $employeeId, false, $customerId)
        //     ->when($customerId, function ($q) use ($customerId) {
        //         $q->whereHas('qrcode', function ($uq) use ($customerId) {
        //             return $uq->where('customer_id', $customerId);
        //         });
        //     })->get();

        // $records = QrPatrolLogs::

        //dates to array
        $dateArray = null;
        $startDateStr = strtotime($startDate);
        $endDateStr = strtotime($endDate);
        $iterationData = null;
        $queryDateArray = [];
        $formatedDateHeader = null;
        while ($startDateStr <= $endDateStr) {
            $iterationData = [
                'color' => 'red',
                'value' => 0.0,
                'required_scan' => 0,
                'actual_scan' => 0,
                'first_scan_at' => '',
                'first_scan_by' => '',
                'last_scan_at' => '',
                'last_scan_by' => '',
            ];
            $queryDateArray[] = date('Y-m-d', $endDateStr);
            $dateArray[date('d-M-y', $endDateStr)] = $iterationData;
            $formatedDateHeader[date('d-M-y', $endDateStr)] = date('l', $endDateStr);
            $endDateStr = strtotime('-1 day', $endDateStr);
        }
        $modelName = "QrPatrolLogs";

        if ($employeeId != null) {
            $records = QrPatrolEmployeeLogs::whereIn("date", $queryDateArray)
                ->where('user_id', intval($employeeId))
                ->where('customer_id', intval($customerId))
                ->get();
        } else {
            $records = QrPatrolLogs::whereIn("date", $queryDateArray)
                ->where('customer_id', intval($customerId))
                ->get();
        }

        //fetch customer checkpoints
        $checkPoints = CustomerQrcodeLocation::where('customer_id', $customerId)
            ->where('qrcode_active', true)
            ->where('deleted_at', null)
            ->orderBy('location', 'ASC')
            ->pluck('location')
            ->toArray();
        $qrCodeLocArray = [];
        $qrCodeLocs = CustomerQrcodeLocation::select("id", 'qrcode', 'location')->where('customer_id', $customerId)
            ->where('qrcode_active', true)
            ->where('deleted_at', null)
            ->orderBy('location', 'ASC')->get();
        foreach ($qrCodeLocs as $qrCodeLoc) {
            $qrCodeLocArray[$qrCodeLoc->id] = [
                "id" => $qrCodeLoc->id, 'qrcode' => $qrCodeLoc->qrcode, 'location' => $qrCodeLoc->location
            ];
        }
        if (!empty($checkPoints) && !empty($dateArray)) {
            $result = array_fill_keys($checkPoints, $dateArray);
        }
        if (!empty($records)) {
            $tempArray = [];
            $qrCodeLocationIdArray = [];
            $toolTipArray = [];
            $totalAttemptsByEmployeeShiftArray = [];
            foreach ($records as $qrCodeSummary) {

                // dd($qrCodeSummary);
                $qrCodeLocation = collect($qrCodeLocArray[$qrCodeSummary->qr_code_id]);
                // dd($qrCodeLoation);
                $date = $qrCodeSummary->date;
                $qrCodeLocationId = $qrCodeLocation["id"];



                $actualScan = $qrCodeSummary->actual_scan;
                $requiredScan = $qrCodeSummary->required_scan;
                $average = ($requiredScan > 0) ? round(($actualScan / $requiredScan) * 100) : 0;
                $average = ($average > 100) ? 100 : $average;

                if ($average < $qrPatrolSettings->critical_level_percentage) {
                    $color = 'red';
                } elseif ($average >= $qrPatrolSettings->acceptable_level_percentage) {
                    $color = 'green';
                } else {
                    $color = 'yellow';
                }


                if (!empty($qrCodeSummary)) {

                    $toolTipArray[$qrCodeLocation["location"]][$date] = [
                        'first_scan_at' => $qrCodeSummary->first_scan,
                        'last_scan_at' => $qrCodeSummary->last_scan,
                        'first_scan_by' => isset($userArray[$qrCodeSummary->first_scan_by]) ? $userArray[$qrCodeSummary->first_scan_by] : "",
                        'last_scan_by' => isset($userArray[$qrCodeSummary->last_scan_by]) ? $userArray[$qrCodeSummary->last_scan_by] : "",
                    ];
                }

                $iterationArray[$date] = [
                    'color' => $color,
                    'value' => $average,
                    'required_scan' => $requiredScan,
                    'actual_scan' => $actualScan,
                    'first_scan_at' => $qrCodeSummary->first_scan,
                    'first_scan_by' => isset($userArray[$qrCodeSummary->first_scan_by]) ? $userArray[$qrCodeSummary->first_scan_by] : "",
                    'last_scan_at' => $qrCodeSummary->last_scan,
                    'last_scan_by' => isset($userArray[$qrCodeSummary->last_scan_by]) ? $userArray[$qrCodeSummary->last_scan_by] : ""
                ];

                $mergeArray = [
                    'color' => $color,
                    'value' => $average,
                    'required_scan' => $requiredScan,
                    'actual_scan' => $actualScan,
                    'first_scan_at' => date("h:i A", strtotime($qrCodeSummary->first_scan)),
                    'first_scan_by' => isset($userArray[$qrCodeSummary->first_scan_by]) ? $userArray[$qrCodeSummary->first_scan_by] : "",
                    'last_scan_at' => date("h:i A", strtotime($qrCodeSummary->last_scan)),
                    'last_scan_by' => isset($userArray[$qrCodeSummary->last_scan_by]) ? $userArray[$qrCodeSummary->last_scan_by] : ""
                ];

                if ($date == "2021-06-20") {
                    //dd($qrCodeLocation["location"]);
                    // dump($iterationArray,  $qrCodeLocation["location"]);
                }
                $qrCodeLocationIdArray[$qrCodeLocationId] = $iterationArray;
                // $indDateFormated =
                // $mergeArray = [$date => $iterationArray];
                $result[$qrCodeLocation["location"]][date("d-M-y", strtotime($date))] = $mergeArray;
            }
            unset($tempArray);
            unset($iterationArray);
        }
        return [
            'rowData' => $result,
            'headerData' => !empty($dateArray) ? array_keys($dateArray) : [],
            'formatedDateHeader' => $formatedDateHeader,
        ];
    }

    public function widgetEntries($start_Date = null, $end_Date = null)
    {
        $noOfDays = 1;
        if ($start_Date != null) {
            $startDate = Carbon::parse($start_Date);
            $endDate = Carbon::parse($end_Date);
            $sDate = Carbon::parse($start_Date);
            $eDate = Carbon::parse($end_Date);
        } else {
            $startDate = Carbon::today()->subDays($noOfDays);
            $endDate = Carbon::today();

            $sDate = Carbon::today()->subDays($noOfDays);
            $eDate = Carbon::today();
        }
        $stack = [];

        $date = $startDate;
        while ($date < $endDate) {

            $stack[] = $date->copy()->format("Y-m-d");
            $date->addDays(1);
        }
        $finalDataArray = [];
        $QrPatrolLogs = QrPatrolLogs::whereIn("date", $stack)
            ->delete();
        $QrPatrolEmployeeLogs = QrPatrolEmployeeLogs::whereIn("date", $stack)
            ->delete();
        $records = $this->widgetDataentryList($stack, $sDate, $eDate, null, false, null);
        $generalArray = [];
        $employeeWiseArray = [];
        foreach ($records as $record) {
            if (isset($record->Qrcode)) {
                $dayString = \Carbon\Carbon::parse($record->time)->format('l');
                $expectedAttempts = 0;
                $expectedAttemptsEmployee = 0;
                if ($dayString == "Saturday" || $dayString == "Sunday") {
                    $expectedAttempts = $record->QrcodeWithTrashed->tot_no_of_attempts_week_ends;
                    $expectedAttemptsEmployee = $record->QrcodeWithTrashed->no_of_attempts_week_ends;
                } else {
                    $expectedAttempts =  $record->QrcodeWithTrashed->tot_no_of_attempts_week_day;
                    $expectedAttemptsEmployee =  $record->QrcodeWithTrashed->no_of_attempts;
                }

                $tot_no_of_attempts_week_day = $record->QrcodeWithTrashed->tot_no_of_attempts_week_day;
                $tot_no_of_attempts_week_ends = $record->QrcodeWithTrashed->tot_no_of_attempts_week_ends;



                # code...
                $no_of_attempts = intval($record->no_of_attempts);
                $first_scan = null;
                $first_scan_by = null;
                $first_scan = null;
                $first_scan_by = null;
                $last_scan = null;
                $last_scan_by = null;
                $date = null;
                $indexDate = date("Y-m-d", strtotime($record->time));

                if (isset($generalArray[$record->QrcodeWithTrashed->customer_id][$record->qrcode_id][$indexDate])) {
                    $last_scan = $record->time;
                    $last_scan_by = $record->user_id;
                    $generalArray[$record->QrcodeWithTrashed->customer_id][$indexDate]["actual_scan"] =
                        intval($generalArray[$record->QrcodeWithTrashed->customer_id][$record->qrcode_id][$indexDate]["actual_scan"]) + 1;
                    if ($expectedAttempts <= $generalArray[$record->QrcodeWithTrashed->customer_id][$indexDate]["actual_scan"]) {
                        $compliance = 100;
                    } else {
                        $compliance = ($generalArray[$record->QrcodeWithTrashed->customer_id][$indexDate]["actual_scan"] / $expectedAttempts) * 100;
                    }
                    $generalArray[$record->QrcodeWithTrashed->customer_id][$record->qrcode_id][$indexDate]["compliance_value"] =
                        ($expectedAttempts / $generalArray[$record->QrcodeWithTrashed->customer_id][$indexDate]["actual_scan"]) * 100;

                    $generalArray[$record->QrcodeWithTrashed->customer_id][$record->qrcode_id][$indexDate]["last_scan"] = $last_scan;
                    $generalArray[$record->QrcodeWithTrashed->customer_id][$record->qrcode_id][$indexDate]["last_scan_by"] = $last_scan_by;

                    $finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["actual_scan"] =
                        intval($finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["actual_scan"]) + 1;
                    if ($expectedAttempts <= $finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["actual_scan"]) {
                        $finalCompliance = 100;
                    } else {
                        $finalCompliance = ($finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["actual_scan"] / $expectedAttempts) * 100;
                    }

                    $finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["compliance_value"] =
                        $finalCompliance;

                    $finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["last_scan"] = $last_scan;
                    $finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate]["last_scan_by"] = $last_scan_by;
                } else {

                    $first_scan = $record->time;
                    $first_scan_by = $record->user_id;
                    $last_scan = $record->time;
                    $last_scan_by = $record->user_id;
                    $compliance = (1 / $expectedAttempts) * 100;
                    $date = date("Y-m-d H:i A", strtotime($record->time));
                    $indexDate = date("Y-m-d", strtotime($record->time));
                    $generalArray[$record->QrcodeWithTrashed->customer_id][$record->qrcode_id][$indexDate] = [
                        'customer_id' => $record->QrcodeWithTrashed->customer_id,
                        'qr_code_id' => $record->qrcode_id,
                        'date' => date("Y-m-d", strtotime($record->time)),
                        'required_scan' => intval($expectedAttempts),
                        'actual_scan' => 1,
                        'compliance_value' => $compliance,
                        'first_scan' => $first_scan,
                        'first_scan_by' => $first_scan_by,
                        'last_scan' => $last_scan,
                        'last_scan_by' => $last_scan_by,
                        "created_at" => \Carbon::now()->format("Y-m-d h:i:s"),
                        "updated_at" => \Carbon::now()->format("Y-m-d h:i:s")
                    ];
                    $finalDataArray[$record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $indexDate] = [
                        'customer_id' => $record->QrcodeWithTrashed->customer_id,
                        'qr_code_id' => $record->qrcode_id,
                        'date' => date("Y-m-d", strtotime($record->time)),
                        'required_scan' => intval($expectedAttempts),
                        'actual_scan' => 1,
                        'compliance_value' => $compliance,
                        'first_scan' => $first_scan,
                        'first_scan_by' => $first_scan_by,
                        'last_scan' => $last_scan,
                        'last_scan_by' => $last_scan_by,
                        "created_at" => \Carbon::now()->format("Y-m-d h:i:s"),
                        "updated_at" => \Carbon::now()->format("Y-m-d h:i:s")
                    ];
                }

                $employeeExpression = $record->QrcodeWithTrashed->customer_id . "_" . $record->qrcode_id . "_" . $record->user_id . "_" . $indexDate;
                if (isset($employeeWiseArray[$employeeExpression])) {
                    $last_scan = $record->time;
                    $last_scan_by = $record->user_id;

                    $employeeWiseArray[$employeeExpression]["actual_scan"] =
                        intval($employeeWiseArray[$employeeExpression]["actual_scan"]) + 1;
                    if ($expectedAttemptsEmployee <= $employeeWiseArray[$employeeExpression]["actual_scan"]) {
                        $finalCompliance = 100;
                    } else {
                        $finalCompliance = ($employeeWiseArray[$employeeExpression]["actual_scan"] / $expectedAttemptsEmployee) * 100;
                    }
                    if ($employeeExpression == "195_471_1357_2021-06-18") {
                        // dump($expectedAttemptsEmployee, $finalCompliance, $employeeWiseArray[$employeeExpression]["actual_scan"], "/");
                    }
                    $employeeWiseArray[$employeeExpression]["compliance_value"] =
                        $finalCompliance;

                    $employeeWiseArray[$employeeExpression]["last_scan"] = $last_scan;
                    $employeeWiseArray[$employeeExpression]["last_scan_by"] = $last_scan_by;
                } else {

                    $first_scan = $record->time;
                    $first_scan_by = $record->user_id;
                    $last_scan = $record->time;
                    $last_scan_by = $record->user_id;
                    $compliance = (1 / $expectedAttemptsEmployee) * 100;
                    $date = date("Y-m-d H:i A", strtotime($record->time));
                    $indexDate = date("Y-m-d", strtotime($record->time));
                    $employeeWiseArray[$employeeExpression] = [
                        'customer_id' => $record->QrcodeWithTrashed->customer_id,
                        'qr_code_id' => $record->qrcode_id,
                        'user_id' => $record->user_id,
                        'date' => date("Y-m-d", strtotime($record->time)),
                        'required_scan' => intval($expectedAttemptsEmployee),
                        'actual_scan' => 1,
                        'compliance_value' => $compliance,
                        'first_scan' => $first_scan,
                        'first_scan_by' => $first_scan_by,
                        'last_scan' => $last_scan,
                        'last_scan_by' => $last_scan_by,
                        "created_at" => \Carbon::now()->format("Y-m-d h:i:s"),
                        "updated_at" => \Carbon::now()->format("Y-m-d h:i:s")
                    ];
                    if ($employeeExpression == "195_473_1357_2021-06-18") {
                    }
                }
            }
        }
        $insertLogArray = (array_values($finalDataArray));
        $insertEmployeeLogArray = (array_values($employeeWiseArray));
        try {
            if (count($insertLogArray) > 0) {
                QrPatrolLogs::insert($insertLogArray);
                QrPatrolEmployeeLogs::insert($insertEmployeeLogArray);
            }
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /*
     * show qr patrol widget details by param
     * @param $customerId
     * @param $employeeId
     * @return json
     */
    // public function getCustomerQrPatrolDetails($customerId, $employeeId)
    // {
    //     $result = null;
    //     $qrPatrolSettings = QrPatrolSetting::first();
    //     if (empty($qrPatrolSettings)) {
    //         return [
    //             'rowData' => [],
    //             'headerData' => [],
    //         ];
    //     }

    //     $endDate = Carbon::today()->subDays(1)->endOfDay();
    //     $startDate = Carbon::today()->subDays($qrPatrolSettings->days_prior)->startOfDay();
    //     $records = QrPatrolWidgetEntry::where('customer_id', (int) $customerId)
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->when(($employeeId != null), function ($qry) use ($employeeId) {
    //             return $qry->where('user_id', (int) $employeeId)
    //                 ->where('type', true);
    //         }, function ($qry) {
    //             return $qry->where('type', false);
    //         })
    //         ->get();

    //     // dd($records);

    //     //dates to array
    //     $dateArray = null;
    //     $startDateStr = strtotime($startDate);
    //     $endDateStr = strtotime($endDate);
    //     $iterationData = null;
    //     $formattedDateHeader = null;
    //     while ($startDateStr <= $endDateStr) {
    //         $iterationData = [
    //             'color' => 'red',
    //             'value' => 0.0,
    //             'required_scan' => 0,
    //             'actual_scan' => 0,
    //             'first_scan_at' => '',
    //             'first_scan_by' => '',
    //             'last_scan_at' => '',
    //             'last_scan_by' => '',
    //         ];
    //         $dateArray[date('d-M-y', $endDateStr)] = $iterationData;
    //         $formattedDateHeader[date('d-M-y', $endDateStr)] = date('l', $endDateStr);
    //         $endDateStr = strtotime('-1 day', $endDateStr);
    //     }

    //     //fetch customer checkpoints
    //     $checkPoints = CustomerQrcodeLocation::where('customer_id', $customerId)
    //         ->where('qrcode_active', true)
    //         ->where('deleted_at', null)
    //         ->whereNotNull('location')
    //         ->orderBy('location', 'ASC')
    //         ->pluck('location')
    //         ->toArray();
    //     if (!empty($checkPoints) && !empty($dateArray)) {
    //         $result = array_fill_keys($checkPoints, $dateArray);
    //     }

    //     if (!empty($records)) {
    //         foreach ($records as $key => $record) {
    //             $dte = Carbon::parse($record->date)->format('d-M-y');
    //             $result[$record->qr_code['location']][$dte] = [
    //                 'color' => $record->color,
    //                 'value' => $record->percentage,
    //                 'required_scan' => $record->required_scan,
    //                 'actual_scan' => $record->actual_scan,
    //                 'first_scan_at' => $record->first_scan_at,
    //                 'first_scan_by' => ($record->first_scan_by_user && isset($record->first_scan_by_user['full_name'])) ? $record->first_scan_by_user['full_name'] : '',
    //                 'last_scan_at' => $record->last_scan_at,
    //                 'last_scan_by' => ($record->last_scan_by_user && isset($record->last_scan_by_user['full_name'])) ? $record->last_scan_by_user['full_name'] : '',
    //             ];
    //         }
    //     }

    //     return [
    //         'rowData' => $result,
    //         'headerData' => !empty($dateArray) ? array_keys($dateArray) : [],
    //         'formatedDateHeader' => $formattedDateHeader,
    //     ];
    // }

    /*
     * process qr patrol widget data
     * @return true
     */
    public function processQrPatrolWidgetEntries($startDate = null, $endDate = null, $cmd = false)
    {
        try {
            $this->logger->info('--------------------------------------------------');
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job started');

            $this->logger->info('---Inside----- processQrPatrolWidgetEntries ');

            $status = $this->makeQrPatrolWidgetEntries($cmd, null, $startDate, $endDate);

            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job finished');
        } catch (\Exception $e) {
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job failed - ' . $e->getMessage() . ' get-line number ' . $e->getLine());
        } finally {
            return true;
        }
    }

    /*
     * make qr patrol widget entries by params
     * @param $customerId
     * @param $employeeId
     * @return true
     */
    public function makeQrPatrolWidgetEntries($cmd, $employeeId = null, $startDate = null, $endDate = null)
    {
        $qrPatrolSettings = QrPatrolSetting::first();
        if (!empty($qrPatrolSettings) && (empty($startDate) || empty($endDate))) {
            //End date as current date
            $endDate = Carbon::today()->subDays(1)->endOfDay();
            //Calculate Start date from qr-patrol settings
            $startDate = Carbon::today()->subDays($qrPatrolSettings->days_prior)->startOfDay();
        }
        //fetch customerqrsummary by start date, end date, customer, employee filters
        $records = $this->list($startDate, $endDate, $employeeId, $cmd)->orderBy('created_at', 'ASC')->get();

        if (!empty($records)) {
            foreach ($records as $qrCodeSummary) {
                $qrCodeLocation = $qrCodeSummary->qrcode;
                $dateObject = $qrCodeSummary->created_at;

                if (!empty($qrCodeLocation) && ($qrCodeLocation->qrcode_active)) {
                    QrPatrolWidgetEntry::updateOrCreate([
                        'customer_id' => $qrCodeLocation->customer_id,
                        'qr_code_id' => $qrCodeLocation->id,
                        'date' => $dateObject,
                    ], [
                        'date' => $dateObject,
                        'required_scan' => $qrCodeSummary->expected_attempts,
                        'actual_scan' => $qrCodeSummary->total_count,
                        'value' => ($qrCodeSummary->expected_attempts > 0) ? (($qrCodeSummary->total_count / $qrCodeSummary->expected_attempts) * 100) : 0,
                        'customer_id' => $qrCodeLocation->customer_id,
                        'qr_code_id' => $qrCodeLocation->id,
                    ]);
                }
            }
        }
        return true;
    }
}
