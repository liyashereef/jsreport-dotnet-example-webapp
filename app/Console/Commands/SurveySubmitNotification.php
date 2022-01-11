<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Supervisorpanel\Http\Controllers\SupervisorPanelController;

class SurveySubmitNotification extends Command
{

    protected $supervisorpanelcontroller;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surveysubmitnotification:supervisors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email notification to supervisor for submiting the survey';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SupervisorPanelController $supervisorpanelcontroller)
    {
        parent::__construct();
        $this->supervisorpanelcontroller = $supervisorpanelcontroller;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->supervisorpanelcontroller->mailToSupervisorextended();
    }
}
