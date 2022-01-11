<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\KPI\Console\KpiBulkJobCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DeleteLoginLogs::class,
        'App\Console\Commands\SurveySubmitNotification',
        'App\Console\Commands\MailFromQueue',
        'App\Console\Commands\UpdateEmployeeShifts',
        'App\Console\Commands\CheckVehicleService',
        'Modules\IdsScheduling\Console\BlockIDSOfficeSlots',
        'Modules\IdsScheduling\Console\BalanceTransactionUpdate',
        'Modules\Client\Console\ScreeningTimeUpdate',
        'Modules\Generator\Console\CreateCurrentYearSchedules',
        KpiBulkJobCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('delete-log:daily')->daily();
        $schedule->command('surveysubmitnotification:supervisors')->hourly();
        $schedule->command('mail:queue')->everyMinute();
        $schedule->command('shift:updateemployeshift')->everyFifteenMinutes();
        $schedule->command('vehicle:checkvehicleservice')->dailyAt(config('globals.vehicleServiceTime'));
        $schedule->command('vehicle:AddVehiclePendingServiceToMailQueue')->dailyAt(config('globals.addVehiclePendingServiceTime'));
        $schedule->call('Modules\ProjectManagement\Http\Controllers\ProjectManagementController@dueDateNotification')->dailyAt(config('globals.notificationTime'));
        $schedule->call('Modules\Contracts\Http\Controllers\RfpController@onboardingTargetDateRemainder')->dailyAt(config('globals.notificationTime'));
        $schedule->call('Modules\Admin\Http\Controllers\UserCertificateExpiryController@userCertificateExpiryDueReminderMail')->dailyAt(config('globals.userCertificateExpiryDueReminderEmailTime'));
        $schedule->call('Modules\Expense\Http\Controllers\ExpenseDashboardController@expenseApprovalReminderMail')->dailyAt(config('globals.ExpenseReminderEmailTime'));
        $schedule->call('Modules\Recruitment\Http\Controllers\RecCandidateController@loginReminderMail')->dailyAt(config('globals.RecruitmentLoginReminderEmailTime'));
        $schedule->call('Modules\Timetracker\Http\Controllers\TimesheetApprovalController@employeeTimeSheetApprovalRating')->weekly();
        $schedule->call('Modules\Timetracker\Http\Controllers\TimesheetApprovalController@employeeTimesheetApprovalEmailNotification')->weekly();
        $schedule->call('Modules\KPI\Http\Controllers\KpiWidgetController@executeJob')->dailyAt(config('globals.kpiCalculationTime')); //TODO:change
        $schedule->call('Modules\Reports\Repositories\CovidReportRepository@sendDailyHealthReport')->dailyAt(config('globals.summaryDashboardDataProcessTime'));
        $schedule->call('Modules\Contracts\Http\Controllers\ContractsController@contractExpiryEmailNotification')->daily();
        $schedule->call('Modules\Recruitment\Http\Controllers\RecCandidateSelectionController@OnboardingDeadlineRemainder')->dailyAt(config('globals.onboardingDeadlineRemainderTime'));
        $schedule->call('Modules\Timetracker\Http\Controllers\QrcodePatrolController@qrPatroldailyActivity')->dailyAt(config('globals.qrPatrolDailyReportTime'));

        //summary dashboard
        $schedule->command('SummaryDashboard:QrPatrolCroneJob')->dailyAt(config('globals.summaryDashboardDataProcessTime'))->withoutOverlapping();
        $schedule->command('SummaryDashboard:ScheduleInfractionCroneJob')->dailyAt(config('globals.summaryDashboardDataProcessTime'))->withoutOverlapping();
        $schedule->command('SummaryDashboard:OperationsDashboardMetricCroneJob')->dailyAt(config('globals.summaryDashboardDataProcessTime'))->withoutOverlapping();
        $schedule->command('SummaryDashboard:SiteTurnOverCroneJob')->dailyAt(config('globals.summaryDashboardDataProcessTime'))->withoutOverlapping();
        $schedule->command('SummaryDashboard:TotalWorkHoursVsEarnedBillingsCroneJob')->dailyAt(config('globals.summaryDashboardDataProcessTime'))->withoutOverlapping();

        //Bonus processing
        $schedule->command('bonusProcess:DailyProcess')->dailyAt(config('globals.BonusDataProcessTime'))->withoutOverlapping();
        $schedule->command('qrpatrol:QrPatrolLogsCroneJob')->dailyAt(config('globals.summaryDashboardQaPatrolLogProcessTime'))->withoutOverlapping();
        //Vacation processing
        $schedule->command('vacation:DailyProcessJob')->dailyAt(config('globals.BonusDataProcessTime'))->withoutOverlapping();
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
