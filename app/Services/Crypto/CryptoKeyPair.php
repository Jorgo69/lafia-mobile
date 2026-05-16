<?php

declare(strict_types=1);

namespace App\Services\Crypto;

final readonly class CryptoKeyPair
{
    public function __construct(
        public string $publicKey,
        public string $secretKey,
    ) {}
}
