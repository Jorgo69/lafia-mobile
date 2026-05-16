<?php

declare(strict_types=1);

namespace App\Modules\Emergency\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Emergency\Commands\ReportServiceUpdate\ReportServiceUpdateCommand;
use App\Modules\Emergency\Enums\EmergencyCategory;
use App\Shared\Enums\Operator;
use App\Modules\Emergency\Queries\GetAllCenters\GetAllCentersQuery;
use App\Modules\Emergency\Queries\GetContactsByOperator\GetContactsByOperatorQuery;
use App\Modules\Emergency\Queries\GetNearestCenter\GetNearestCenterQuery;
use App\Modules\Emergency\Requests\ReportServiceUpdateRequest;
use App\Modules\Emergency\Resources\EmergencyCenterResource;
use App\Modules\Emergency\Resources\EmergencyServiceUpdateResource;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class EmergencyController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $category = $request->query('category');
        $department = $request->query('department');

        $centers = $this->queryBus->dispatch(new GetAllCentersQuery(
            departmentCode: is_string($department) ? $department : null,
            category: is_string($category) ? EmergencyCategory::tryFrom($category) : null,
        ));

        return EmergencyCenterResource::collection($centers);
    }

    public function byOperator(Request $request, string $operator): AnonymousResourceCollection
    {
        $resolvedOperator = Operator::tryFrom($operator);

        if ($resolvedOperator === null) {
            abort(422, "Operateur invalide: {$operator}. Valeurs acceptees: mtn, moov, celtiis");
        }

        $department = $request->query('department');

        $centers = $this->queryBus->dispatch(new GetContactsByOperatorQuery(
            operator: $resolvedOperator,
            departmentCode: is_string($department) ? $department : null,
        ));

        return EmergencyCenterResource::collection($centers);
    }

    public function nearest(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'category' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $category = $request->query('category');

        $centers = $this->queryBus->dispatch(new GetNearestCenterQuery(
            latitude: (float) $request->query('lat'),
            longitude: (float) $request->query('lng'),
            category: is_string($category) ? EmergencyCategory::tryFrom($category) : null,
            limit: (int) ($request->query('limit', '3')),
        ));

        return EmergencyCenterResource::collection($centers);
    }

    public function reportUpdate(ReportServiceUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $update = $this->commandBus->dispatch(new ReportServiceUpdateCommand(
            emergencyContactId: $validated['emergency_contact_id'],
            reportedIssue: $validated['reported_issue'],
            suggestedPhoneNumber: $validated['suggested_phone_number'] ?? null,
            details: $validated['details'] ?? null,
            reporterLatitude: isset($validated['reporter_latitude']) ? (float) $validated['reporter_latitude'] : null,
            reporterLongitude: isset($validated['reporter_longitude']) ? (float) $validated['reporter_longitude'] : null,
        ));

        return (new EmergencyServiceUpdateResource($update))
            ->response()
            ->setStatusCode(201);
    }
}
