<?php

declare(strict_types=1);

namespace App\Modules\Vault\Queries\GetDecryptedHealthData;

use App\Modules\Vault\DTOs\HealthData;
use App\Modules\Vault\Models\Vault;
use App\Services\Crypto\EncryptionService;
use App\Services\Crypto\KeyPairManager;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use RuntimeException;

final class GetDecryptedHealthDataHandler implements QueryHandler
{
    public function __construct(
        private readonly EncryptionService $encryptionService,
        private readonly KeyPairManager $keyPairManager,
    ) {}

    public function handle(Query $query): HealthData
    {
        assert($query instanceof GetDecryptedHealthDataQuery);

        $vault = Vault::where('id', $query->vaultId)
            ->where('user_id', $query->userId)
            ->firstOrFail();

        $keyIdentifier = 'user-' . $query->userId;

        if (!$this->keyPairManager->hasKeyPair($keyIdentifier)) {
            throw new RuntimeException('No decryption key found for this user. Data cannot be decrypted.');
        }

        $keyPair = $this->keyPairManager->loadKeyPair($keyIdentifier);

        $decrypted = $this->encryptionService->decrypt(
            $vault->encrypted_payload,
            $keyPair->secretKey,
        );

        return HealthData::fromJson($decrypted);
    }
}
