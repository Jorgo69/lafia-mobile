<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\RegisterDevice;

use App\Modules\Identity\Enums\DeviceStatus;
use App\Modules\Identity\Models\Identity;
use App\Services\Crypto\KeyPairManager;
use App\Shared\Bus\Command;
use App\Shared\Bus\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class RegisterDeviceHandler implements CommandHandler
{
    public function __construct(
        private readonly KeyPairManager $keyPairManager,
    ) {}

    public function handle(Command $command): Identity
    {
        assert($command instanceof RegisterDeviceCommand);

        try {
            return DB::transaction(function () use ($command): Identity {
                $keyIdentifier = 'user-' . $command->userId;

                if (!$this->keyPairManager->hasKeyPair($keyIdentifier)) {
                    $keyPair = $this->keyPairManager->generateKeyPair();
                    $this->keyPairManager->storeKeyPair($keyIdentifier, $keyPair);
                } else {
                    $keyPair = $this->keyPairManager->loadKeyPair($keyIdentifier);
                }

                $fingerprint = sodium_bin2hex(
                    sodium_crypto_generichash($keyPair->publicKey, '', 16)
                );

                $identity = Identity::create([
                    'user_id' => $command->userId,
                    'device_uuid' => $command->deviceUuid,
                    'device_name' => $command->deviceName,
                    'device_platform' => $command->devicePlatform,
                    'status' => DeviceStatus::ACTIVE,
                    'public_key_fingerprint' => $fingerprint,
                    'guardian_threshold' => $command->guardianThreshold,
                ]);

                return $identity->refresh();
            });
        } catch (\Throwable $e) {
            Log::error('RegisterDevice failed', [
                'user_id' => $command->userId,
                'device_uuid' => $command->deviceUuid,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Impossible d\'enregistrer l\'appareil.');
        }
    }
}
