<?php

declare(strict_types=1);

namespace App\Modules\Identity\Queries\GetIdentity;

use App\Modules\Identity\Models\Identity;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;

final class GetIdentityHandler implements QueryHandler
{
    public function handle(Query $query): ?Identity
    {
        assert($query instanceof GetIdentityQuery);

        return Identity::with('guardians')
            ->where('user_id', $query->userId)
            ->latest()
            ->first();
    }
}
