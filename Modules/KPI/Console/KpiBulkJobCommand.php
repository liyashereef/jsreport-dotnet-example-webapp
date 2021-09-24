<?php

namespace Modules\KPI\Console;

use DateTime;
use Illuminate\Console\Command;
use Modules\KPI\Repositories\KpiAnalyticsRepository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class KpiBulkJobCommand extends Command
{
    protected $kpiAnalyticsRepository;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kpi:bulk-dump';
    protected $signature = 'kpi:bulk-dump {--start_date=} {--end_date=} {--kpid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'KPI bulk computation.Inputs {--start_date=} {--end_date=} {--kpid=}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(KpiAnalyticsRepository $kpiAnalyticsRepository)
    {
        $this->kpiAnalyticsRepository = $kpiAnalyticsRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->options();
        $this->kpiAnalyticsRepository->executeJob($arguments,true);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['from', InputArgument::OPTIONAL, 'Start date | Project date'],
            ['to', InputArgument::OPTIONAL, 'End date'],
            ['kpid', InputArgument::OPTIONAL, 'KPID only execute specific KPI'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
