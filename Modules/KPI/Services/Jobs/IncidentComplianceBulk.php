<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;
use Modules\Supervisorpanel\Models\IncidentReport;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;

class IncidentComplianceBulk extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;
    protected $incidentReportRepository;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
        $this->incidentReportRepository = app()->make(IncidentReportRepository::class);
    }

    public function run()
    {
        $datas = [];
        $customers = $this->options->allCustomers;
        $arguments = $this->options->arguments;
        foreach ($customers as $customer) {
            $incidents = IncidentReport::whereHas('payperiod')
                ->where('customer_id', $customer->id)
                ->select(DB::raw('DATE(created_at) as createdAt'), 'customer_id')
                ->groupBy('customer_id', 'createdAt')
                ->when(isset($arguments['start_date']),function($query) use($arguments){
                    return $query->whereDate('created_at','>=',$arguments['start_date']);
                })
                ->when(isset($arguments['end_date']),function($query) use($arguments){
                    return $query->whereDate('created_at','<=',$arguments['end_date']);
                })
                ->get();

            foreach ($incidents as $incident) {
                $result = $this->incidentReportRepository->getCustomerIncidentCompliance($customer->id, $incident->createdAt);
                if ($result['percentage'] > 0) {
                    $datas[] = [
                        "kpid" => $this->options->kpi->id,
                        "customer_id" => $customer->id,
                        "process_date" => Carbon::parse($incident->createdAt),
                        "value" => $result['percentage'],
                        "value_total" => $result['total'],
                        "value_output" => $result['completed']
                    ];
                }
            }
        }
        return $datas;
    }
}
