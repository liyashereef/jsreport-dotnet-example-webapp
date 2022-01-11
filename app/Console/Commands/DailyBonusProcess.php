<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BonusDailyProcessing;

class DailyBonusProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonusProcess:DailyProcess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Bonus Process';

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
        BonusDailyProcessing::dispatch();
    }
}
