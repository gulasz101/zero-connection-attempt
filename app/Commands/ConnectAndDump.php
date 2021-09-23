<?php

namespace App\Commands;

use App\ConnectionAttempt;
use App\Enums\ConnectionAttemptStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class ConnectAndDump extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:connect-and-dump';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requestUrl = 'https://packagist.org';
        $timeStart = Carbon::now();

        $connectionAttempt = new ConnectionAttempt();
        $connectionAttempt->url_requested = $requestUrl;
        $connectionAttempt->time_execution_started = $timeStart;

        try {
            $contents = Http::timeout(30)
                ->get($requestUrl)
                ->throw();

            $connectionAttempt->status = ConnectionAttemptStatus::ok();
            $connectionAttempt->data_transferred = mb_strlen($contents);
        } catch (\Throwable $t) {
            $connectionAttempt->status = ConnectionAttemptStatus::failed();
            $connectionAttempt->error_msg = $t->getMessage();
        } finally {
            $connectionAttempt->time_execution_finished = Carbon::now();
            $connectionAttempt->time_diff = $timeStart
                ->longRelativeToNowDiffForHumans(
                    $connectionAttempt->time_execution_finished
                );
            $connectionAttempt->save();
        }

        return 0;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
         $schedule->command(static::class)->everyMinute();
    }
}
