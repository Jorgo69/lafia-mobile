<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Emergency;

use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Modules\Emergency\Enums\EmergencyCenterType;
use App\Shared\Enums\Operator;
use PHPUnit\Framework\TestCase;

final class EmergencyEnumsTest extends TestCase
{
    public function test_operator_has_three_values(): void
    {
        $this->assertCount(3, Operator::cases());
        $this->assertSame('mtn', Operator::MTN->value);
        $this->assertSame('moov', Operator::MOOV->value);
        $this->assertSame('celtiis', Operator::CELTIIS->value);
    }

    public function test_center_type_values(): void
    {
        $cases = EmergencyCenterType::cases();
        $this->assertGreaterThanOrEqual(2, count($cases));
    }

    public function test_emergency_category_values(): void
    {
        $this->assertSame('civil_protection', EmergencyCategory::CIVIL_PROTECTION->value);
        $this->assertSame('fire', EmergencyCategory::FIRE->value);
        $this->assertSame('medical', EmergencyCategory::MEDICAL->value);
        $this->assertSame('police', EmergencyCategory::POLICE->value);
    }
}
