<?php

namespace Modules\Contracts\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Contracts\Repositories\ContractsRepository;

class ContractExpiryReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $template;
    protected $expiryDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template, $expiryDate)
    {
        $this->template = $template;
        $this->expiryDate = $expiryDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ContractsRepository $contractsRepository)
    {
        try {
            $response = $contractsRepository->contractExpiryReminder($this->template, $this->expiryDate);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('contractExpiryReminderLog')
                ->error($errorMessage);
        }
    }
}
