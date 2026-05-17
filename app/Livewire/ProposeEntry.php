<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Modules\Community\Enums\ProposalType;
use App\Modules\Community\Services\OfflineProposalQueue;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ProposeEntry extends Component
{
    public string $type = 'pharmacy';
    public string $name = '';
    public string $phone = '';
    public string $zone = '';
    public string $code = '';
    public string $operator = '';
    public string $description = '';
    public string $commune = '';
    public string $reason = '';
    public bool $showForm = false;

    public function propose(): void
    {
        $proposalType = ProposalType::tryFrom($this->type);

        if ($proposalType === null) {
            $this->dispatch('toast', message: __('common.error'), variant: 'error');
            return;
        }

        $payload = match ($proposalType) {
            ProposalType::PHARMACY => $this->validatePharmacy(),
            ProposalType::EMERGENCY_CONTACT => $this->validateEmergencyContact(),
            ProposalType::USSD_CODE => $this->validateUssdCode(),
        };

        if ($payload === null) {
            return;
        }

        app(OfflineProposalQueue::class)->queue(
            proposalType: $proposalType->value,
            payload: $payload,
            reason: $this->reason ?: null,
        );

        $this->reset(['name', 'phone', 'zone', 'code', 'operator', 'description', 'commune', 'reason']);
        $this->showForm = false;

        $this->dispatch('toast', message: __('community.proposal_queued'), variant: 'success');
    }

    /** @return array<string, mixed>|null */
    private function validatePharmacy(): ?array
    {
        $this->validate([
            'name' => 'required|string|min:2|max:100',
            'phone' => 'required|string|min:8|max:20',
            'zone' => 'required|string|min:2|max:50',
        ]);

        return ['name' => $this->name, 'phone' => $this->phone, 'zone' => $this->zone];
    }

    /** @return array<string, mixed>|null */
    private function validateEmergencyContact(): ?array
    {
        $this->validate([
            'name' => 'required|string|min:2|max:100',
            'phone' => 'required|string|min:3|max:20',
            'commune' => 'required|string|min:2|max:50',
        ]);

        return ['name' => $this->name, 'phone' => $this->phone, 'commune' => $this->commune];
    }

    /** @return array<string, mixed>|null */
    private function validateUssdCode(): ?array
    {
        $this->validate([
            'code' => 'required|string|min:3|max:30',
            'operator' => 'required|string|in:mtn,moov,celtiis',
            'description' => 'required|string|min:5|max:200',
        ]);

        return ['code' => $this->code, 'operator' => $this->operator, 'description' => $this->description];
    }

    public function render(): View
    {
        return view('livewire.propose-entry');
    }
}
