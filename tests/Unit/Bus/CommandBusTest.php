<?php

declare(strict_types=1);

namespace Tests\Unit\Bus;

use App\Shared\Bus\Command;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\CommandHandler;
use Tests\TestCase;

final class CommandBusTest extends TestCase
{
    public function test_dispatch_executes_handler(): void
    {
        $bus = app(CommandBus::class);

        $cmd = new class implements Command {};
        $handler = new class implements CommandHandler {
            public static bool $executed = false;
            public function handle(Command $command): mixed
            {
                self::$executed = true;
                return 'result';
            }
        };

        $bus->register($cmd::class, $handler::class);
        $this->app->instance($handler::class, $handler);

        $result = $bus->dispatch($cmd);

        $this->assertTrue($handler::$executed);
        $this->assertSame('result', $result);
    }

    public function test_dispatch_without_handler_throws(): void
    {
        $bus = app(CommandBus::class);
        $cmd = new class implements Command {};

        $this->expectException(\RuntimeException::class);
        $bus->dispatch($cmd);
    }
}
