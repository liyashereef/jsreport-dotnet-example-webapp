<?php

namespace Modules\Generator\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateCurrentYearSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userschedule:create  {--start_date?} {--end_date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create schedule under given Date Range.';

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
        $date = \Carbon::createFromDate(date("Y"), 2, 23);

        $startDate = $date->copy()->startOfYear();;
        $endDate = $date->copy()->endOfYear();
        dd($date->copy()->endOfYear());
        if (!empty($this->option('start_date')) && !empty($this->option('end_date'))) {
        }
    }

    
}
