<?php

namespace Modules\Contracts\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Modules\Contracts\Jobs\ContractExpiryReminderEmails;
use Modules\Contracts\Models\ContractExpirySettings;

class ContractExpiryReminder implements ShouldQueue
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
            $configurationDetails = ContractExpirySettings::first();
            $mail_1_time = $configurationDetails->email_1_time;
            $mail_2_time = $configurationDetails->email_2_time;
            $mail_3_time = $configurationDetails->email_3_time;
            $mail_date = Carbon::now()->format('Y-m-d');
            $email_1_delay = Carbon::parse($mail_date . ' ' . $mail_1_time);
            $email_2_delay = Carbon::parse($mail_date . ' ' . $mail_2_time);
            $email_3_delay = Carbon::parse($mail_date . ' ' . $mail_3_time);
            //First Mail alert
            $alert_period_1 = Carbon::now()->addDays($configurationDetails->alert_period_1)->toDateString();
            $responseAlertOne = ContractExpiryReminderEmails::dispatch('contract_expiry_remainder_mail_1', $alert_period_1)->delay($email_1_delay);
            //Second Mail alert
            $alert_period_2 = Carbon::now()->addDays($configurationDetails->alert_period_2)->toDateString();
            $responseAlertTwo = ContractExpiryReminderEmails::dispatch('contract_expiry_remainder_mail_2', $alert_period_2)->delay($email_2_delay);
            //Third Mail alert
            $alert_period_3 = Carbon::now()->addDays($configurationDetails->alert_period_3)->toDateString();
            $responseAlertThree = ContractExpiryReminderEmails::dispatch('contract_expiry_remainder_mail_3', $alert_period_3)->delay($email_3_delay);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            Log::channel('contractExpiryReminderLog')
                ->error($errorMessage);
        }
    }
}
