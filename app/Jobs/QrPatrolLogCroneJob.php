<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Timetracker\Repositories\CustomerQrcodeRepository;

class QrPatrolLogCroneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDate, $endDate;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 7200;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::channel('summaryDashboardLog')->info("--------------------------------------------------");
            Log::channel('summaryDashboardLog')->info("SUMMARY-DASHBOARD-LOG-BULK: Job started");
            Log::channel('summaryDashboardLog')->info("---Inside----- processQrPatrolWidgetEntries ");

            $customerQrcodeRepository = app()->make(CustomerQrcodeRepository::class);
            $response = $customerQrcodeRepository->widgetEntries($this->startDate, $this->endDate);

            Log::channel('summaryDashboardLog')->info("SUMMARY-DASHBOARD-LOG-BULK: Job finished");
        } catch (\Exception $e) {
            Log::channel('summaryDashboardLog')->info("Error: " . $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile());
        }
    }
}
