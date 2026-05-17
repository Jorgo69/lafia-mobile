<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->markOnboarded();
    }

    protected function markOnboarded(): void
    {
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'onboarding_done'],
                ['value' => '1'],
            );
        } catch (\Throwable) {
            // Table may not exist in some test contexts
        }
    }
}
