<?php

namespace Modules\IdsScheduling\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\IdsScheduling\Http\Controllers\Admin\IdsSchedulingController;
class CheckRefundStatusJob implements ShouldQueue
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
        $idsSchedulingController = app()->make(IdsSchedulingController::class);
        $data = $idsSchedulingController->checkRefundProceesingStatus($this->params);
        
    }
}
