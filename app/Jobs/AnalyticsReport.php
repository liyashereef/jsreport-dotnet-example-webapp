<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Repositories\RecruitingAnalyticsRepository;

class AnalyticsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userEmail)
    {
        Log::channel('reportLog')->info($userEmail);
        $this->email = $userEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RecruitingAnalyticsRepository $recruitingAnalyticsRepository)
    {
        try {
            $response = $recruitingAnalyticsRepository->recruitingAnalyticsExcelReport($this->email);
            Log::channel('reportLog')->info($response);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('reportLog')
                ->error($errorMessage);
        }
    }
}
