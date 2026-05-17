<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Enums\UssdTab;
use App\Modules\Ussd\Enums\UssdCategory;
use App\Modules\Ussd\Models\UssdCode;
use App\Modules\Ussd\Models\UssdFavorite;
use App\Services\Settings\SettingsService;
use App\Shared\Enums\Operator;
use Illuminate\Support\Collection;
use Livewire\Component;

final class UssdGuide extends Component
{
    // --- Etat operateur (detecte puis confirmable) ---
    public string $activeOperator = 'mtn';
    public string $secondOperator = '';

    // --- Navigation ---
    public UssdTab $activeTab = UssdTab::LIST;
    public string $activeCategory = '';

    // --- Guide mode ---
    public ?int $activeCodeId = null;
    /** @var array<string, string> */
    public array $paramValues = [];

    // --- Device ---
    public string $deviceId = 'local-device';

    public function mount(): void
    {
        $settings = app(SettingsService::class);
        $this->activeOperator = $settings->get('ussd_operator', 'mtn') ?? 'mtn';
        $this->activeCategory = $settings->get('ussd_category', '') ?? '';
        $this->activeTab = UssdTab::tryFrom($settings->get('ussd_tab', 'list') ?? 'list') ?? UssdTab::LIST;
    }

    public function setOperator(string $operator): void
    {
        $this->activeOperator = $operator;
        $this->activeCodeId = null;
        $this->paramValues = [];
        app(SettingsService::class)->set('ussd_operator', $operator);
    }

    public function setCategory(string $category): void
    {
        $this->activeCategory = ($category === '' || $this->activeCategory === $category) ? '' : $category;
        $this->activeCodeId = null;
        $this->paramValues = [];
        app(SettingsService::class)->set('ussd_category', $this->activeCategory);
    }

    public function setTab(string $tab): void
    {
        $t = UssdTab::tryFrom($tab);
        if ($t === null) {
            return;
        }
        $this->activeTab = $t;
        $this->activeCodeId = null;
        $this->paramValues = [];
        app(SettingsService::class)->set('ussd_tab', $tab);
    }

    public function selectCode(int $codeId): void
    {
        $code = UssdCode::find($codeId);

        if ($code === null) {
            return;
        }

        if (!$code->needsParams() && $code->action_type === \App\Modules\Ussd\Enums\UssdActionType::DIRECT) {
            // Direct: lancer immediatement (le JS s'en charge via l'event)
            $this->trackUsage($codeId);
            $this->dispatch('launch-ussd', uri: $this->buildCallUri($code->toTelUri()));
            return;
        }

        // Guided ou Menu: ouvrir le formulaire ou lancer le menu
        if ($code->action_type === \App\Modules\Ussd\Enums\UssdActionType::MENU && !$code->needsParams()) {
            $this->trackUsage($codeId);
            $this->dispatch('launch-ussd', uri: $this->buildCallUri($code->toTelUri()));
            return;
        }

        $this->activeCodeId = $codeId;
        $this->paramValues = [];

        // Pre-remplir depuis les favoris si disponible
        $favorite = UssdFavorite::where('device_id', $this->deviceId)
            ->where('ussd_code_id', $codeId)
            ->first();

        if ($favorite !== null && is_array($favorite->saved_params)) {
            $this->paramValues = $favorite->saved_params;
        }
    }

    public function launchGuided(): void
    {
        $code = UssdCode::find($this->activeCodeId);

        if ($code === null) {
            return;
        }

        // Verifier que tous les params requis sont remplis
        foreach ($code->getParamDefinitions() as $param) {
            if (empty($this->paramValues[$param['key']] ?? '')) {
                return;
            }
        }

        $this->trackUsage($code->id, $this->paramValues);
        $this->dispatch('launch-ussd', uri: $this->buildCallUri($code->toTelUri($this->paramValues)));

        $this->activeCodeId = null;
        $this->paramValues = [];
    }

    public function cancelGuided(): void
    {
        $this->activeCodeId = null;
        $this->paramValues = [];
    }

    public function toggleFavorite(int $codeId): void
    {
        $existing = UssdFavorite::where('device_id', $this->deviceId)
            ->where('ussd_code_id', $codeId)
            ->first();

        if ($existing !== null) {
            $existing->delete();
        } else {
            UssdFavorite::create([
                'device_id' => $this->deviceId,
                'ussd_code_id' => $codeId,
            ]);
        }
    }

    /**
     * @return Collection<int, UssdCode>
     */
    public function getCodesProperty(): Collection
    {
        $query = UssdCode::where('operator', $this->activeOperator)
            ->where('is_active', true);

        if ($this->activeCategory !== '') {
            $query->where('category', $this->activeCategory);
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * @return Collection<int, UssdFavorite>
     */
    public function getFavoritesProperty(): Collection
    {
        return UssdFavorite::where('device_id', $this->deviceId)
            ->whereHas('ussdCode', fn ($q) => $q->where('operator', $this->activeOperator)->where('is_active', true))
            ->orderByDesc('use_count')
            ->with('ussdCode')
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function getFavoriteIdsProperty(): array
    {
        return UssdFavorite::where('device_id', $this->deviceId)
            ->pluck('ussd_code_id')
            ->all();
    }

    /**
     * @return Collection<int, UssdCode>
     */
    public function getRecentProperty(): Collection
    {
        return UssdCode::whereHas('favorites', fn ($q) => $q->where('device_id', $this->deviceId)->whereNotNull('last_used_at'))
            ->where('operator', $this->activeOperator)
            ->where('is_active', true)
            ->get()
            ->sortByDesc(fn ($code) => $code->favorites->first()?->last_used_at)
            ->take(3);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.ussd-guide', [
            'operators'  => Operator::cases(),
            'ussdTabs'   => UssdTab::cases(),
            'categories' => UssdCategory::cases(),
            'activeCode' => $this->activeCodeId ? UssdCode::find($this->activeCodeId) : null,
        ])->layout('components.layouts.app', ['title' => __('ussd.title')]);
    }

    private function buildCallUri(string $telUri): string
    {
        $mode = app(SettingsService::class)->get('call_mode', 'dial');
        return $mode === 'direct' ? str_replace('tel:', 'tel-direct:', $telUri) : $telUri;
    }

    private function trackUsage(int $codeId, array $params = []): void
    {
        $favorite = UssdFavorite::firstOrCreate(
            ['device_id' => $this->deviceId, 'ussd_code_id' => $codeId],
        );

        $favorite->incrementUse();

        // Sauvegarder les params (sauf code secret) pour pre-remplir la prochaine fois
        $safeParams = array_filter($params, fn ($key) => !str_contains($key, 'secret') && !str_contains($key, 'password'), ARRAY_FILTER_USE_KEY);

        if (!empty($safeParams)) {
            $favorite->update(['saved_params' => $safeParams]);
        }
    }
}
