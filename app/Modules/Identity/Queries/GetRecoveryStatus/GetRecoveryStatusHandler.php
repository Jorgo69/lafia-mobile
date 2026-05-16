<?php

declare(strict_types=1);

namespace App\Modules\Identity\Queries\GetRecoveryStatus;

use App\Modules\Identity\Models\RecoveryRequest;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;

final class GetRecoveryStatusHandler implements QueryHandler
{
    public function handle(Query $query): RecoveryRequest
    {
        assert($query instanceof GetRecoveryStatusQuery);

        return RecoveryRequest::with(['fragments.guardian', 'identity'])
            ->findOrFail($query->recoveryRequestId);
    }
}
