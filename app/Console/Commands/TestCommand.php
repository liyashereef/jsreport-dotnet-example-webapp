<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\SummaryDashboardMaster;
use Modules\Admin\Models\TemplateQuestionsCategory;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\Hranalytics\Http\Controllers\JobApplicationController;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:fntest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to test functions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        PayPeriodRepository $pay_period_repository,
        CustomerReportRepository $customer_report_repository,
        Customer $customer
    )
    {
        parent::__construct();
        $this->pay_period_repository = $pay_period_repository;
        $this->customer_report_repository = $customer_report_repository;
        $this->customer = $customer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        Log::info(get_current_user());
        $startDate= '2021-01-01';
        $endDate = '2021-04-17';
        $payperiods = [];

        if (!empty($startDate) && !empty($endDate)) {
            $payPeriodList = $this->pay_period_repository->getPayperiodRangeAll($startDate, $endDate);
        }

        $prevTime = $start;
        $start = microtime(true);
        $elapsed = $start - $prevTime;
        Log::info("time - ".$elapsed);

        $customerIds = $this->customer->whereIn('id',[298,299,230,231,232,233])->get();
        $summaryDashboardMasterObj = SummaryDashboardMaster::where('machine_name', 'operational-dashboard-matrix')->first();
        foreach ($customerIds as $customerId) {
            $reportColor = [];
            foreach ($payPeriodList as $key => $eachList) {
                $report_arr = $this->customer_report_repository->customerPayperiodTrendReport
                (
                    $customerId,
                    $eachList->start_date,
                    $eachList->end_date
                );
                array_push($payperiods, $eachList->id);
                array_push($reportColor, $report_arr['average_report']);
            }

            $reportKeyList = TemplateQuestionsCategory::withTrashed()->get()->pluck('id', 'description')->toArray();
            Log::info(sizeof($reportColor));
            if (!empty($reportColor)) {
                foreach ($reportColor as $key => $report) {
                    if (empty($report)) {
                        continue;
                    }
                    foreach ($report["score"] as $ky => $score) {
                        if (array_key_exists($ky, $reportKeyList)) {
                            $outArr = [
                                'sd_id' => $summaryDashboardMasterObj->id,
                                'customer_id' => $customerId,
                                'value' => $score,
                                'created_at' => Carbon::now(),
                                'category_id' => $reportKeyList[$ky],
                                'payperiod_id' => $payperiods[$key],
                            ];
                            Log::info("-------- outArr --------");
                            Log::info(
                                " - customer - ".$outArr['customer_id']->id.
                                " - score - ".$outArr['value'].
                                " - category_id - ".$reportKeyList[$ky].
                                " - payperiod_id - ".$payperiods[$key].
                                " - report color size - ".sizeof($reportColor)
                            );
                        }
                    }
                }
            }
        }

        $prevTime = $start;
        $start = microtime(true);
        $elapsed = $start - $prevTime;
        Log::info("time - ".$elapsed);

        Log::info('Test command executed');
    }
}
