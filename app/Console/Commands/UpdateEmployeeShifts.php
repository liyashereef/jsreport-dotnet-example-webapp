<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Supervisorpanel\Http\Controllers\SupervisorPanelController;

class UpdateEmployeeShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:updateemployeshift';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update employee shift if duration limit exceeds';

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
        $this->supervisorpanelcontroller->updateEmployeeShiftEndTime();
    }
}
