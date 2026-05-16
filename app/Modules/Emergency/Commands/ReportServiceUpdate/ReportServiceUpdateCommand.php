<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Commands\ReportServiceUpdate;

use App\Shared\Bus\Command;

final readonly class ReportServiceUpdateCommand implements Command
{
    public function __construct(
        public string $emergencyContactId,
        public string $reportedIssue,
        public ?string $suggestedPhoneNumber = null,
        public ?string $details = null,
        public ?float $reporterLatitude = null,
        public ?float $reporterLongitude = null,
    ) {}
}
