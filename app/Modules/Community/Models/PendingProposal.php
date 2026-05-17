<?php

declare(strict_types=1);

namespace App\Modules\Community\Models;

use App\Modules\Community\Enums\ProposalType;
use App\Modules\Community\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class PendingProposal extends Model
{
    protected $fillable = [
        'proposal_type',
        'payload',
        'reason',
        'latitude',
        'longitude',
        'status',
        'attempts',
        'last_error',
        'remote_id',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'proposal_type' => ProposalType::class,
            'status' => ReportStatus::class,
            'payload' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
            'sent_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    /** @param Builder<PendingProposal> $query */
    public function scopeQueued(Builder $query): void
    {
        $query->whereIn('status', [ReportStatus::QUEUED, ReportStatus::FAILED])
            ->where('attempts', '<', 5);
    }

    /** @param Builder<PendingProposal> $query */
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

    public function markSent(string $remoteId): void
    {
        $this->update([
            'status' => ReportStatus::SENT,
            'remote_id' => $remoteId,
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
