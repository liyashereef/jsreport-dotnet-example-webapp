<?php

namespace Modules\KPI\Services\Jobs;

use Carbon\Carbon;
use Modules\KPI\Services\KpiJobInterface;
use Modules\KPI\Services\KpiJobOption;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\KPI\Services\AbstractKpiJob;
use Modules\Supervisorpanel\Repositories\CustomerPayPeriodTemplateRepository;
use Modules\Admin\Repositories\CustomerRepository;

class SiteMetricDaily extends AbstractKpiJob implements KpiJobInterface
{
    protected $options;
    protected $customerReportRepository;
    protected $customerMapRepository;
    protected $payPeriodRepository;
    protected $customerPayPeriodTemplateRepository;

    public function __construct(KpiJobOption $kpiJobOption)
    {
        $this->options = $kpiJobOption;
        $this->customerReportRepository = app()->make(CustomerReportRepository::class);
        $this->customerMapRepository = app()->make(CustomerMapRepository::class);
        $this->payPeriodRepository = app()->make(PayPeriodRepository::class);
        $this->customerPayPeriodTemplateRepository = app()->make(CustomerPayPeriodTemplateRepository::class);
        $this->customerRepository = app()->make(CustomerRepository::class);
    }
    /*
    * Run site metric daily job
    * Fetching all yestordays updated site metric entries, update or store in kapi_data.
    * If yesterday is current payperiod's week two start date,
        then check customer's previous payperiod site metric data submitted or not,
        if not set as value as zero in kapi_data.
    */
    public function run()
    {
        $datas = [];
        $currentPayPeriod = [];
        $previousPayPeriod = [];
        $runPreviousPayPeriodMetric = false;
        // $customers = $this->options->allCustomers;
        $customers = $this->customerRepository->getAllShowSiteDashboardEnabled();
        /* Fetching all yestordays updated site metric entries. */
        $filters['date'] = $this->options->yesterday;
        $templateDetails = $this->customerReportRepository->getCustomerDetails($filters);
        /* Format yestordays updated site metric entries. */
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
                    "value_total" => '',
                    "value_output" => '',
                    'is_submitted'=>true
                ];
            }
        }

        /* Fetching current and previous payperiod.*/
        $payperiods = $this->payPeriodRepository->getLastNthPayPeriodsByEndDate($this->options->today,2);
        if(sizeof($payperiods)==2 ){
            $currentPayPeriod = $payperiods[0];
            $previousPayPeriod = $payperiods[1];
            /* Checking yesterday is current payperiod's week two start date. */
            if($currentPayPeriod->week_two_start_date == $this->options->yesterday){
                $runPreviousPayPeriodMetric = true;
            }
        }
        /*yesterday is current payperiod's week two start date*/
        if($runPreviousPayPeriodMetric){
            $inputs['payperiod_id'] = $previousPayPeriod->id;
            foreach ($customers as $customer) {
                $inputs['customer_id'] = $customer->id;
                /*  Checking customer's previous payperiod site metric data submitted or not,
                    if not set as value as zero in kapi_data.*/
                $dataExists = $this->customerPayPeriodTemplateRepository->getByPayperiodAndCustomer($inputs);
                if($dataExists == 0){
                    $datas[] = [
                        "kpid" => $this->options->kpi->id,
                        "customer_id" => $customer->id,
                        "payperiod_id" => $previousPayPeriod->id,
                        "template_id" => '',
                        "alloction_template_id" => '',
                        "process_date" => Carbon::parse($currentPayPeriod->week_one_end_date),
                        "value" => 0,
                        "value_total" => '',
                        "value_output" => '',
                        'is_submitted'=>false
                    ];
                }
            }
        }
        return $datas;
    }
}
