<?php

namespace App\Console\Commands;

use App\Jobs\QrPatrolLogCroneJob as JobsQrPatrolLogCroneJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QrPatrolLogsCroneJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrpatrol:QrPatrolLogsCroneJob  {--start_date=} {--end_date=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'QR Patrol Widget - Landing page widget by start, end date parameters';

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
            $startDate = Carbon::parse($this->option('start_date'))->startOfDay()->format("Y-m-d h:i:s");
            $endDate = Carbon::parse($this->option('end_date'))->endOfDay()->format("Y-m-d h:i:s");
        }
        JobsQrPatrolLogCroneJob::dispatch($startDate, $endDate);
    }
}
