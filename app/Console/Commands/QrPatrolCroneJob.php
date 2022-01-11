<?php

namespace App\Console\Commands;

use App\Jobs\QrPatrolCroneJob as JobsQrPatrolCroneJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QrPatrolCroneJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SummaryDashboard:QrPatrolCroneJob {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or replace QR Patrol widget entries by start, end date parameters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDate = null;
        $endDate = null;
        if (!empty($this->option('start_date')) && !empty($this->option('end_date'))) {
            $startDate = Carbon::parse($this->option('start_date'))->startOfDay();
            $endDate = Carbon::parse($this->option('end_date'))->endOfDay();
        }
        JobsQrPatrolCroneJob::dispatch($startDate, $endDate);
    }
}
