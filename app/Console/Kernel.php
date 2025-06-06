<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
         'App\Console\Commands\DatabaseBackUp',
         'App\Console\Commands\ExpiredModules',
         '\App\Console\Commands\BackupCronCommand',
         'App\Console\Commands\DeletePrevBackups',
         'App\Console\Commands\SyncTenantDatabases',
         'App\Console\Commands\DetectDatabaseChanges',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $env = config('app.env');
        $email = config('mail.username');

        if ($env === 'live') {
            //Scheduling backup, specify the time when the backup will get cleaned & time when it will run.
            $schedule->command('backup:run')->dailyAt('23:30');
            $schedule->command('backups:delete-old')->dailyAt('23:30');

            //Schedule to create recurring invoices
            $schedule->command('pos:generateSubscriptionInvoices')->daily();
            $schedule->command('pos:updateRewardPoints')->daily();

        }

        if ($env === 'demo' && !empty($email)) {
            //IMPORTANT NOTE: This command will delete all business details and create dummy business, run only in demo server.
            $schedule->command('pos:dummyBusiness')
                    ->cron('0 */3 * * *')
                    //->everyThirtyMinutes()
                    ->emailOutputTo($email);
        }
        $schedule->command('tenants:sync')->everyFiveMinutes();
        $schedule->command('expired-modules')->dailyAt('8:00');
        $schedule->command('db:detect-changes')->dailyAt('23:30');
        $schedule->command('sms:send-departure-reminders')->dailyAt('09:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
