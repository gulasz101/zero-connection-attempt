<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class BackupDB extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:backup';

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
        Storage::disk('s3')->put(
            Carbon::now()->format(\DateTimeInterface::ATOM) . '.sqlite',
            database_path('database.sqlite'),
        );

        return Command::SUCCESS;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
         $schedule->command(static::class)->dailyAt('7:00');
    }
}
