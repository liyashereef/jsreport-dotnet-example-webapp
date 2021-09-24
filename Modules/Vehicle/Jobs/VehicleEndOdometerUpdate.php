<?php

namespace Modules\Vehicle\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Vehicle\Repositories\VehicleTripRepository;
use Illuminate\Support\Facades\Log;

class VehicleEndOdometerUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $shift;
    public function __construct($shift)
    {
        //
        $this->shift =$shift;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function  handle(VehicleTripRepository $vehicleTripRepository)
    {   
        try{
            $vehicleTripRepository->updateOdometerAndTrip($this->shift);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage()." at ".$e->getLine()." in ".$e->getFile();
            Log::info($errorMessage);
        }
    }
}
