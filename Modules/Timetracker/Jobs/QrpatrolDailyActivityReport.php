<?php

namespace Modules\Timetracker\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Timetracker\Repositories\QrcodeLocationRepository;

class QrpatrolDailyActivityReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(QrcodeLocationRepository $qrcodeLocationRepository)
    {
        try {
            $response = $qrcodeLocationRepository->getDailyActivityReport();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            \Log::error('QR patrol daily report ' . $errorMessage);
            return $errorMessage;
        }

    }
}
