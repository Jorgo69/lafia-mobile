<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Queries\GetContactsByOperator;

use App\Shared\Enums\Operator;
use App\Shared\Bus\Query;

final readonly class GetContactsByOperatorQuery implements Query
{
    public function __construct(
        public Operator $operator,
        public ?string $departmentCode = null,
    ) {}
}
