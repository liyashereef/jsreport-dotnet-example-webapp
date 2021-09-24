<?php

namespace Modules\Timetracker\Jobs;
use Illuminate\Support\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Admin\Models\TimesheetApprovalRatingConfiguration;
use Modules\Timetracker\Repositories\EmployeeShiftAprovalRatingRepository;
use Modules\Timetracker\Jobs\TimesheetApprovalRatingWeeklyCalculation;
use Modules\Timetracker\Models\TimeSheetApprovalConfiguration;


class TimeSheetApprovalRating implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
        Log::channel('timeSheetApprovalRatingLog')->info("EnteringTimeSheetApprovalRating");
        $configurationDetails = TimeSheetApprovalConfiguration::first();
        $highestDiffernce = TimesheetApprovalRatingConfiguration::max('difference');
        $deadLineDay = TimeSheetApprovalConfiguration::first()->day;
        $deadLineTime = TimeSheetApprovalConfiguration::first()->time;
        $deadlinedate = Carbon::now()->addDays($deadLineDay)->format('Y-m-d');
        $deadlineDateTime = Carbon::parse($deadlinedate.' '.$deadLineTime);
        Log::channel('timeSheetApprovalRatingLog')->info($deadlineDateTime);
        $JobTime = $deadlineDateTime->addHours($highestDiffernce);
        Log::channel('timeSheetApprovalRatingLog')->info("JobTime".$JobTime);
        //job start from last diff hours after deadline and time
        Log::channel('timeSheetApprovalRatingLog')->info("Entering job TimesheetApprovalRatingWeeklyCalculation");
       $response = TimesheetApprovalRatingWeeklyCalculation::dispatch($deadlinedate,$deadLineTime)->delay($JobTime);;
        // Log::channel('timeSheetApprovalRatingLog')->info("TimesheetApprovalRatingWeeklyCalculation".$response);
    } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
        Log::channel('timeSheetApprovalRatingLog')
                ->error($errorMessage);
        }
    }
}
