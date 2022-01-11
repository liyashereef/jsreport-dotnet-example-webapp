<?php

namespace App\Console\Commands;

use App\Repositories\SummaryDashboardRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SiteTurnOverCroneJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SummaryDashboard:SiteTurnOverCroneJob {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create site turnover entries by start, end date parameters';

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

            $this->logger->info('---Inside----- croneSiteTurnOver ');
            if (!empty($this->option('start_date')) && !empty($this->option('end_date'))) {
                $startDate = Carbon::parse($this->option('start_date'))->startOfDay();
                $endDate = Carbon::parse($this->option('end_date'))->endOfDay();
            } else {
                $endDate = Carbon::today()->subDays(1)->endOfDay();
                $startDate = Carbon::parse($endDate)->startOfDay();
            }
            $status = $this->summaryDashboardRepository->croneSiteTurnOver($startDate, $endDate);
            $this->logger->info('SUMMARY-DASHBOARD-BULK: Job finished');
        } catch (\Exception $e) {
            $this->logger->error('SUMMARY-DASHBOARD-BULK: Job failed - ' . $e->getMessage() . ' get-line number ' . $e->getLine());
        }
    }
}
