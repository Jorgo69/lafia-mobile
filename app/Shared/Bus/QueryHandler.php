<?php

declare(strict_types=1);

namespace App\Shared\Bus;

interface QueryHandler
{
    public function handle(Query $query): mixed;
}
