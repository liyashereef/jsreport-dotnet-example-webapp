<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Builder;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\Admin\Repositories\CustomerRepository;

class SiteMetricBulk extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;
    protected $payPeriodRepository;
    protected $customerMapRepository;
    protected $customerReportRepository;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
        $this->payPeriodRepository = app()->make(PayPeriodRepository::class);
        $this->customerMapRepository = app()->make(CustomerMapRepository::class);
        $this->customerReportRepository = app()->make(CustomerReportRepository::class);
        $this->customerRepository = app()->make(CustomerRepository::class);
    }

    /*
    * Run site metric bulk job
    * Fetching all site metric entries, update or store in kapi_data.
    * Checking all previous payperiod's customer's site metric data submitted or not,
        if not set as value as zero in kapi_data.
    */
    public function run()
    {
        $datas = [];
        $arguments = $this->options->arguments;
        if(isset($arguments['start_date']) && isset($arguments['end_date'])){
            $startDate = $arguments['start_date'];
            $endDate = $arguments['end_date'];
            $completedPayPeriods = collect($this->payPeriodRepository->getAllActivePayPeriodsBetweenDates($startDate,$endDate))->sortBy('start_date');
        }else{
            $endDate = $this->options->today;
            $completedPayPeriods = $this->payPeriodRepository->getPreviousNthPayPeriodsByDate($endDate,null);
        }

        $templateDetails = $this->customerReportRepository->getCustomerDetails($arguments);

        foreach ($templateDetails as $template) {
            $trend_report = $this->customerMapRepository->getPayperiodAvgReport(
                $template->customer_id,
                $template->payperiod_id,
                [$template->id]
            );

            if (isset($trend_report['score']) && isset($trend_report['score']['total'])) {
                $value = $trend_report['score']['total'];
                $datas[] = [
                    "kpid" => $this->options->kpi->id,
                    "customer_id" => $template->customer_id,
                    "payperiod_id" => $template->payperiod_id,
                    "template_id" => $template->template_id,
                    "alloction_template_id" => $template->id,
                    "process_date" => Carbon::parse($template->created_at),
                    "value" => $value,
                    'is_submitted' => true,
                    "value_total" => '',
                    "value_output" => ''
                ];
            }
        }

        $customers = $this->customerRepository->getAllShowSiteDashboardEnabled();
       
        foreach ($customers as $customer) {
            foreach ($completedPayPeriods as $payperiod) {

                $dataExists = collect($templateDetails)
                    ->where('payperiod_id', $payperiod->id)
                    ->where('customer_id', $customer->id)
                    ->count();
                if ($dataExists == 0) {
                    // $nextPayperiod = collect($this->payPeriodRepository->getPastPayPeriodsByDate($payperiod->end_date,1))->first();
                    $nextPayperiod = collect($completedPayPeriods)
                    ->where('start_date', '>=', $payperiod->end_date)
                    ->first();

                    if(
                        empty(!$nextPayperiod) &&
                        Carbon::parse($nextPayperiod->week_two_start_date)->format('Y-m-d') < Carbon::parse($endDate)->format('Y-m-d') &&
                        Carbon::parse($nextPayperiod->week_two_start_date)->format('Y-m-d') < Carbon::parse($this->options->today)->format('Y-m-d')
                    ){

                        $datas[] = [
                            "kpid" => $this->options->kpi->id,
                            "customer_id" => $customer->id,
                            "payperiod_id" => $payperiod->id,
                            "template_id" => '',
                            "alloction_template_id" => '',
                            "process_date" => Carbon::parse($nextPayperiod->week_one_end_date),
                            "value" => 0,
                            'is_submitted'=>false,
                            "value_total" => '',
                            "value_output" => ''
                        ];
                    }
                }
            }
        }

        return $datas;
    }
}
