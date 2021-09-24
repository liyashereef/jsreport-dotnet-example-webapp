<?php

namespace Modules\Timetracker\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
use Modules\Timetracker\Models\TimeSheetApprovalConfiguration;

class TimeSheetApprovalReminderEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $template,$deadlineDateTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template,$deadlineDateTime)
    {
        $this->template = $template;
        $this->deadlineDateTime = $deadlineDateTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmployeeShiftAprovalRatingRepository $employeeShiftAprovalRatingRepository)
    {
        try {
            Log::channel('timeSheetApprovalRatingLog')->info([$this->template, $this->deadlineDateTime]);
            $response = $employeeShiftAprovalRatingRepository->timesheetApprovalReminder($this->template, $this->deadlineDateTime);
            Log::channel('timeSheetApprovalRatingLog')->info($response);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('timeSheetApprovalRatingLog')
                ->error($errorMessage);
        }

    }
}
