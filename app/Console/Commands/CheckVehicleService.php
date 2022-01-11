<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;

class CheckVehicleService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:checkvehicleservice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To update vehicle pending service due';

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
        $this->vehiclePendingMaintenanceRepository->updatePendingServiceByDate();
    }
}
