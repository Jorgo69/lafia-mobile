<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\RequestRecovery;

use App\Modules\Identity\Enums\DeviceStatus;
use App\Modules\Identity\Enums\GuardianStatus;
use App\Modules\Identity\Enums\RecoveryStatus;
use App\Modules\Identity\Models\Identity;
use App\Modules\Identity\Models\RecoveryRequest;
use App\Shared\Bus\Command;
use App\Shared\Bus\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class RequestRecoveryHandler implements CommandHandler
{
    public function handle(Command $command): RecoveryRequest
    {
        assert($command instanceof RequestRecoveryCommand);

        try {
            return DB::transaction(function () use ($command): RecoveryRequest {
                $identity = Identity::findOrFail($command->identityId);

                $activeGuardians = $identity->guardians()
                    ->where('status', GuardianStatus::ACCEPTED)
                    ->count();

                if ($activeGuardians < $identity->guardian_threshold) {
                    throw new RuntimeException(
                        "Pas assez de gardiens ({$activeGuardians}) pour atteindre le seuil ({$identity->guardian_threshold})."
                    );
                }

                $identity->update(['status' => DeviceStatus::LOST]);

                $request = RecoveryRequest::create([
                    'identity_id' => $identity->id,
                    'new_device_uuid' => $command->newDeviceUuid,
                    'new_device_public_key' => $command->newDevicePublicKey,
                    'status' => RecoveryStatus::PENDING,
                    'fragments_needed' => $identity->guardian_threshold,
                    'fragments_received' => 0,
                    'expires_at' => now()->addDays(7),
                ]);

                return $request->refresh();
            });
        } catch (\Throwable $e) {
            Log::error('RequestRecovery failed', [
                'identity_id' => $command->identityId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
