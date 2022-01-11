<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Vehicle\Repositories\VehiclePendingMaintenanceRepository;
use Illuminate\Support\Facades\Log;

class CheckVehicleServiceOnSubmit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:checkvehicleserviceonsubmit {vehicle_id} {odometre}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check vehicle pending service on trip submit';

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
        $vehicle_id =$this->argument('vehicle_id');
        $odometre = $this->argument('odometre');
        $response = $this->vehiclePendingMaintenanceRepository->updatePendingServiceByOdometre($vehicle_id,$odometre);
        Log::info('Vehicle pending service log'.$this->argument('vehicle_id').'odmetre:'.$this->argument('odometre').'<br>respone:'.print_r($response, true));
    }
}
