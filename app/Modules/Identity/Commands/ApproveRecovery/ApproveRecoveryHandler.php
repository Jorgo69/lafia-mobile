<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\ApproveRecovery;

use App\Modules\Identity\Enums\RecoveryStatus;
use App\Modules\Identity\Models\RecoveryFragment;
use App\Modules\Identity\Models\RecoveryRequest;
use App\Shared\Bus\Command;
use App\Shared\Bus\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class ApproveRecoveryHandler implements CommandHandler
{
    public function handle(Command $command): RecoveryRequest
    {
        assert($command instanceof ApproveRecoveryCommand);

        try {
            return DB::transaction(function () use ($command): RecoveryRequest {
                $request = RecoveryRequest::findOrFail($command->recoveryRequestId);

                if ($request->isExpired()) {
                    $request->update(['status' => RecoveryStatus::EXPIRED]);
                    throw new RuntimeException('La demande de recuperation a expire.');
                }

                if ($request->status === RecoveryStatus::COMPLETED) {
                    throw new RuntimeException('La recuperation est deja terminee.');
                }

                RecoveryFragment::create([
                    'recovery_request_id' => $request->id,
                    'guardian_id' => $command->guardianId,
                    're_encrypted_fragment' => $command->reEncryptedFragment,
                ]);

                $request->increment('fragments_received');

                if ($request->status === RecoveryStatus::PENDING) {
                    $request->update(['status' => RecoveryStatus::IN_PROGRESS]);
                }

                $request->refresh();

                if ($request->hasEnoughFragments()) {
                    $request->update([
                        'status' => RecoveryStatus::COMPLETED,
                        'completed_at' => now(),
                    ]);
                    $request->refresh();
                }

                return $request;
            });
        } catch (\Throwable $e) {
            Log::error('ApproveRecovery failed', [
                'recovery_request_id' => $command->recoveryRequestId,
                'guardian_id' => $command->guardianId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
