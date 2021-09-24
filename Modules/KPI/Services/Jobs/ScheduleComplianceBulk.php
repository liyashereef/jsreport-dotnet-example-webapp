<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Modules\Employeescheduling\Models\EmployeeScheduleTimeLog;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class ScheduleComplianceBulk extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;
    protected $schedulingRepository;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
        $this->schedulingRepository = app()->make(SchedulingRepository::class);
    }

    public function run()
    {
        $datas = [];
        $customers = $this->options->allCustomers;
        $arguments = $this->options->arguments;
        foreach ($customers as $customer) {

            $empShifts = EmployeeScheduleTimeLog::whereHas('schedule', function ($query) use ($customer) {
                return $query->where('status', 1)->where('customer_id', $customer->id);
            })
            ->groupBy('employee_schedule_id', 'schedule_date')
            ->select('employee_schedule_id', 'schedule_date')
            ->when(isset($arguments['start_date']),function($query) use($arguments){
                return $query->whereDate('schedule_date','>=',$arguments['start_date']);
            })
            ->when(isset($arguments['end_date']),function($query) use($arguments){
                return $query->whereDate('schedule_date','<=',$arguments['end_date']);
            })
            ->get();

            if (sizeof($empShifts) >= 1) {
                foreach ($empShifts as $empShift) {

                    //Fetch report
                    $result = $this->schedulingRepository
                        ->getCustomerScheduleComplienceByDate(
                            $customer->id,
                            $empShift->schedule_date
                        );

                    if ($result['percentage'] > 0) {

                        $datas[] = [
                            "kpid" => $this->options->kpi->id,
                            "customer_id" => $customer->id,
                            "process_date" => Carbon::parse($empShift->schedule_date),
                            "value" => $result['percentage'],
                            "value_total" => $result['total'],
                            "value_output" => $result['completed']
                        ];
                    }
                }
            }
        }
        return $datas;
    }
}
