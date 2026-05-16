<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Commands\ReportServiceUpdate;

use App\Modules\Emergency\Enums\UpdateStatus;
use App\Modules\Emergency\Models\EmergencyServiceUpdate;
use App\Shared\Bus\Command;
use App\Shared\Bus\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ReportServiceUpdateHandler implements CommandHandler
{
    public function handle(Command $command): EmergencyServiceUpdate
    {
        assert($command instanceof ReportServiceUpdateCommand);

        try {
            return DB::transaction(function () use ($command): EmergencyServiceUpdate {
                $update = EmergencyServiceUpdate::create([
                    'emergency_contact_id' => $command->emergencyContactId,
                    'status' => UpdateStatus::PENDING,
                    'reported_issue' => $command->reportedIssue,
                    'suggested_phone_number' => $command->suggestedPhoneNumber,
                    'details' => $command->details,
                    'reporter_latitude' => $command->reporterLatitude,
                    'reporter_longitude' => $command->reporterLongitude,
                ]);

                return $update->refresh();
            });
        } catch (\Throwable $e) {
            Log::error('ReportServiceUpdate failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
