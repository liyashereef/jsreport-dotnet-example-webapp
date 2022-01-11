<?php

namespace App\Console\Commands;

use App\Repositories\SummaryDashboardRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TotalWorkHoursVsEarnedBillingsCroneJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SummaryDashboard:TotalWorkHoursVsEarnedBillingsCroneJob {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate total hours ,Earned billings entries by start, end date parameters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SummaryDashboardRepository $summaryDashboardRepository)
    {
        parent::__construct();
        $this->summaryDashboardRepository = $summaryDashboardRepository;
        $this->logger = Log::channel('summaryDashboardLog');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->logger->info('--------------------------------------------------');
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job started');

            $this->logger->info('---Inside----- croneTotalWorkHoursVsEarnedBilling ');
            $startDate = $endDate = null;
            if (!empty($this->option('start_date')) && !empty($this->option('end_date'))) {
                $startDate = Carbon::parse($this->option('start_date'))->startOfDay();
                $endDate = Carbon::parse($this->option('end_date'))->endOfDay();
            }
            $status = $this->summaryDashboardRepository->croneTotalWorkHoursVsEarnedBilling($startDate, $endDate);
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job finished');
        } catch (\Exception $e) {
            $this->logger->error('SUMMARY-DASHBOARD-BULK: Job failed - ' . $e->getMessage() . ' get-line number ' . $e->getLine());
        }
    }
}
