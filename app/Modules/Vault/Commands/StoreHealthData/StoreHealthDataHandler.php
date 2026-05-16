<?php

declare(strict_types=1);

namespace App\Modules\Vault\Commands\StoreHealthData;

use App\Modules\Vault\Enums\VaultDataType;
use App\Modules\Vault\Models\Vault;
use App\Services\Crypto\EncryptionService;
use App\Services\Crypto\KeyPairManager;
use App\Shared\Bus\Command;
use App\Shared\Bus\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class StoreHealthDataHandler implements CommandHandler
{
    public function __construct(
        private readonly EncryptionService $encryptionService,
        private readonly KeyPairManager $keyPairManager,
    ) {}

    public function handle(Command $command): Vault
    {
        assert($command instanceof StoreHealthDataCommand);

        try {
            return DB::transaction(function () use ($command): Vault {
                $keyIdentifier = 'user-' . $command->userId;

                if (!$this->keyPairManager->hasKeyPair($keyIdentifier)) {
                    $keyPair = $this->keyPairManager->generateKeyPair();
                    $this->keyPairManager->storeKeyPair($keyIdentifier, $keyPair);
                } else {
                    $keyPair = $this->keyPairManager->loadKeyPair($keyIdentifier);
                }

                $plaintext = $command->healthData->toJson();
                $encrypted = $this->encryptionService->encrypt($plaintext, $keyPair->publicKey);

                $fingerprint = sodium_bin2hex(
                    sodium_crypto_generichash($keyPair->publicKey, '', 16)
                );

                $vault = Vault::create([
                    'user_id' => $command->userId,
                    'data_type' => VaultDataType::HEALTH,
                    'label' => $command->label,
                    'encrypted_payload' => $encrypted,
                    'public_key_fingerprint' => $fingerprint,
                ]);

                return $vault->refresh();
            });
        } catch (\Throwable $e) {
            Log::error('StoreHealthData failed', [
                'user_id' => $command->userId,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Impossible de sauvegarder les donnees de sante.');
        }
    }
}
