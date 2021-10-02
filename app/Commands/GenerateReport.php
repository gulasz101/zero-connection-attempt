<?php

namespace App\Commands;

use App\ConnectionAttempt;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterFactory;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class GenerateReport extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:generate-report';

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
        $fileName = sprintf('report_%s_.csv', Carbon::now()->format('Ymd'));
        $filePath = Storage::path($fileName);

        $writer = WriterFactory::createFromFile($filePath);
        $writer->openToFile($filePath);

        $writer->addRow(
            WriterEntityFactory::createRowFromArray(
                [
                    'id',
                    'time_execution_started',
                    'time_execution_finished',
                    'time_diff',
                    'status',
                    'data_transferred',
                    'url_requested',
                    'error_msg',
                    'created_at',
                    'updated_at',
                ]
            )
        );

        ConnectionAttempt::all()->each(
            fn(ConnectionAttempt $connectionAttempt) => $writer->addRow(
                WriterEntityFactory::createRowFromArray(
                    tap(
                        $connectionAttempt->toArray(),
                        fn (&$connectionAttemptAsArray) => $connectionAttemptAsArray = collect($connectionAttemptAsArray)
                            ->map(fn ($eachAttribute) => (string)$eachAttribute)
                            ->toArray()
                    )
                )
            )
        );

        $writer->close();

        Storage::disk('s3')
            ->put(
                $fileName,
                File::get($filePath),
            );

        return Command::SUCCESS;
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->dailyAt('7:00');
    }
}
