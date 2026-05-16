<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Queries\GetAllCenters;

use App\Modules\Emergency\Models\EmergencyCenter;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;
use Illuminate\Database\Eloquent\Collection;

final class GetAllCentersHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        assert($query instanceof GetAllCentersQuery);

        $builder = EmergencyCenter::with(['department', 'contacts'])
            ->where('is_active', true);

        if ($query->departmentCode !== null) {
            $builder->whereHas('department', function ($q) use ($query) {
                $q->where('code', $query->departmentCode);
            });
        }

        if ($query->category !== null) {
            $builder->where('category', $query->category);
        }

        return $builder->orderBy('name')->get();
    }
}
