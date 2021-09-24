<?php

namespace Modules\Timetracker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Timetracker\Jobs\TimeSheetApprovalReminderEmails;
use Modules\Timetracker\Models\TimeSheetApprovalConfiguration;

class TimeSheetApprovalReminder implements ShouldQueue
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
            Log::channel('timeSheetApprovalRatingLog')->info("EnteringTimeSheetApprovalReminder");
            $configurationDetails = TimeSheetApprovalConfiguration::first();
            $deadLineDay = TimeSheetApprovalConfiguration::first()->day;
            $deadLineTime = TimeSheetApprovalConfiguration::first()->time;
            $dayBefore = Carbon::now()->addDays($deadLineDay)->format('y-m-d');
            $deadlineDateTime = Carbon::parse($dayBefore.' '.$deadLineTime);
            Log::channel('timeSheetApprovalRatingLog')->info('deadline'.$deadlineDateTime);
            //First Mail alert
            $firstEmailNotificationTime = Carbon::parse($deadlineDateTime->subHours($configurationDetails->email_1_time));
            Log::channel('timeSheetApprovalRatingLog')->info('mail 1 alert on'.$firstEmailNotificationTime);
            $responseAlertOne = TimeSheetApprovalReminderEmails::dispatch('time_sheet_approve_notification_mail_1',$dayBefore)->delay($firstEmailNotificationTime);
             //Second Mail alert
            $deadlineDateTime = Carbon::parse($dayBefore.' '.$deadLineTime);
            $secondEmailNotificationTime = Carbon::parse($deadlineDateTime->subHours($configurationDetails->email_2_time));
            Log::channel('timeSheetApprovalRatingLog')->info('mail 2 alert on'.$secondEmailNotificationTime);
            $responseAlertTwo = TimeSheetApprovalReminderEmails::dispatch('time_sheet_approve_notification_mail_2',$dayBefore)->delay($secondEmailNotificationTime);
              //Third Mail alert
            $deadlineDateTime = Carbon::parse($dayBefore.' '.$deadLineTime);
            $thirdEmailNotificationTime = Carbon::parse($deadlineDateTime->subHours($configurationDetails->email_3_time));
            Log::channel('timeSheetApprovalRatingLog')->info('mail 3 alert on'.$thirdEmailNotificationTime);
            $responseAlertThree =TimeSheetApprovalReminderEmails::dispatch('time_sheet_approve_notification_mail_3',$dayBefore)->delay($thirdEmailNotificationTime);
            //LogDetails
            //Log::channel('timeSheetApprovalRatingLog')->info([$responseAlertOne,$responseAlertTwo, $responseAlertThree]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('timeSheetApprovalRatingLog')
                ->error($errorMessage);
        }
    }
}
