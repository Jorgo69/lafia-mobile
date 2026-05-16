<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Services;

use App\Shared\Enums\Operator;

final class OperatorDetectorService
{
    /**
     * Prefixes connus des operateurs beninois.
     * Source: ARCEP Benin — plages de numerotation mobile.
     *
     * @var array<string, Operator>
     */
    private const PREFIX_MAP = [
        // MTN Benin
        '229051' => Operator::MTN,
        '229052' => Operator::MTN,
        '229053' => Operator::MTN,
        '229054' => Operator::MTN,
        '229055' => Operator::MTN,
        '229056' => Operator::MTN,
        '229057' => Operator::MTN,
        '229058' => Operator::MTN,
        '229059' => Operator::MTN,
        '22901' => Operator::MTN,

        // Moov Africa
        '229040' => Operator::MOOV,
        '229041' => Operator::MOOV,
        '229042' => Operator::MOOV,
        '229043' => Operator::MOOV,
        '229044' => Operator::MOOV,
        '229045' => Operator::MOOV,
        '229046' => Operator::MOOV,
        '229060' => Operator::MOOV,
        '229061' => Operator::MOOV,
        '229062' => Operator::MOOV,
        '229063' => Operator::MOOV,
        '229064' => Operator::MOOV,
        '229065' => Operator::MOOV,
        '229066' => Operator::MOOV,
        '229067' => Operator::MOOV,
        '229068' => Operator::MOOV,
        '229069' => Operator::MOOV,

        // Celtiis
        '229047' => Operator::CELTIIS,
        '229048' => Operator::CELTIIS,
        '229049' => Operator::CELTIIS,
    ];

    public function detectFromPhoneNumber(string $phoneNumber): ?Operator
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        if ($cleaned === null) {
            return null;
        }

        if (!str_starts_with($cleaned, '229')) {
            $cleaned = '229' . $cleaned;
        }

        // Essayer le prefixe le plus long d'abord (6 digits) puis 5 digits
        foreach ([6, 5] as $length) {
            $prefix = substr($cleaned, 0, $length);

            if (isset(self::PREFIX_MAP[$prefix])) {
                return self::PREFIX_MAP[$prefix];
            }
        }

        return null;
    }

    public function detectFromSimInfo(?string $simOperatorName): ?Operator
    {
        if ($simOperatorName === null || $simOperatorName === '') {
            return null;
        }

        $normalized = mb_strtolower($simOperatorName);

        return match (true) {
            str_contains($normalized, 'mtn') => Operator::MTN,
            str_contains($normalized, 'moov') => Operator::MOOV,
            str_contains($normalized, 'celtiis'),
            str_contains($normalized, 'glo') => Operator::CELTIIS,
            default => null,
        };
    }
}
