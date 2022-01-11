<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Vehicle\Repositories\VehicleTripRepository;

class VehicleOdometerUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:vehicleodometerupdate {shift_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vehicle odometer';

    public function __construct(VehicleTripRepository $vehicleTripRepository)
    {
        parent::__construct();
        $this->vehicleTripRepository = $vehicleTripRepository;

        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shift_id =$this->argument('shift_id');
        $response = $this->vehicleTripRepository->updateOdometerAndTrip($shift_id);
        Log::info('update vehicle odometer'.$this->argument('shift_id'));
    }
}
