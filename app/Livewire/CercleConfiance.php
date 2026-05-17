<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\RequiresAuth;
use App\Modules\Identity\Enums\GuardianStatus;
use App\Modules\Identity\Enums\RecoveryStatus;
use App\Modules\Identity\Models\Guardian;
use App\Modules\Identity\Models\Identity;
use App\Modules\Identity\Models\RecoveryRequest;
use App\Modules\Identity\Commands\RegisterDevice\RegisterDeviceCommand;
use App\Modules\Identity\Commands\AddGuardian\AddGuardianCommand;
use App\Modules\Identity\Commands\RequestRecovery\RequestRecoveryCommand;
use App\Shared\Bus\CommandBus;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Cercle de Confiance - Lafia')]
final class CercleConfiance extends Component
{
    use RequiresAuth;
    public bool $embedded = false;
    public ?array $identity = null;

    /** @var array<int, array<string, mixed>> */
    public array $guardians = [];

    public ?array $activeRecovery = null;

    public bool $showAddGuardian = false;
    public string $guardianAlias = '';

    public bool $showRecovery = false;

    public function mount(): void
    {
        $this->loadData();
    }

    public function registerDevice(): void
    {
        $commandBus = app(CommandBus::class);

        $commandBus->dispatch(new RegisterDeviceCommand(
            userId: 1,
            deviceUuid: 'device-' . Str::uuid(),
            deviceName: php_uname('n'),
            devicePlatform: PHP_OS_FAMILY,
            guardianThreshold: 2,
        ));

        $this->loadData();
    }

    public function addGuardian(): void
    {
        if (!$this->identity || $this->guardianAlias === '') {
            return;
        }

        $commandBus = app(CommandBus::class);

        $guardianKeyPair = sodium_crypto_box_keypair();
        $guardianPublicKey = base64_encode(sodium_crypto_box_publickey($guardianKeyPair));

        $commandBus->dispatch(new AddGuardianCommand(
            identityId: $this->identity['id'],
            userId: 1,
            guardianAlias: $this->guardianAlias,
            guardianPublicKey: $guardianPublicKey,
        ));

        $this->guardianAlias = '';
        $this->showAddGuardian = false;
        $this->loadData();
    }

    public function requestRecovery(): void
    {
        if (!$this->identity) {
            return;
        }

        $commandBus = app(CommandBus::class);

        $newKeyPair = sodium_crypto_box_keypair();
        $newPublicKey = base64_encode(sodium_crypto_box_publickey($newKeyPair));

        $commandBus->dispatch(new RequestRecoveryCommand(
            identityId: $this->identity['id'],
            newDeviceUuid: 'new-device-' . Str::uuid(),
            newDevicePublicKey: $newPublicKey,
        ));

        $this->showRecovery = false;
        $this->loadData();
    }

    private function loadData(): void
    {
        $identity = Identity::with('guardians')
            ->where('user_id', 1)
            ->latest()
            ->first();

        if ($identity) {
            $this->identity = [
                'id' => $identity->id,
                'device_name' => $identity->device_name,
                'device_platform' => $identity->device_platform,
                'status' => $identity->status->value,
                'fingerprint' => $identity->public_key_fingerprint,
                'threshold' => $identity->guardian_threshold,
            ];

            $this->guardians = $identity->guardians
                ->filter(fn (Guardian $g) => $g->status !== GuardianStatus::REVOKED)
                ->map(fn (Guardian $g) => [
                    'id' => $g->id,
                    'alias' => $g->guardian_alias,
                    'status' => $g->status->value,
                    'status_label' => $g->status->label(),
                    'accepted_at' => $g->accepted_at?->format('d/m/Y'),
                ])->values()->toArray();

            $this->activeRecovery = RecoveryRequest::where('identity_id', $identity->id)
                ->whereIn('status', [RecoveryStatus::PENDING, RecoveryStatus::IN_PROGRESS])
                ->latest()
                ->first()
                ?->only(['id', 'status', 'fragments_needed', 'fragments_received', 'expires_at']);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.cercle-confiance');
    }
}
