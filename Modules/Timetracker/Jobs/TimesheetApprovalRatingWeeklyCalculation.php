<?php

namespace Modules\Timetracker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
use Illuminate\Foundation\Bus\Dispatchable;

class TimesheetApprovalRatingWeeklyCalculation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deadlinedate,$deadLineTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deadlinedate,$deadLineTime)
    {
        Log::channel('timeSheetApprovalRatingLog')->info("entered TimesheetApprovalRatingWeeklyCalculation. date".$deadlinedate);
        Log::channel('timeSheetApprovalRatingLog')->info("entered TimesheetApprovalRatingWeeklyCalculation. time".$deadLineTime);
        $this->deadlinedate = $deadlinedate;
        $this->deadLineTime = $deadLineTime;
        // Log::channel("timeSheetApprovalRatingLogObject Erro undoo ".$this->deadlineDateTime);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmployeeShiftAprovalRatingRepository $employeeShiftAprovalRatingRepository)
    {
        try {
            Log::channel('timeSheetApprovalRatingLog')->info("EnteringTimeSheetApprovalReminder above repository entry");
            // Log::channel('timeSheetApprovalRatingLog')->info("checking param in job".$this->deadlineDateTime);
            $deadlineDateTime = Carbon::parse($this->deadlinedate.' '.$this->deadLineTime);
            $response = $employeeShiftAprovalRatingRepository->timesheetApprovalRatings(null,$deadlineDateTime);
            Log::channel('timeSheetApprovalRatingLog')->info("----TimesheetApprovalRatingWeeklyCalculation----".$response);
            return true;
            // $ratingResponse = $employeeShiftAprovalRatingRepository->addCustomerApproverRatingCalculation();
        } catch (\Exception $e) {
            $error = 'error: ' . $e->getMessage() . ' in '. $e->getFile() . ' at ' . $e->getLine();
            Log::channel('timeSheetApprovalRatingLog')->info("-----TimesheetApprovalRatingWeeklyCalculation Error----".$error);
            return $error;
        }
    }
}
