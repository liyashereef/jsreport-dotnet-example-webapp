<?php

namespace App\Console\Commands;

use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;
use Illuminate\Console\Command;

class AddVehiclePendingServiceToMailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:AddVehiclePendingServiceToMailQueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To add pending service mail to mail queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(VehiclePendingMaintenanceRepository $vehiclePendingMaintenanceRepository)
    {
        parent::__construct();
        $this->vehiclePendingMaintenanceRepository = $vehiclePendingMaintenanceRepository;
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->vehiclePendingMaintenanceRepository->addPendingServiceMailToMailQueue();
    }
}
