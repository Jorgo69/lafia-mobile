<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\User;

trait CreatesTestUser
{
    protected function createTestUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@lafia.bj',
            'password' => bcrypt('password'),
        ]);
    }
}
