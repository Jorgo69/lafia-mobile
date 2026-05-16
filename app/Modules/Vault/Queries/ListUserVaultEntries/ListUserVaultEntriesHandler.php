<?php

declare(strict_types=1);

namespace App\Modules\Vault\Queries\ListUserVaultEntries;

use App\Modules\Vault\Models\Vault;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;
use Illuminate\Database\Eloquent\Collection;

final class ListUserVaultEntriesHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        assert($query instanceof ListUserVaultEntriesQuery);

        $builder = Vault::where('user_id', $query->userId)
            ->select(['id', 'data_type', 'label', 'public_key_fingerprint', 'created_at', 'updated_at']);

        if ($query->dataType !== null) {
            $builder->where('data_type', $query->dataType);
        }

        return $builder->orderByDesc('updated_at')->get();
    }
}
