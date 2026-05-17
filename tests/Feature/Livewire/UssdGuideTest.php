<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\UssdGuide;
use App\Modules\Ussd\Enums\UssdActionType;
use App\Modules\Ussd\Enums\UssdCategory;
use App\Modules\Ussd\Models\UssdCode;
use App\Modules\Ussd\Models\UssdFavorite;
use App\Services\Settings\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class UssdGuideTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function makeDirectCode(string $code = '*880#', string $operator = 'mtn'): UssdCode
    {
        return UssdCode::create([
            'operator'    => $operator,
            'category'    => UssdCategory::COMPTE,
            'action_type' => UssdActionType::DIRECT,
            'slug'        => 'test-direct-' . uniqid(),
            'label'       => 'Solde MTN',
            'code'        => $code,
            'params'      => null,
            'sort_order'  => 1,
            'is_active'   => true,
        ]);
    }

    private function makeGuidedCode(string $operator = 'mtn'): UssdCode
    {
        return UssdCode::create([
            'operator'    => $operator,
            'category'    => UssdCategory::MOBILE_MONEY,
            'action_type' => UssdActionType::GUIDED,
            'slug'        => 'test-guided-' . uniqid(),
            'label'       => 'Transfert MTN',
            'code'        => '*880*{phone}*{amount}#',
            'params'      => [
                ['key' => 'phone',  'label' => 'Numéro',  'type' => 'tel',    'placeholder' => '07XXXXXXXX'],
                ['key' => 'amount', 'label' => 'Montant', 'type' => 'number', 'placeholder' => '500'],
            ],
            'sort_order' => 2,
            'is_active'  => true,
        ]);
    }

    private function makeMenuCode(string $operator = 'mtn'): UssdCode
    {
        return UssdCode::create([
            'operator'    => $operator,
            'category'    => UssdCategory::AUTRE,
            'action_type' => UssdActionType::MENU,
            'slug'        => 'test-menu-' . uniqid(),
            'label'       => 'Menu MTN',
            'code'        => '*880#',
            'params'      => null,
            'sort_order'  => 3,
            'is_active'   => true,
        ]);
    }

    // ------------------------------------------------------------------ selectCode

    public function test_select_direct_code_dispatches_launch_ussd_event(): void
    {
        $code = $this->makeDirectCode('*880#');

        Livewire::test(UssdGuide::class)
            ->call('selectCode', $code->id)
            ->assertDispatched('launch-ussd', fn (string $name, array $params) =>
                str_contains($params['uri'], 'tel:') &&
                str_contains($params['uri'], '%23')
            );
    }

    public function test_select_direct_code_uri_contains_encoded_hash(): void
    {
        $code = $this->makeDirectCode('*880#');

        Livewire::test(UssdGuide::class)
            ->call('selectCode', $code->id)
            ->assertDispatched('launch-ussd', fn (string $name, array $params) =>
                $params['uri'] === 'tel:*880%23'
            );
    }

    public function test_select_direct_code_with_call_mode_direct_uses_tel_direct_scheme(): void
    {
        app(SettingsService::class)->set('call_mode', 'direct');
        $code = $this->makeDirectCode('*880#');

        Livewire::test(UssdGuide::class)
            ->call('selectCode', $code->id)
            ->assertDispatched('launch-ussd', fn (string $name, array $params) =>
                str_starts_with($params['uri'], 'tel-direct:')
            );
    }

    public function test_select_guided_code_opens_sheet_and_does_not_dispatch_event(): void
    {
        $code = $this->makeGuidedCode();

        Livewire::test(UssdGuide::class)
            ->call('selectCode', $code->id)
            ->assertSet('activeCodeId', $code->id)
            ->assertNotDispatched('launch-ussd');
    }

    public function test_select_menu_code_dispatches_launch_ussd_event(): void
    {
        $code = $this->makeMenuCode();

        Livewire::test(UssdGuide::class)
            ->call('selectCode', $code->id)
            ->assertDispatched('launch-ussd');
    }

    public function test_select_nonexistent_code_does_nothing(): void
    {
        Livewire::test(UssdGuide::class)
            ->call('selectCode', 99999)
            ->assertNotDispatched('launch-ussd')
            ->assertSet('activeCodeId', null);
    }

    // ------------------------------------------------------------------ launchGuided

    public function test_launch_guided_with_valid_params_dispatches_event(): void
    {
        $code = $this->makeGuidedCode();

        Livewire::test(UssdGuide::class)
            ->set('activeCodeId', $code->id)
            ->set('paramValues', ['phone' => '0701234567', 'amount' => '500'])
            ->call('launchGuided')
            ->assertDispatched('launch-ussd', fn (string $name, array $params) =>
                str_contains($params['uri'], '0701234567') &&
                str_contains($params['uri'], '500')
            );
    }

    public function test_launch_guided_with_missing_params_does_not_dispatch(): void
    {
        $code = $this->makeGuidedCode();

        Livewire::test(UssdGuide::class)
            ->set('activeCodeId', $code->id)
            ->set('paramValues', ['phone' => '0701234567']) // amount manquant
            ->call('launchGuided')
            ->assertNotDispatched('launch-ussd');
    }

    public function test_launch_guided_resets_state_after_dispatch(): void
    {
        $code = $this->makeGuidedCode();

        Livewire::test(UssdGuide::class)
            ->set('activeCodeId', $code->id)
            ->set('paramValues', ['phone' => '0701234567', 'amount' => '500'])
            ->call('launchGuided')
            ->assertSet('activeCodeId', null)
            ->assertSet('paramValues', []);
    }

    // ------------------------------------------------------------------ toggleFavorite

    public function test_toggle_favorite_creates_favorite_on_first_call(): void
    {
        $code = $this->makeDirectCode();

        Livewire::test(UssdGuide::class)
            ->call('toggleFavorite', $code->id);

        $this->assertDatabaseHas('ussd_favorites', [
            'ussd_code_id' => $code->id,
        ]);
    }

    public function test_toggle_favorite_removes_favorite_on_second_call(): void
    {
        $code = $this->makeDirectCode();

        $component = Livewire::test(UssdGuide::class);
        $component->call('toggleFavorite', $code->id); // ajoute
        $component->call('toggleFavorite', $code->id); // supprime

        $this->assertDatabaseMissing('ussd_favorites', [
            'ussd_code_id' => $code->id,
        ]);
    }

    // ------------------------------------------------------------------ trackUsage (via selectCode)

    public function test_select_direct_code_increments_use_count(): void
    {
        $code = $this->makeDirectCode();

        Livewire::test(UssdGuide::class)
            ->call('selectCode', $code->id)
            ->call('selectCode', $code->id);

        $fav = UssdFavorite::where('ussd_code_id', $code->id)->first();
        $this->assertNotNull($fav);
        $this->assertSame(2, $fav->use_count);
    }

    // ------------------------------------------------------------------ cancelGuided

    public function test_cancel_guided_resets_state(): void
    {
        $code = $this->makeGuidedCode();

        Livewire::test(UssdGuide::class)
            ->set('activeCodeId', $code->id)
            ->set('paramValues', ['phone' => '07'])
            ->call('cancelGuided')
            ->assertSet('activeCodeId', null)
            ->assertSet('paramValues', []);
    }
}
