<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Queries\GetContactsByOperator;

use App\Modules\Emergency\Models\EmergencyCenter;
use App\Shared\Bus\Query;
use App\Shared\Bus\QueryHandler;
use Illuminate\Database\Eloquent\Collection;

final class GetContactsByOperatorHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        assert($query instanceof GetContactsByOperatorQuery);

        $builder = EmergencyCenter::with(['department', 'contacts' => function ($q) use ($query) {
            $q->where('operator', $query->operator)
                ->where('is_active', true)
                ->orderByDesc('priority_score');
        }])
            ->where('is_active', true);

        if ($query->departmentCode !== null) {
            $builder->whereHas('department', function ($q) use ($query) {
                $q->where('code', $query->departmentCode);
            });
        }

        return $builder->orderBy('name')->get();
    }
}
