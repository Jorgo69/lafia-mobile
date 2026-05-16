<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use RuntimeException;

final class EncryptionService
{
    public function encrypt(string $plaintext, string $recipientPublicKey): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ephemeralKeyPair = sodium_crypto_box_keypair();
        $ephemeralPublicKey = sodium_crypto_box_publickey($ephemeralKeyPair);
        $ephemeralSecretKey = sodium_crypto_box_secretkey($ephemeralKeyPair);

        $sharedKey = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $ephemeralSecretKey,
            $recipientPublicKey,
        );

        $encrypted = sodium_crypto_box($plaintext, $nonce, $sharedKey);

        sodium_memzero($ephemeralSecretKey);
        sodium_memzero($sharedKey);

        return base64_encode($nonce . $ephemeralPublicKey . $encrypted);
    }

    public function decrypt(string $ciphertext, string $recipientSecretKey): string
    {
        $decoded = base64_decode($ciphertext, true);

        if ($decoded === false) {
            throw new RuntimeException('Invalid ciphertext encoding.');
        }

        $nonceLength = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
        $publicKeyLength = SODIUM_CRYPTO_BOX_PUBLICKEYBYTES;

        if (strlen($decoded) < $nonceLength + $publicKeyLength) {
            throw new RuntimeException('Ciphertext is too short.');
        }

        $nonce = substr($decoded, 0, $nonceLength);
        $ephemeralPublicKey = substr($decoded, $nonceLength, $publicKeyLength);
        $encrypted = substr($decoded, $nonceLength + $publicKeyLength);

        $sharedKey = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $recipientSecretKey,
            $ephemeralPublicKey,
        );

        $plaintext = sodium_crypto_box_open($encrypted, $nonce, $sharedKey);

        sodium_memzero($sharedKey);

        if ($plaintext === false) {
            throw new RuntimeException('Decryption failed. Invalid key or corrupted data.');
        }

        return $plaintext;
    }
}
