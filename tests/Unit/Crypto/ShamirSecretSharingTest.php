<?php

declare(strict_types=1);

namespace Tests\Unit\Crypto;

use App\Modules\Identity\Services\ShamirSecretSharingService;
use PHPUnit\Framework\TestCase;

final class ShamirSecretSharingTest extends TestCase
{
    private ShamirSecretSharingService $shamir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shamir = new ShamirSecretSharingService();
    }

    public function test_split_and_reconstruct_with_minimum_shares(): void
    {
        $secret = 'my-secret-vault-key-123';
        $shares = $this->shamir->split($secret, totalShares: 3, threshold: 2);

        $this->assertCount(3, $shares);

        // Shares are 1-indexed
        $this->assertSame($secret, $this->shamir->reconstruct([$shares[1], $shares[2]]));
        $this->assertSame($secret, $this->shamir->reconstruct([$shares[2], $shares[3]]));
        $this->assertSame($secret, $this->shamir->reconstruct([$shares[1], $shares[3]]));
    }

    public function test_reconstruct_with_all_shares(): void
    {
        $secret = 'complete-secret';
        $shares = $this->shamir->split($secret, totalShares: 5, threshold: 3);
        $this->assertSame($secret, $this->shamir->reconstruct(array_values($shares)));
    }

    public function test_split_binary_data(): void
    {
        $secret = random_bytes(32);
        $shares = $this->shamir->split($secret, totalShares: 3, threshold: 2);
        $this->assertSame($secret, $this->shamir->reconstruct([$shares[1], $shares[3]]));
    }

    public function test_split_unicode(): void
    {
        $secret = 'Koffi cle secrete benin';
        $shares = $this->shamir->split($secret, totalShares: 3, threshold: 2);
        $this->assertSame($secret, $this->shamir->reconstruct([$shares[1], $shares[2]]));
    }

    public function test_shares_are_unique(): void
    {
        $shares = $this->shamir->split('test', totalShares: 5, threshold: 3);
        $this->assertCount(5, array_unique($shares));
    }

    public function test_insufficient_shares_throws(): void
    {
        $shares = $this->shamir->split('secret', totalShares: 3, threshold: 2);

        $this->expectException(\InvalidArgumentException::class);
        $this->shamir->reconstruct([$shares[1]]);
    }
}
