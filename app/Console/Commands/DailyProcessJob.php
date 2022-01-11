<?php

namespace App\Console\Commands;

use App\Jobs\VacationDailyProcess as VacationLogCroneJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyProcessJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacation:DailyProcessJob';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Vacation calculation daily run';

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
        VacationLogCroneJob::dispatch();
    }
}
