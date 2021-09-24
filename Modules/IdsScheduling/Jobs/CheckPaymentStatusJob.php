<?php

namespace Modules\IdsScheduling\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\IdsScheduling\Http\Controllers\IdsSlotBookingController;
class CheckPaymentStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inputs) {
        $this->params =$inputs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::channel('kpiLog')->info('-Start Running Job---params-- '.json_encode($this->params));
        $idsSlotBookingController = app()->make(IdsSlotBookingController::class);
        $data = $idsSlotBookingController->updateSlotBooking($this->params);
        // Log::channel('kpiLog')->info('-Running Job End----- Check Payment Status  ----------');
    }
}
