<?php

declare(strict_types=1);

namespace Tests\Unit\Crypto;

use App\Services\Crypto\CryptoKeyPair;
use App\Services\Crypto\EncryptionService;
use App\Services\Crypto\KeyPairManager;
use PHPUnit\Framework\TestCase;

final class EncryptionServiceTest extends TestCase
{
    private EncryptionService $encryption;
    private CryptoKeyPair $keyPair;

    protected function setUp(): void
    {
        parent::setUp();
        $manager = new KeyPairManager(sys_get_temp_dir());
        $this->keyPair = $manager->generateKeyPair();
        $this->encryption = new EncryptionService();
    }

    public function test_encrypt_and_decrypt_string(): void
    {
        $plaintext = 'Groupe sanguin: O+';
        $encrypted = $this->encryption->encrypt($plaintext, $this->keyPair->publicKey);
        $decrypted = $this->encryption->decrypt($encrypted, $this->keyPair->secretKey);

        $this->assertSame($plaintext, $decrypted);
        $this->assertNotSame($plaintext, $encrypted);
    }

    public function test_encrypt_produces_different_ciphertext_each_time(): void
    {
        $plaintext = 'Allergies: penicilline';
        $e1 = $this->encryption->encrypt($plaintext, $this->keyPair->publicKey);
        $e2 = $this->encryption->encrypt($plaintext, $this->keyPair->publicKey);

        $this->assertNotSame($e1, $e2);
        $this->assertSame($plaintext, $this->encryption->decrypt($e1, $this->keyPair->secretKey));
        $this->assertSame($plaintext, $this->encryption->decrypt($e2, $this->keyPair->secretKey));
    }

    public function test_encrypt_empty_string(): void
    {
        $encrypted = $this->encryption->encrypt('', $this->keyPair->publicKey);
        $this->assertSame('', $this->encryption->decrypt($encrypted, $this->keyPair->secretKey));
    }

    public function test_encrypt_unicode_string(): void
    {
        $plaintext = 'Koffi Adje — arachide';
        $encrypted = $this->encryption->encrypt($plaintext, $this->keyPair->publicKey);
        $this->assertSame($plaintext, $this->encryption->decrypt($encrypted, $this->keyPair->secretKey));
    }

    public function test_encrypt_long_data(): void
    {
        $plaintext = str_repeat('A', 10000);
        $encrypted = $this->encryption->encrypt($plaintext, $this->keyPair->publicKey);
        $this->assertSame($plaintext, $this->encryption->decrypt($encrypted, $this->keyPair->secretKey));
    }

    public function test_decrypt_with_wrong_key_fails(): void
    {
        $encrypted = $this->encryption->encrypt('secret', $this->keyPair->publicKey);
        $otherPair = (new KeyPairManager(sys_get_temp_dir()))->generateKeyPair();

        $this->expectException(\RuntimeException::class);
        $this->encryption->decrypt($encrypted, $otherPair->secretKey);
    }

    public function test_decrypt_tampered_ciphertext_fails(): void
    {
        $encrypted = $this->encryption->encrypt('test', $this->keyPair->publicKey);
        $this->expectException(\RuntimeException::class);
        $this->encryption->decrypt($encrypted . 'TAMPERED', $this->keyPair->secretKey);
    }
}
