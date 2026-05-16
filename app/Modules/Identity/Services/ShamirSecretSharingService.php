<?php

declare(strict_types=1);

namespace App\Modules\Identity\Services;

use InvalidArgumentException;
use RuntimeException;

/**
 * Shamir's Secret Sharing over GF(256).
 *
 * Splits a secret into N shares where any T shares can reconstruct it.
 * Works byte-by-byte: each byte of the secret is split independently
 * using a random polynomial of degree (threshold - 1) over GF(256).
 *
 * Zero external dependencies. Pure PHP + random_bytes().
 */
final class ShamirSecretSharingService
{
    /** @var array<int, int> Exponent table for GF(256) */
    private array $exp = [];

    /** @var array<int, int> Logarithm table for GF(256) */
    private array $log = [];

    public function __construct()
    {
        $this->initGaloisField();
    }

    /**
     * @return array<int, string> Indexed shares (1-based). Each share is base64-encoded.
     */
    public function split(string $secret, int $totalShares, int $threshold): array
    {
        if ($threshold < 2) {
            throw new InvalidArgumentException('Threshold must be at least 2.');
        }

        if ($totalShares < $threshold) {
            throw new InvalidArgumentException('Total shares must be >= threshold.');
        }

        if ($totalShares > 255) {
            throw new InvalidArgumentException('Maximum 255 shares supported.');
        }

        $secretBytes = array_values(unpack('C*', $secret) ?: []);
        $shares = array_fill(1, $totalShares, []);

        foreach ($secretBytes as $byte) {
            $coefficients = [$byte];
            for ($i = 1; $i < $threshold; $i++) {
                $coefficients[] = random_int(0, 255);
            }

            for ($x = 1; $x <= $totalShares; $x++) {
                $shares[$x][] = $this->evaluatePolynomial($coefficients, $x);
            }
        }

        $result = [];
        for ($x = 1; $x <= $totalShares; $x++) {
            $result[$x] = base64_encode(chr($x) . pack('C*', ...$shares[$x]));
        }

        return $result;
    }

    /**
     * @param array<string> $shares Base64-encoded shares (minimum = threshold).
     */
    public function reconstruct(array $shares): string
    {
        if (count($shares) < 2) {
            throw new InvalidArgumentException('Need at least 2 shares to reconstruct.');
        }

        $decoded = [];
        $xValues = [];

        foreach ($shares as $share) {
            $raw = base64_decode($share, true);

            if ($raw === false || strlen($raw) < 2) {
                throw new RuntimeException('Invalid share format.');
            }

            $x = ord($raw[0]);
            $bytes = array_values(unpack('C*', substr($raw, 1)) ?: []);

            $xValues[] = $x;
            $decoded[] = $bytes;
        }

        $secretLength = count($decoded[0]);
        $shareCount = count($decoded);
        $result = [];

        for ($byteIndex = 0; $byteIndex < $secretLength; $byteIndex++) {
            $yValues = [];
            for ($i = 0; $i < $shareCount; $i++) {
                $yValues[] = $decoded[$i][$byteIndex];
            }

            $result[] = $this->lagrangeInterpolate($xValues, $yValues);
        }

        return pack('C*', ...$result);
    }

    /**
     * Evaluate polynomial at point x in GF(256).
     *
     * @param array<int> $coefficients
     */
    private function evaluatePolynomial(array $coefficients, int $x): int
    {
        $result = 0;

        for ($i = count($coefficients) - 1; $i >= 0; $i--) {
            $result = $this->gfAdd($this->gfMul($result, $x), $coefficients[$i]);
        }

        return $result;
    }

    /**
     * Lagrange interpolation at x=0 in GF(256).
     *
     * @param array<int> $xValues
     * @param array<int> $yValues
     */
    private function lagrangeInterpolate(array $xValues, array $yValues): int
    {
        $count = count($xValues);
        $result = 0;

        for ($i = 0; $i < $count; $i++) {
            $numerator = 1;
            $denominator = 1;

            for ($j = 0; $j < $count; $j++) {
                if ($i === $j) {
                    continue;
                }

                // Evaluate at x=0, so (0 - xj) = xj in GF(256)
                $numerator = $this->gfMul($numerator, $xValues[$j]);
                $denominator = $this->gfMul($denominator, $this->gfAdd($xValues[$i], $xValues[$j]));
            }

            $lagrange = $this->gfMul($yValues[$i], $this->gfMul($numerator, $this->gfInv($denominator)));
            $result = $this->gfAdd($result, $lagrange);
        }

        return $result;
    }

    private function gfAdd(int $a, int $b): int
    {
        return $a ^ $b;
    }

    private function gfMul(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return $this->exp[($this->log[$a] + $this->log[$b]) % 255];
    }

    private function gfInv(int $a): int
    {
        if ($a === 0) {
            throw new RuntimeException('Cannot invert zero in GF(256).');
        }

        return $this->exp[255 - $this->log[$a]];
    }

    /**
     * Initialize GF(256) lookup tables.
     * Uses generator 0x03 with irreducible polynomial x^8 + x^4 + x^3 + x + 1 (0x11B).
     * This is the same field used by AES/Rijndael.
     */
    private function initGaloisField(): void
    {
        $this->exp = [];
        $this->log = [];

        $x = 1;

        for ($i = 0; $i < 256; $i++) {
            $this->exp[$i] = $x;

            if ($i < 255) {
                $this->log[$x] = $i;
            }

            // Russian peasant multiplication by generator 3 in GF(256)/0x11B
            // x = x * 3 = x * (2 + 1) = xtime(x) ^ x
            $highBit = $x & 0x80;
            $doubled = ($x << 1) & 0xFF;

            if ($highBit) {
                $doubled ^= 0x1B;
            }

            $x = $doubled ^ $x;
        }
    }
}
