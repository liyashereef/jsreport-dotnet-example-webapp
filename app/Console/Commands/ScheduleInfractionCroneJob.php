<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\SiteSettings;
use Modules\Admin\Models\SummaryDashboardData;
use Modules\Admin\Models\SummaryDashboardMaster;
use Modules\Employeescheduling\Models\EmployeeSchedule;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Timetracker\Models\EmployeeShift;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class ScheduleInfractionCroneJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SummaryDashboard:ScheduleInfractionCroneJob {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or replace schedule infraction entries by start, end date parameters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SchedulingRepository $schedulingRepository)
    {
        parent::__construct();
        $this->schedulingRepository = $schedulingRepository;
        $this->logger = Log::channel('summaryDashboardLog');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $originalInfractions = [];
        try {
            $this->logger->info('--------------------------------------------------');
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job started');

            $this->logger->info('---Inside----- croneScheduleInfraction ');
            if (!empty($this->option('start_date')) && !empty($this->option('end_date'))) {
                $startDate = Carbon::parse($this->option('start_date'))->startOfDay();
                $endDate = Carbon::parse($this->option('end_date'))->endOfDay();
            } else {
                $endDate = Carbon::today()->subDays(1)->endOfDay();
                $startDate = Carbon::parse($endDate)->startOfDay();
            }
            $qStartDate = $startDate->subDay(1)->toDateTimeString();
            $qEndDate = $endDate->toDateTimeString();

            $customers = Customer::where('active', 1)
                // ->whereIn("id", [232])
                ->orderBy('id', 'DESC')->pluck('id');
            if (!empty($customers)) {
                $infractionSettings = SummaryDashboardMaster::where('machine_name', 'schedule-infraction')->first();
                $siteSettings = SiteSettings::find(1);

                //calculation start
                $scheduleCol = EmployeeSchedule::where('status', 1)->get();
                $scheduleCustomers = array_unique(EmployeeSchedule::whereHas("scheduleTimeLogs", function ($q) use ($endDate, $startDate) {
                    return $q->where('schedule_date', '<=', Carbon::parse($endDate)->startOfDay())
                        ->where('schedule_date', '>=', Carbon::parse($startDate)->startOfDay());
                })->where('status', 1)->pluck("customer_id")->toArray());
                $scheQryCol = EmployeeScheduleTimeLog::with(['schedule'])
                    ->where('schedule_date', '<=', Carbon::parse($endDate)->startOfDay())
                    ->where('schedule_date', '>=', Carbon::parse($startDate)->startOfDay())
                    ->get();
                $summaryBasicData = [];
                // $scheduleCustomers = [232];
                $scheduleData = EmployeeSchedule::with('scheduleTimeLogs')->whereIn("customer_id", $scheduleCustomers)
                    ->whereHas("scheduleTimeLogs", function ($q) use ($endDate, $startDate) {
                        return $q->where('schedule_date', '<=', Carbon::parse($endDate)->startOfDay())
                            ->where('schedule_date', '>=', Carbon::parse($startDate)->startOfDay());
                        // return $q->whereBetween("schedule_date", [
                        //     Carbon::parse($endDate)->startOfDay(),
                        //     Carbon::parse($endDate)->endOfDay()
                        // ]);
                    })
                    ->where('status', 1)
                    ->get();
                while ((strtotime($endDate->format('Y-m-d')) > strtotime($startDate->format('Y-m-d')))) {
                    $actualDayEnd = Carbon::parse($endDate)->startOfDay();

                    $this->logger->info('SUMMARY-DASHBOARD-BULK:' . $actualDayEnd->toFormattedDateString());

                    //fetch all customers
                    foreach ($scheduleCustomers as $key => $customer) {
                        # code...
                        $summaryBasicData[] = [
                            'sd_id' => $infractionSettings->id,
                            'customer_id' => $customer,
                            'value' => 0,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'process_date' => $actualDayEnd->format("Y-m-d"),
                        ];
                    }
                    $maximumShiftStartTolerance = !empty($siteSettings) ? (($siteSettings->shift_start_time_tolerance + 1)) : 0;
                    $maximumShiftEndTolerance = !empty($siteSettings) ? (($siteSettings->shift_end_time_tolerance + 1)) : 0;


                    $endDate = Carbon::parse($actualDayEnd)->subDays(1)->endOfDay();
                }

                if (count($summaryBasicData) > 0) {
                    SummaryDashboardData::whereIn("customer_id", $scheduleCustomers)
                        ->where("sd_id", 2)
                        ->where('process_date', '<=',  $qEndDate)
                        ->where('process_date', '>=', date('Y-m-d', strtotime($qStartDate . ' -1 days')))
                        ->delete();
                    SummaryDashboardData::insert($summaryBasicData);
                }
                foreach ($scheduleData as $schedData) {
                    $customerId = $schedData->customer_id;
                    foreach ($schedData->scheduleTimeLogs as $scheduleVal) {
                        $i = 0;
                        if ($scheduleVal->schedule_date > $qStartDate  && $scheduleVal->schedule_date <= $qEndDate) {
                            $signInStartDate = \Carbon::parse($scheduleVal->start_datetime)->subHours(8);
                            $signOutEndDate = \Carbon::parse($scheduleVal->end_datetime)->addHours(8);
                            $shiftPayperiodIds = EmployeeShiftPayperiod::where('pay_period_id', $scheduleVal->payperiod_id)
                                ->where('employee_id', $scheduleVal->user_id)
                                ->where('customer_id', $customerId)
                                ->pluck('id')->toArray();

                            $shiftLateIn = EmployeeShift::select('start', \DB::raw('abs(floor((TIMESTAMPDIFF(SECOND,start,"' . $scheduleVal->start_datetime . '"))/60)) as diffminutes'))
                                ->whereIn('employee_shift_payperiod_id', $shiftPayperiodIds)
                                ->where('start', '>=', $signInStartDate)->where('start', '<=', $signOutEndDate)
                                ->orderBy(\DB::raw('abs(TIMESTAMPDIFF(SECOND,start,"' . $scheduleVal->start_datetime . '"))'), 'asc')
                                ->take(1)->get();

                            $shiftEarlyOut = EmployeeShift::select('end', \DB::raw('abs(floor((TIMESTAMPDIFF(SECOND,"' . $scheduleVal->end_datetime . '",end))/60)) as diffminutes'))
                                ->whereIn('employee_shift_payperiod_id', $shiftPayperiodIds)
                                ->where('end', '>=', $signInStartDate)->where('end', '<=', $signOutEndDate)
                                ->orderBy(\DB::raw('abs(TIMESTAMPDIFF(SECOND,"' . $scheduleVal->end_datetime . '",end))'), 'asc')
                                ->take(1)->get();

                            $result['late_in_minutes'] = (isset($shiftLateIn[0]) && ($shiftLateIn[0]->start > $scheduleVal->start_datetime)) ? $shiftLateIn[0]->diffminutes : 0;
                            $result['early_out_minutes'] = (isset($shiftEarlyOut[0]) && ($shiftEarlyOut[0]->end < $scheduleVal->end_datetime)) ? $shiftEarlyOut[0]->diffminutes : 0;
                            $actualIn = (isset($shiftLateIn[0])) ? \Carbon::parse($shiftLateIn[0]->start)->format('h:i A') : '-';
                            $actualOut = (isset($shiftEarlyOut[0])) ? \Carbon::parse($shiftEarlyOut[0]->end)->format('h:i A') : '-';

                            if ($scheduleVal->schedule_date == "2021-04-12" && $customerId == 232) {
                                // dump($shiftLateIn[0], $scheduleVal->start_datetime);
                            }
                            //type based conditions
                            $typeIds = [2, 7];
                            $popArray = $typeIds;





                            if (in_array(2, $typeIds) && (($result['late_in_minutes'] >= $maximumShiftStartTolerance))) {
                                $popArray = array_diff($popArray, array("2"));
                                $i++;
                            }

                            if (in_array(7, $typeIds) && (($result['early_out_minutes'] >= $maximumShiftEndTolerance))) {
                                $popArray = array_diff($popArray, array("7"));
                                $i++;
                            }
                            if ($i > 0) {

                                if (isset($originalInfractions[$customerId])) {
                                    if (isset($originalInfractions[$customerId][$scheduleVal->schedule_date])) {
                                        $originalInfractions[$customerId][$scheduleVal->schedule_date]["infraction"] =
                                            intval($originalInfractions[$customerId][$scheduleVal->schedule_date]["infraction"]) + $i;
                                    } else {
                                        $originalInfractions[$customerId][$scheduleVal->schedule_date]["infraction"] =  $i;
                                    }
                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["user_id"][] = $scheduleVal->user_id;
                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["process_date"] = $scheduleVal->schedule_date;
                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["customer_id"] = $customerId;
                                } else {
                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["customer_id"] = $customerId;

                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["infraction"] = $i;
                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["user_id"][] = $scheduleVal->user_id;
                                    $originalInfractions[$customerId][$scheduleVal->schedule_date]["process_date"] = $scheduleVal->schedule_date;
                                }
                            }
                            // if ($scheduleVal->schedule_date == "2021-03-12" && $customerId == 232 && $scheduleVal->user_id == 146) {
                            //     dump($result['early_out_minutes'], $customerId, $scheduleVal->user_id, $i);
                            // }


                            if (!empty($popArray)) {
                                // $i++;
                            }
                            $process_date = $scheduleVal->schedule_date;


                            $i = 0;
                        }
                    }
                }
                if (count($originalInfractions) > 0) {
                    foreach ($originalInfractions as $key => $originalInfractioncustomerwise) {
                        $customer_id = $key;
                        foreach ($originalInfractioncustomerwise as $processDateKey => $originalInfraction) {

                            $process_date = $processDateKey;
                            if ($process_date >= $qStartDate  && $scheduleVal->schedule_date <= $qEndDate) {

                                SummaryDashboardData::updateOrCreate([
                                    'customer_id' => $customer_id,
                                    'process_date' => $process_date,
                                ], [
                                    'value' => $originalInfraction["infraction"]
                                ]);
                            }
                        }
                    }
                }
                // SummaryDashboardData::updateOrCreate([
                //     'sd_id' => $infractionSettings->id,
                //     'customer_id' => $customerId,
                //     'process_date' => $actualDayEnd,
                // ], [
                //     'sd_id' => $infractionSettings->id,
                //     'customer_id' => $customerId,
                //     'value' => $i,
                //     'created_at' => Carbon::now(),
                //     'process_date' => $actualDayEnd,
                // ]);
            }
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job finished');
        } catch (\Exception $e) {
            $this->logger->error('SUMMARY-DASHBOARD-BULK: Job failed - ' . $e->getMessage() . ' get-line number ' . $e->getLine());
        }
    }
}
