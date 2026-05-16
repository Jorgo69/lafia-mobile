<?php

declare(strict_types=1);

namespace Tests\Unit\Crypto;

use App\Services\Crypto\KeyPairManager;
use PHPUnit\Framework\TestCase;

final class KeyPairManagerTest extends TestCase
{
    private string $testKeyDir;
    private KeyPairManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testKeyDir = sys_get_temp_dir() . '/lafia-keypair-test-' . uniqid();
        mkdir($this->testKeyDir, 0700, true);
        $this->manager = new KeyPairManager($this->testKeyDir);
    }

    protected function tearDown(): void
    {
        // Cleanup recursively
        $this->deleteDir($this->testKeyDir);
        parent::tearDown();
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function test_generate_returns_keypair(): void
    {
        $pair = $this->manager->generateKeyPair();
        $this->assertSame(SODIUM_CRYPTO_BOX_PUBLICKEYBYTES, strlen($pair->publicKey));
        $this->assertSame(SODIUM_CRYPTO_BOX_SECRETKEYBYTES, strlen($pair->secretKey));
    }

    public function test_store_and_load_keypair(): void
    {
        $pair = $this->manager->generateKeyPair();
        $this->manager->storeKeyPair('test-user', $pair);

        $this->assertTrue($this->manager->hasKeyPair('test-user'));

        $loaded = $this->manager->loadKeyPair('test-user');
        $this->assertSame($pair->publicKey, $loaded->publicKey);
        $this->assertSame($pair->secretKey, $loaded->secretKey);
    }

    public function test_has_key_pair_false_when_missing(): void
    {
        $this->assertFalse($this->manager->hasKeyPair('nonexistent'));
    }

    public function test_regenerate_creates_different_keys(): void
    {
        $pair1 = $this->manager->generateKeyPair();
        $pair2 = $this->manager->generateKeyPair();
        $this->assertNotSame($pair1->publicKey, $pair2->publicKey);
    }

    public function test_load_nonexistent_throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->manager->loadKeyPair('does-not-exist');
    }
}
