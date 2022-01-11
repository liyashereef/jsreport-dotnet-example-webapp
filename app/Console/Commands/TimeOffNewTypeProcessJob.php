<?php

namespace App\Console\Commands;

use App\Jobs\VacationNewRequestTypeProcess as VacationLogCroneJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TimeOffNewTypeProcessJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacation:TimeOffNewRequestType';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Vacation re-calculation on new request type';

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
