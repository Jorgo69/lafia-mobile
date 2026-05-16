<?php

declare(strict_types=1);

namespace App\Modules\Identity\Commands\AddGuardian;

use App\Modules\Identity\Enums\GuardianStatus;
use App\Modules\Identity\Models\Guardian;
use App\Modules\Identity\Models\Identity;
use App\Modules\Identity\Services\ShamirSecretSharingService;
use App\Services\Crypto\EncryptionService;
use App\Services\Crypto\KeyPairManager;
use App\Shared\Bus\Command;
use App\Shared\Bus\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class AddGuardianHandler implements CommandHandler
{
    public function __construct(
        private readonly ShamirSecretSharingService $shamir,
        private readonly EncryptionService $encryptionService,
        private readonly KeyPairManager $keyPairManager,
    ) {}

    public function handle(Command $command): Guardian
    {
        assert($command instanceof AddGuardianCommand);

        try {
            return DB::transaction(function () use ($command): Guardian {
                $identity = Identity::where('id', $command->identityId)
                    ->where('user_id', $command->userId)
                    ->firstOrFail();

                $existingCount = Guardian::where('identity_id', $identity->id)
                    ->whereNot('status', GuardianStatus::REVOKED)
                    ->count();

                $nextIndex = $existingCount + 1;
                $totalShares = $identity->guardian_threshold + 1;

                $keyIdentifier = 'user-' . $command->userId;

                if (!$this->keyPairManager->hasKeyPair($keyIdentifier)) {
                    throw new RuntimeException('Aucune cle Vault trouvee pour cet utilisateur.');
                }

                $keyPair = $this->keyPairManager->loadKeyPair($keyIdentifier);

                $shares = $this->shamir->split(
                    $keyPair->secretKey,
                    $totalShares,
                    $identity->guardian_threshold,
                );

                if (!isset($shares[$nextIndex])) {
                    throw new RuntimeException('Nombre maximum de gardiens atteint.');
                }

                $guardianPubKey = base64_decode($command->guardianPublicKey, true);

                if ($guardianPubKey === false || strlen($guardianPubKey) !== SODIUM_CRYPTO_BOX_PUBLICKEYBYTES) {
                    throw new RuntimeException('Cle publique du gardien invalide.');
                }

                $encryptedFragment = $this->encryptionService->encrypt(
                    $shares[$nextIndex],
                    $guardianPubKey,
                );

                $guardian = Guardian::create([
                    'identity_id' => $identity->id,
                    'guardian_alias' => $command->guardianAlias,
                    'guardian_public_key' => $command->guardianPublicKey,
                    'encrypted_fragment' => $encryptedFragment,
                    'fragment_index' => $nextIndex,
                    'status' => GuardianStatus::ACCEPTED,
                    'accepted_at' => now(),
                ]);

                return $guardian->refresh();
            });
        } catch (\Throwable $e) {
            Log::error('AddGuardian failed', [
                'identity_id' => $command->identityId,
                'alias' => $command->guardianAlias,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
