<?php

declare(strict_types=1);

namespace App\Modules\Vault\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vault\Commands\StoreHealthData\StoreHealthDataCommand;
use App\Modules\Vault\DTOs\HealthData;
use App\Modules\Vault\Queries\GetDecryptedHealthData\GetDecryptedHealthDataQuery;
use App\Modules\Vault\Queries\ListUserVaultEntries\ListUserVaultEntriesQuery;
use App\Modules\Vault\Requests\StoreHealthDataRequest;
use App\Modules\Vault\Resources\DecryptedHealthDataResource;
use App\Modules\Vault\Resources\VaultEntryResource;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class VaultController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = $this->resolveUserId($request);

        $entries = $this->queryBus->dispatch(new ListUserVaultEntriesQuery(
            userId: $userId,
        ));

        return VaultEntryResource::collection($entries);
    }

    public function storeHealth(StoreHealthDataRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $this->resolveUserId($request);

        $healthData = new HealthData(
            bloodType: $validated['blood_type'] ?? null,
            allergies: $validated['allergies'] ?? [],
            medications: $validated['medications'] ?? [],
            conditions: $validated['conditions'] ?? [],
            emergencyNotes: $validated['emergency_notes'] ?? null,
            weightKg: isset($validated['weight_kg']) ? (float) $validated['weight_kg'] : null,
            heightCm: isset($validated['height_cm']) ? (float) $validated['height_cm'] : null,
        );

        $vault = $this->commandBus->dispatch(new StoreHealthDataCommand(
            userId: $userId,
            label: $validated['label'],
            healthData: $healthData,
        ));

        return (new VaultEntryResource($vault))
            ->response()
            ->setStatusCode(201);
    }

    public function showDecrypted(Request $request, string $vaultId): DecryptedHealthDataResource
    {
        $userId = $this->resolveUserId($request);

        $healthData = $this->queryBus->dispatch(new GetDecryptedHealthDataQuery(
            vaultId: $vaultId,
            userId: $userId,
        ));

        return new DecryptedHealthDataResource($healthData);
    }

    /**
     * En mode NativePHP local, l'utilisateur est toujours l'utilisateur 1
     * (single-user app sur l'appareil). En production avec auth,
     * ce sera $request->user()->id.
     */
    private function resolveUserId(Request $request): int
    {
        return $request->user()?->id ?? 1;
    }
}
