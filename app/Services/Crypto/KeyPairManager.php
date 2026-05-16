<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use RuntimeException;

final class KeyPairManager
{
    public function __construct(
        private readonly string $storagePath,
    ) {}

    public function generateKeyPair(): CryptoKeyPair
    {
        $keyPair = sodium_crypto_box_keypair();

        return new CryptoKeyPair(
            publicKey: sodium_crypto_box_publickey($keyPair),
            secretKey: sodium_crypto_box_secretkey($keyPair),
        );
    }

    public function storeKeyPair(string $identifier, CryptoKeyPair $keyPair): void
    {
        $dir = $this->storagePath . '/' . $identifier;

        if (!is_dir($dir) && !mkdir($dir, 0700, true)) {
            throw new RuntimeException("Cannot create key directory: {$dir}");
        }

        file_put_contents($dir . '/public.key', $keyPair->publicKey);
        file_put_contents($dir . '/secret.key', $keyPair->secretKey);

        chmod($dir . '/secret.key', 0600);
    }

    public function loadKeyPair(string $identifier): CryptoKeyPair
    {
        $dir = $this->storagePath . '/' . $identifier;

        $publicKey = @file_get_contents($dir . '/public.key');
        $secretKey = @file_get_contents($dir . '/secret.key');

        if ($publicKey === false || $secretKey === false) {
            throw new RuntimeException("Key pair not found for: {$identifier}");
        }

        return new CryptoKeyPair(
            publicKey: $publicKey,
            secretKey: $secretKey,
        );
    }

    public function hasKeyPair(string $identifier): bool
    {
        $dir = $this->storagePath . '/' . $identifier;

        return file_exists($dir . '/public.key') && file_exists($dir . '/secret.key');
    }
}
