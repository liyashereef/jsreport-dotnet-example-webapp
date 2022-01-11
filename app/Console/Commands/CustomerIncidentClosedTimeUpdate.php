<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Supervisorpanel\Repositories\IncidentReportRepository;

class CustomerIncidentClosedTimeUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incident:incidentclosedtimeupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update incident closed time';

    public function __construct(IncidentReportRepository $incidentReportRepository)
    {
        parent::__construct();
        $this->incidentReportRepository = $incidentReportRepository;

        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->incidentReportRepository->updateClosedTimeForAllIncidentReports();
        Log::info('update incident closed time:'.$response);
    }
}
