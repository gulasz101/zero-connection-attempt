<?php

declare(strict_types=1);

namespace App;

use App\Enums\ConnectionAttemptStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Carbon $time_execution_started
 * @property Carbon $time_execution_finished
 * @property string $time_diff
 * @property ConnectionAttemptStatus $status
 * @property int|null $data_transferred
 * @property string $url_requested
 * @property string|null $error_msg
 */
class ConnectionAttempt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'time_execution_started' => 'datetime',
        'time_execution_finished' => 'datetime',
        'status' => ConnectionAttemptStatus::class
    ];
}
