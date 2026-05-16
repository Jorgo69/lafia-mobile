<?php

declare(strict_types=1);

namespace App\Modules\Identity\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Commands\AddGuardian\AddGuardianCommand;
use App\Modules\Identity\Commands\ApproveRecovery\ApproveRecoveryCommand;
use App\Modules\Identity\Commands\RegisterDevice\RegisterDeviceCommand;
use App\Modules\Identity\Commands\RequestRecovery\RequestRecoveryCommand;
use App\Modules\Identity\Queries\GetIdentity\GetIdentityQuery;
use App\Modules\Identity\Queries\GetRecoveryStatus\GetRecoveryStatusQuery;
use App\Modules\Identity\Resources\GuardianResource;
use App\Modules\Identity\Resources\IdentityResource;
use App\Modules\Identity\Resources\RecoveryRequestResource;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class IdentityController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $userId = $request->user()?->id ?? 1;

        $identity = $this->queryBus->dispatch(new GetIdentityQuery(userId: $userId));

        if ($identity === null) {
            return response()->json(['data' => null, 'message' => 'No identity registered.'], 200);
        }

        return (new IdentityResource($identity))->response();
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_uuid' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_platform' => ['nullable', 'string', 'max:50'],
            'guardian_threshold' => ['nullable', 'integer', 'min:2', 'max:5'],
        ]);

        $userId = $request->user()?->id ?? 1;

        $identity = $this->commandBus->dispatch(new RegisterDeviceCommand(
            userId: $userId,
            deviceUuid: $validated['device_uuid'],
            deviceName: $validated['device_name'] ?? null,
            devicePlatform: $validated['device_platform'] ?? null,
            guardianThreshold: $validated['guardian_threshold'] ?? 2,
        ));

        return (new IdentityResource($identity))
            ->response()
            ->setStatusCode(201);
    }

    public function addGuardian(Request $request, string $identityId): JsonResponse
    {
        $validated = $request->validate([
            'alias' => ['required', 'string', 'max:255'],
            'public_key' => ['required', 'string'],
        ]);

        $userId = $request->user()?->id ?? 1;

        $guardian = $this->commandBus->dispatch(new AddGuardianCommand(
            identityId: $identityId,
            userId: $userId,
            guardianAlias: $validated['alias'],
            guardianPublicKey: $validated['public_key'],
        ));

        return (new GuardianResource($guardian))
            ->response()
            ->setStatusCode(201);
    }

    public function requestRecovery(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identity_id' => ['required', 'string', 'exists:identities,id'],
            'new_device_uuid' => ['required', 'string', 'max:255'],
            'new_device_public_key' => ['required', 'string'],
        ]);

        $recovery = $this->commandBus->dispatch(new RequestRecoveryCommand(
            identityId: $validated['identity_id'],
            newDeviceUuid: $validated['new_device_uuid'],
            newDevicePublicKey: $validated['new_device_public_key'],
        ));

        return (new RecoveryRequestResource($recovery))
            ->response()
            ->setStatusCode(201);
    }

    public function approveRecovery(Request $request, string $recoveryRequestId): JsonResponse
    {
        $validated = $request->validate([
            'guardian_id' => ['required', 'string', 'exists:guardians,id'],
            're_encrypted_fragment' => ['required', 'string'],
        ]);

        $recovery = $this->commandBus->dispatch(new ApproveRecoveryCommand(
            recoveryRequestId: $recoveryRequestId,
            guardianId: $validated['guardian_id'],
            reEncryptedFragment: $validated['re_encrypted_fragment'],
        ));

        return (new RecoveryRequestResource($recovery))->response();
    }

    public function recoveryStatus(string $recoveryRequestId): JsonResponse
    {
        $recovery = $this->queryBus->dispatch(new GetRecoveryStatusQuery(
            recoveryRequestId: $recoveryRequestId,
        ));

        return (new RecoveryRequestResource($recovery))->response();
    }
}
