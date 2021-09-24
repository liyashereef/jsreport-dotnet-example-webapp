<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;

class ScheduleComplianceDaily extends AbstractKpiJob implements KpiJobInterface
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

        foreach ($customers as $customer) {
            //Fetch report
            $result = $this->schedulingRepository
                ->getCustomerScheduleComplienceByDate(
                    $customer->id,
                    $this->options->yesterday
                );

            if ($result['percentage'] > 0) {
                $datas[] = [
                    "kpid" => $this->options->kpi->id,
                    "customer_id" => $customer->id,
                    "process_date" => Carbon::parse($this->options->yesterday),
                    "value" => $result['percentage'],
                    "value_total" => $result['total'],
                    "value_output" => $result['completed']
                ];
            }
        }
        return $datas;
    }
}
