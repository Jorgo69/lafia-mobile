<?php

declare(strict_types=1);

namespace App\Modules\Community\Models;

use App\Modules\Community\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class PendingReport extends Model
{
    protected $fillable = [
        'target_type',
        'target_id',
        'target_label',
        'report_type',
        'details',
        'latitude',
        'longitude',
        'observed_at',
        'status',
        'attempts',
        'last_error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'observed_at' => 'datetime',
            'sent_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    /** @param Builder<PendingReport> $query */
    public function scopeQueued(Builder $query): void
    {
        $query->whereIn('status', [ReportStatus::QUEUED, ReportStatus::FAILED])
            ->where('attempts', '<', 5);
    }

    /** @param Builder<PendingReport> $query */
    public function scopeUnsent(Builder $query): void
    {
        $query->whereNot('status', ReportStatus::SENT);
    }

    public function markSending(): void
    {
        $this->update([
            'status' => ReportStatus::SENDING,
            'attempts' => $this->attempts + 1,
        ]);
    }

    public function markSent(): void
    {
        $this->update([
            'status' => ReportStatus::SENT,
            'sent_at' => now(),
            'last_error' => null,
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => ReportStatus::FAILED,
            'last_error' => $error,
        ]);
    }
}
