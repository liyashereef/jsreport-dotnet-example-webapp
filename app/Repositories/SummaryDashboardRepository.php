<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Models\QrPatrolWidgetEntry;
use Modules\Admin\Models\SummaryDashboardData;
use Modules\Admin\Models\SummaryDashboardMaster;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Client\Models\ClientConcern;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Hranalytics\Models\EmployeeExitInterview;
use Modules\Hranalytics\Models\Job;
use Modules\Hranalytics\Models\UserRating;
use Modules\KPI\Models\KpiData;
use Modules\KPI\Services\KpiFrequencyMap;
use Modules\Recruitment\Models\RecJob;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;

class SummaryDashboardRepository
{

    protected $pay_period_repository;
    protected $customer_report_repository;
    protected $schedulingRepository;
    protected $customerMapRepository;
    protected $customerEmployeeAllocationRepository;
    protected $logger;
    protected $employeeAllocationRepository;
    public function __construct(
        CustomerRepository $customerRepository,
        PayPeriodRepository $pay_period_repository,
        CustomerReportRepository $customer_report_repository,
        SchedulingRepository $schedulingRepository,
        EmployeeAllocationRepository $employeeAllocationRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->pay_period_repository = $pay_period_repository;
        $this->customer_report_repository = $customer_report_repository;
        $this->schedulingRepository = $schedulingRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->customerMapRepository = app()->make(CustomerMapRepository::class);

        $this->logger = Log::channel('summaryDashboardLog');
    }

    public function getKpiDataByParams($startDate, $endDate, $customerIds = [])
    {
        $qry = KpiData::whereBetween('process_date', [$startDate, $endDate]);
        if (!empty($customerIds)) {
            $customerIdArray = array_map('intval', $customerIds);
            $qry->whereIn("customer_id", $customerIdArray);
        } else if (empty($customerIds) && !\Auth::user()->hasAnyPermission([
            "super_admin",
            "admin"
        ])) {
            $customerIdArray = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
            $qry->whereIn('customer_id', $customerIdArray);
        }
        return $qry->get();
    }

    public function fetchOperationsDashboardMatrix($key, $customerIds, $payperiodIds)
    {
        $qry = SummaryDashboardData::where('sd_id', $key)
            ->whereIn('payperiod_id', $payperiodIds);
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry->whereIn("customer_id", $customerIds);
        } else if (empty($customerIds) && !\Auth::user()->hasAnyPermission([
            "super_admin",
            "admin"
        ])) {
            $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
            $qry->whereIn('customer_id', $customerIds);
        }
        return $qry->orderBy('process_date', 'DESC')->get();
    }

    public function fetchClientConcernCount($customerIds, $startDate, $endDate)
    {
        $qry = ClientConcern::whereBetween('created_at', [$startDate, $endDate]);
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry = $qry->whereIn('customer_id', $customerIds);
        } elseif (!(\Auth::User()->can('view_all_client_concern'))) {
            $allocatedCustomerIdArray = $this->schedulingRepository->getAllocatedCustomerIds();
            $qry->whereIn('customer_id', $allocatedCustomerIdArray);
        }
        return $qry->count();
    }

    public function fetchIncidentsCount($customerIds, $startDate, $endDate)
    {
        $qry = IncidentReport::whereHas('payperiod')
            ->whereHas('latestStatus.incidentStatusList', function ($q) {
                $q->where('status', "Open");
            })->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()]);
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry->whereIn('customer_id', $customerIds);
        } elseif (!(\Auth::User()->can('view_all_incident_report'))) {
            $allocatedCustomerIdArray = $this->schedulingRepository->getAllocatedCustomerIds();
            $qry->whereIn('customer_id', $allocatedCustomerIdArray);
        }
        return $qry->count();
    }

    public function getEmployeeTotalWorkHours($customerIds, $startDate, $endDate)
    {
        $userId = null;
        $result = ['hours' => 0, 'minutes' => 0, 'earned_billing_amount' => 0];
        if ((!(\Auth::User()->can('supervisor')))
            && (!(\Auth::User()->can('area_manager')))
        ) {
        } elseif ((!(\Auth::User()->can('supervisor')))
            && (!(\Auth::User()->can('area_manager')))
        ) {
            $userId = \Auth::User()->id;
        }

        $workHourSettingsId = SummaryDashboardMaster::where('machine_name', 'total-work-hours')->pluck('id');
        $earnedBillingSettingsId = SummaryDashboardMaster::where('machine_name', 'earned-billings')->pluck('id');

        $payperiodIdsByDateRange = $this->pay_period_repository->getPayperiodIdArrayInRange($startDate, $endDate);
        $qry = SummaryDashboardData::whereIn('payperiod_id', $payperiodIdsByDateRange);
        if (!empty($userId)) {
            $qry->whereIn('user_id', $userId);
        }
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry->whereIn('customer_id', $customerIds);
        } else if (empty($customerIds) && !\Auth::user()->hasAnyPermission([
            "super_admin",
            "admin"
        ])) {
            $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
            $qry->whereIn('customer_id', $customerIds);
        }
        $totalMinutes = $qry->where('sd_id', $workHourSettingsId[0])->get()->sum('value');

        $qry = SummaryDashboardData::whereIn('payperiod_id', $payperiodIdsByDateRange);
        if (!empty($userId)) {
            $qry->whereIn('user_id', $userId);
        }
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry->whereIn('customer_id', $customerIds);
        } else if (empty($customerIds) && !\Auth::user()->hasAnyPermission([
            "super_admin",
            "admin"
        ])) {
            $customerIds = $this->customerEmployeeAllocationRepository->getAllocatedCustomers(\Auth::user());
            $qry->whereIn('customer_id', $customerIds);
        }
        $earnedBillingAmount = $qry->where('sd_id', $earnedBillingSettingsId[0])->get()->sum('value');
        $result['hours'] = intdiv($totalMinutes, 60);
        $result['minutes'] = ($totalMinutes % 60);
        $result['earned_billing_amount'] = (int) $earnedBillingAmount;
        return $result;
    }

    public function fetchSummaryDashboardData($keyArray, $startDate, $endDate, $customerIds = [], $viewAllPermission = '')
    {
        $qry = SummaryDashboardData::whereIn('sd_id', $keyArray)
            // ->whereBetween('process_date', [$startDate, $endDate])
            ->where('value', '>', 0)
            ->where('process_date', '<=',  $endDate->format("Y-m-d"))
            ->where('process_date', '>=', $startDate->format("Y-m-d"));
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry->whereIn('customer_id', $customerIds);
        } elseif (($viewAllPermission != '') && (!(\Auth::User()->can($viewAllPermission)))) {
            $allocatedCustomerIdArray = $this->schedulingRepository->getAllocatedCustomerIds();
            $qry->whereIn('customer_id', $allocatedCustomerIdArray);
        }
        return $qry->get();
    }

    public function fetchJobTicketCount($customerIds, $startDate, $endDate)
    {
        $count = 0;

        //Old DB
        $qry = Job::whereActive(true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved');

        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry = $qry->whereIn('customer_id', $customerIds);
        } elseif (!(\Auth::User()->can('list-jobs-from-all'))) {
            $allocatedCustomerIdArray = $this->schedulingRepository->getAllocatedCustomerIds();
            $qry->whereIn('customer_id', $allocatedCustomerIdArray);
        }
        $count += $qry->count();

        //Recruitment DB
        $qry = RecJob::whereActive(true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved');

        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry = $qry->whereIn('customer_id', $customerIds);
        } elseif (!(\Auth::User()->can('list-jobs-from-all'))) {
            $allocatedCustomerIdArray = $this->schedulingRepository->getAllocatedCustomerIds();
            $qry->whereIn('customer_id', $allocatedCustomerIdArray);
        }

        $count += $qry->count();

        return $count;
    }

    public function fetchGuardTourCompliance($customerIds, $startDate, $endDate)
    {
        $qry = QrPatrolWidgetEntry::whereBetween('date', [$startDate, $endDate]);
        if (!empty($customerIds)) {
            $customerIds = array_map('intval', $customerIds);
            $qry->whereIn('customer_id', $customerIds);
        } elseif (empty($customerIds) &&  !\Auth::user()->hasAnyPermission([
            "super_admin",
            "admin"
        ])) {
            $customerIds = $this->schedulingRepository->getAllocatedCustomerIds();
            $qry->whereIn('customer_id', $customerIds);
        }
        $qrPatrol = $qry->get();
        return $qrPatrol;
    }

    //crone for site turn over
    public function croneSiteTurnOver($startDate = null, $endDate = null)
    {
        $result = [];
        $exitInterviews = EmployeeExitInterview::with('customer.customerEmployeeAllocation.user.roles.permissions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        if (!empty($exitInterviews)) {
            foreach ($exitInterviews as $exitInterview) {
                $i = 0;
                foreach ($exitInterview->customer->customerEmployeeAllocation as $allocation) {
                    if ($allocation->user && $allocation->user->roles) {
                        foreach ($allocation->user->roles[0]->permissions as $permission) {
                            if (in_array($permission->name, ['supervisor', 'guard', 'Spares Pool'])) {
                                $i++;
                            }
                        }
                    }
                }

                $dateString = Carbon::parse($exitInterview->created_at)->format('d-m-Y');
                if (array_key_exists($exitInterview->project_id, $result) && array_key_exists($dateString, $result[$exitInterview->project_id])) {
                    $result[$exitInterview->project_id][$dateString]['count'] = ($result[$exitInterview->project_id][$dateString]['count'] + 1);
                    $result[$exitInterview->project_id][$dateString]['value'] = (($i > 0) ? (($result[$exitInterview->project_id][$dateString]['count'] / $i) * 100) : 0);
                } else {
                    $result[$exitInterview->project_id][$dateString] = ['count' => 1, 'allocation_count' => $i, 'date' => $exitInterview->created_at, 'value' => (($i > 0) ? round(((1 / $i) * 100)) : 0)];
                }
            }

            $summaryDashboardMasterObj = SummaryDashboardMaster::where('machine_name', 'site-turn-over')->first();
            foreach ($result as $prjId => $items) {
                foreach ($items as $ky => $item) {
                    SummaryDashboardData::updateOrCreate(
                        [
                            'customer_id' => $prjId,
                            'sd_id' => $summaryDashboardMasterObj->id,
                            'process_date' => $item['date'],
                        ],
                        [
                            'sd_id' => $summaryDashboardMasterObj->id,
                            'customer_id' => $prjId,
                            'value' => $item['value'],
                            'created_at' => Carbon::now(),
                            'process_date' => $item['date'],
                        ]
                    );
                }
            }
        }

        return true;
    }

    //crone for operations dashboard matrix
    public function croneOperationsDashboard($startDate = null, $endDate = null)
    {

        $datas = [];
        $currentPayPeriod = [];
        $previousPayPeriod = [];
        $runPreviousPayPeriodMetric = false;
        if (!empty($startDate) && !empty($endDate)) {
            $payPeriodList = $this->pay_period_repository->getPayperiodRangeAll($startDate, $endDate);
        } else {
            $today = Carbon::now()->startOfDay();
            $payPeriodList = $this->pay_period_repository->getLastNthPayPeriodsByEndDate($today, 2);
        }
        $reportKeyList = TemplateQuestionsCategory::withTrashed()->get()->pluck('id', 'description')->toArray();
        $customerIds = $this->customerRepository->getAllCustomers();
        $reportKeys = [];
        $summaryDashboardMasterObj = SummaryDashboardMaster::where('machine_name', 'operational-dashboard-matrix')->first();
        // $customers = $this->options->allCustomers;
        $customers = $this->customerRepository->getAllShowSiteDashboardEnabled();
        /* Fetching all yestordays updated site metric entries. */
        $filters['date'] = Carbon::now()->subDays(1)->format("Y-m-d");
        if ($startDate != null) {
            $filters['start_date'] = Carbon::parse($startDate)->format("Y-m-d");
            $filters['date'] = null;
        }
        if ($endDate != null) {
            $filters['end_date'] = Carbon::parse($endDate)->format("Y-m-d");
            $filters['date'] = null;
        }
        $templateDetails = $this->customer_report_repository->getCustomerDetails($filters);
        foreach ($templateDetails as $template) {
            $report = $this->customerMapRepository->getPayperiodAvgReport(
                $template->customer_id,
                $template->payperiod_id,
                [$template->id]
            );
            if (empty($report)) {
                continue;
            }
            if (isset($report['score']) && isset($report['score']['total'])) {
                foreach ($report["score"] as $ky => $score) {
                    if (array_key_exists($ky, $reportKeyList)) {
                        SummaryDashboardData::updateOrCreate(
                            array(
                                'sd_id' => $summaryDashboardMasterObj->id,
                                'customer_id' => $template->customer_id,
                                'payperiod_id' => $template->payperiod_id,
                                'category_id' => $reportKeyList[$ky]
                            ),
                            [
                                'sd_id' => $summaryDashboardMasterObj->id,
                                'customer_id' => $template->customer_id,
                                'value' => $score,
                                'created_at' => Carbon::now(),
                                'category_id' => $reportKeyList[$ky],
                                'payperiod_id' => $template->payperiod_id,
                            ]
                        );
                    }
                }
            }
        }

        // dd("here");
        // foreach ($customerIds as $customerId) {
        //     $reportColor = [];
        //     $payperiods = [];
        //     foreach ($payPeriodList as $key => $eachList) {
        //         $report_arr = $this->customer_report_repository->customerPayperiodTrendReport(
        //             $customerId,
        //             $eachList->start_date,
        //             $eachList->end_date
        //         );

        //         // $trend_report = $this->customerMapRepository->getPayperiodAvgReport(
        //         //     $customerId,
        //         //     $eachList->payperiod_id,
        //         //     [$template->id]
        //         // );
        //         array_push($payperiods, $eachList->id);
        //         array_push($reportColor, $report_arr['average_report']);
        //     }
        //     if (!empty($reportColor)) {
        //         foreach ($reportColor as $key => $report) {
        //             if (empty($report)) {
        //                 continue;
        //             }
        //             foreach ($report["score"] as $ky => $score) {
        //                 if ($score > 0) {
        //                     dd("Hi", $reportKeyList);
        //                 }
        //                 if (array_key_exists($ky, $reportKeyList)) {
        //                     SummaryDashboardData::updateOrCreate(
        //                         array(
        //                             'sd_id' => $summaryDashboardMasterObj->id,
        //                             'customer_id' => $customerId,
        //                             'payperiod_id' => $payperiods[$key],
        //                             'category_id' => $reportKeyList[$ky]
        //                         ),
        //                         [
        //                             'sd_id' => $summaryDashboardMasterObj->id,
        //                             'customer_id' => $customerId,
        //                             'value' => $score,
        //                             'created_at' => Carbon::now(),
        //                             'category_id' => $reportKeyList[$ky],
        //                             'payperiod_id' => $payperiods[$key],
        //                         ]
        //                     );
        //                 }
        //             }
        //         }
        //     }
        // }
        return true;
    }

    public function croneTotalWorkHoursVsEarnedBilling($startDate = null, $endDate = null)
    {

        $dateStringFromSettings = strtotime(config('globals.earned_billing_table_switch_date'));
        $daysFromSettings = (int) config('globals.earned_billing_table_switch_regular_update_upto_days');
        if ((empty($startDate) || empty($endDate))) {
            $endDate = Carbon::today()->subDays(1)->endOfDay();
            $startDate = Carbon::today()->subDays(($daysFromSettings + 1))->startOfDay();
        }

        $workHourSettings = SummaryDashboardMaster::where('machine_name', 'total-work-hours')->first();
        $earnedBillingSettings = SummaryDashboardMaster::where('machine_name', 'earned-billings')->first();

        $i = 0;
        while (strtotime($endDate->format('Y-m-d')) >= strtotime($startDate->format('Y-m-d'))) {
            $payperiod = $this->pay_period_repository->getPayperiodByDate($endDate);

            if (!empty($payperiod)) {
                if ($dateStringFromSettings < strtotime($endDate->format('Y-m-d'))) {
                    $records = EmployeeShiftReportEntry::select('customer_id', 'user_id', DB::raw('sum(hours) as total_hours'), DB::raw('sum(total_amount) as final_amount'))
                        ->where('payperiod_id', $payperiod->id)
                        ->groupBy('customer_id', 'user_id')
                        ->get();

                    if (!empty($records)) {
                        foreach ($records as $record) {
                            //total work hours
                            SummaryDashboardData::updateOrCreate([
                                'sd_id' => $workHourSettings->id,
                                'customer_id' => $record->customer_id,
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ], [
                                'sd_id' => $workHourSettings->id,
                                'customer_id' => $record->customer_id,
                                'value' => ($record->total_hours > 0) ? ($record->total_hours) : 0,
                                'created_at' => Carbon::now(),
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ]);

                            //earned Billing
                            SummaryDashboardData::updateOrCreate([
                                'sd_id' => $earnedBillingSettings->id,
                                'customer_id' => $record->customer_id,
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ], [
                                'sd_id' => $earnedBillingSettings->id,
                                'customer_id' => $record->customer_id,
                                'value' => $record->final_amount,
                                'created_at' => Carbon::now(),
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ]);
                        }
                    }
                } else {
                    //for automated time sheet
                    $employeeShiftPayPeriod = EmployeeShiftPayperiod::with('earned_billing_amount')
                        ->whereHas('shifts')
                        ->whereActive(true)
                        ->whereApproved(true)
                        ->where('pay_period_id', $payperiod->id)
                        ->get();

                    if (!empty($employeeShiftPayPeriod)) {
                        foreach ($employeeShiftPayPeriod as $empShiftPayPeriod) {
                            $totalMinutes = 0;

                            if (!empty($empShiftPayPeriod->earned_billing_amount) && (isset($empShiftPayPeriod->earned_billing_amount[0]))) {
                                //earned Billing
                                SummaryDashboardData::updateOrCreate([
                                    'sd_id' => $earnedBillingSettings->id,
                                    'customer_id' => $empShiftPayPeriod->customer_id,
                                    'payperiod_id' => $payperiod->id,
                                    'user_id' => $empShiftPayPeriod->employee_id,
                                    'is_manual_entry' => false,
                                ], [
                                    'sd_id' => $earnedBillingSettings->id,
                                    'customer_id' => $empShiftPayPeriod->customer_id,
                                    'value' => $empShiftPayPeriod->earned_billing_amount[0]['amount'],
                                    'created_at' => Carbon::now(),
                                    'payperiod_id' => $payperiod->id,
                                    'user_id' => $empShiftPayPeriod->employee_id,
                                    'is_manual_entry' => false,
                                ]);
                            }
                            $totalStat = (!empty($empShiftPayPeriod->approved_total_statutory_hours)) ? $empShiftPayPeriod->approved_total_statutory_hours : "00:00";
                            list($statHour, $statMinute) = explode(':', $totalStat);
                            $totalMinutes += $statHour * 60;
                            $totalMinutes += $statMinute;

                            $totalReg = (!empty($empShiftPayPeriod->approved_total_regular_hours)) ? $empShiftPayPeriod->approved_total_regular_hours : "00:00";
                            list($regHour, $regMinute) = explode(':', $totalReg);
                            $totalMinutes += $regHour * 60;
                            $totalMinutes += $regMinute;

                            $totalOt = (!empty($empShiftPayPeriod->approved_total_overtime_hours)) ? $empShiftPayPeriod->approved_total_overtime_hours : "00:00";
                            list($otHour, $otMinute) = explode(':', $totalOt);
                            $totalMinutes += $otHour * 60;
                            $totalMinutes += $otMinute;

                            //total work hours
                            SummaryDashboardData::updateOrCreate([
                                'sd_id' => $workHourSettings->id,
                                'customer_id' => $empShiftPayPeriod->customer_id,
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $empShiftPayPeriod->employee_id,
                                'is_manual_entry' => false,
                            ], [
                                'sd_id' => $workHourSettings->id,
                                'customer_id' => $empShiftPayPeriod->customer_id,
                                'value' => $totalMinutes,
                                'created_at' => Carbon::now(),
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $empShiftPayPeriod->employee_id,
                                'is_manual_entry' => false,
                            ]);
                        }
                    }

                    //for manual
                    $records = EmployeeShiftReportEntry::select('customer_id', 'user_id', DB::raw('sum(hours) as total_hours'), DB::raw('sum(total_amount) as final_amount'))
                        ->where('is_manual', 1)
                        ->where('payperiod_id', $payperiod->id)
                        ->groupBy('customer_id', 'user_id')
                        ->get();

                    if (!empty($records)) {
                        foreach ($records as $record) {
                            //earned billings
                            SummaryDashboardData::updateOrCreate([
                                'sd_id' => $earnedBillingSettings->id,
                                'customer_id' => $record->customer_id,
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ], [
                                'sd_id' => $earnedBillingSettings->id,
                                'customer_id' => $record->customer_id,
                                'value' => $record->final_amount,
                                'created_at' => Carbon::now(),
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ]);

                            //total work hours
                            SummaryDashboardData::updateOrCreate([
                                'sd_id' => $workHourSettings->id,
                                'customer_id' => $record->customer_id,
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ], [
                                'sd_id' => $workHourSettings->id,
                                'customer_id' => $record->customer_id,
                                'value' => ($record->total_hours > 0) ? ($record->total_hours) : 0,
                                'created_at' => Carbon::now(),
                                'payperiod_id' => $payperiod->id,
                                'user_id' => $record->user_id,
                                'is_manual_entry' => true,
                            ]);
                        }
                    }
                }
            }

            $endDate = $endDate->subDays(1)->endOfDay();
        }
        return true;
    }

    public function guardPerfomanceCluster(Request $request)
    {
        // $cids = $request->input('cIds');
        // $date = $request->input('date');

        // if (empty($cids)) {
        //     $users  = $this->employeeAllocationRepository->getUserAllocationList(auth()->user()->id);
        //     $cids = array_pluck($users, 'id');
        // }

        // $q = CustomerEmployeeAllocation::withTrashed();

        // //From date same as month
        // if ($date != null) {
        //     $dt = Carbon::parse($date);
        //     $start = $dt->firstOfMonth();
        //     $end = (clone $dt)->endOfMonth();

        //     $q->whereDate('from', '<=', $end);
        //     $q->where(function ($query) use ($start) {
        //         $query->whereDate('to', '>=', $start)
        //             ->orWhereNull('to');
        //     });
        // }

        // $q->when($cids != null && !empty($cids), function ($q) use ($cids) {
        //     $q->whereIn('customer_id', $cids);
        // });

        // $ids = $q->get()->pluck('user_id')->toArray();
        // $ids = array_unique($ids);

        // $uq = UserRating::selectRaw('AVG(rating) average,user_id')
        //     ->whereIn('user_id', $ids)
        //     ->whereHas('user')
        //     ->whereHas('user.employee')
        //     ->with(['user', 'user.employee']);
        //     $uq->groupBy('user_id');

        // if ($cids != null && !empty($cids)) {
        //     $uq->whereIn('customer_id', $cids);
        // }

        // $res = $uq->get();

        $kpiRes = $request->input('split_up');
        $kpiRes = unserialize(Crypt::decryptString($kpiRes));

        $hasKpiUsers = (is_array($kpiRes) && !empty($kpiRes)) ? true : false;
        $res = [
            'list' => [],
            'custAgg' => [],
        ];

        $cusPool = [];

        if ($hasKpiUsers) {
            foreach ($kpiRes as $kr) {
                $user = User::where('id', $kr['user_id'])->withTrashed()->first();
                if ($user == null) {
                    continue;
                }
                if (!isset($cusPool[$kr['customer_id']])) {
                    $cusPool[$kr['customer_id']] = Customer::where('id', $kr['customer_id'])->withTrashed()->first();
                }
                $customer =  $cusPool[$kr['customer_id']];

                $o = [];
                //Rating from kpi
                $o['rating'] = number_format($kr['average'], 2);
                $o['customer_name'] = $customer ? $customer->customer_name_and_number : '--';
                $emp = $user->employee;
                $o['full_name'] = $user->full_name;
                $o['employee_no'] = $emp->employee_no;
                $o['phone'] = !empty($emp->phone) ? $emp->phone : '--';
                $o['employee_work_email'] = !empty($emp->employee_work_email) ? $emp->employee_work_email : '--';
                array_push($res['list'], $o);
            }

            //customer wise avg
            $custAgg = [];
            $col = collect($kpiRes)->groupBy('customer_id');

            foreach ($col as $dtCust => $r) {
                $customer =  $cusPool[$dtCust];
                $custAgg[] = [
                    'customer_name' => $customer ? $customer->customer_name_and_number : '--',
                    'average' => $r->avg('average')
                ];
            }
            $res['custAgg'] = $custAgg;
        }

        return $res;
    }
}
