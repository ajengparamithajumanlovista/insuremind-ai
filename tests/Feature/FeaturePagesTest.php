<?php

namespace Tests\Feature;

use App\Models\InsuranceProduct;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\InsuranceProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ensures every InsureMind AI feature page renders without errors for users
 * with a complete profile (the deepest branch).
 */
class FeaturePagesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(InsuranceProductSeeder::class);

        $this->user = User::create([
            'name' => 'Tester',
            'email' => 'tester@insuremind.ai',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        UserProfile::create([
            'user_id' => $this->user->id,
            'age' => 34,
            'occupation' => 'Staf keuangan',
            'city' => 'Jakarta',
            'monthly_income' => 15_000_000,
            'monthly_expense' => 7_500_000,
            'monthly_debt' => 1_500_000,
            'savings' => 45_000_000,
            'dependents' => 2,
            'has_health_insurance' => false,
            'has_life_insurance' => false,
            'has_work_insurance' => true,
            'existing_coverage' => 100_000_000,
            'education_years' => 6,
            'education_cost' => 180_000_000,
            'notes' => 'Tidak ada riwayat klaim.',
        ]);
    }

    public function test_dashboard_renders(): void
    {
        $this->actingAs($this->user)->get(route('dashboard'))->assertOk()->assertSee('Ringkasan Proteksi Anda');
    }

    public function test_profile_page_renders(): void
    {
        $this->actingAs($this->user)->get(route('profile.edit'))->assertOk()->assertSee('Profil Keuangan');
    }

    public function test_profile_can_be_updated(): void
    {
        $this->actingAs($this->user)
            ->put(route('profile.update'), [
                'age' => 35,
                'monthly_income' => '16.000.000',
                'monthly_expense' => '7.000.000',
                'monthly_debt' => '1.000.000',
                'savings' => '50.000.000',
                'dependents' => 2,
                'has_health_insurance' => 1,
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertEqualsWithDelta(16_000_000, (float) $this->user->fresh()->profileOrDefault()->monthly_income, 0.01);
    }

    public function test_risk_page_renders_and_can_be_saved(): void
    {
        $this->actingAs($this->user)->get(route('risk.index'))->assertOk()->assertSee('Skor Risiko Proteksi');

        $this->actingAs($this->user)->post(route('risk.store'))->assertRedirect(route('risk.index'));

        $this->assertDatabaseCount('risk_assessments', 1);
    }

    public function test_simulator_renders_for_every_scenario(): void
    {
        foreach (['life', 'critical_illness', 'income_loss'] as $scenario) {
            $this->actingAs($this->user)
                ->get(route('simulator.index', [
                    'scenario' => $scenario,
                    'coverage' => '500.000.000',
                    'premium' => '450.000',
                ]))
                ->assertOk()
                ->assertSee('Simulator Risiko');
        }
    }

    public function test_simulation_can_be_saved(): void
    {
        $this->actingAs($this->user)
            ->post(route('simulator.store'), [
                'scenario' => 'life',
                'coverage' => '500.000.000',
                'premium' => '450.000',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('risk_simulations', 1);
    }

    public function test_comparison_page_renders(): void
    {
        $this->actingAs($this->user)
            ->get(route('comparison.index'))
            ->assertOk()
            ->assertSee('Perbandingan Produk');

        $this->actingAs($this->user)
            ->get(route('comparison.index', ['category' => 'health']))
            ->assertOk()
            ->assertSee('Rawatinap Plus');
    }

    public function test_category_pages_render(): void
    {
        foreach (InsuranceProduct::CATEGORIES as $category) {
            $this->actingAs($this->user)
                ->get(route('categories.show', $category))
                ->assertOk();
        }
    }

    public function test_unknown_category_returns_404(): void
    {
        $this->actingAs($this->user)->get('/categories/unknown')->assertNotFound();
    }

    public function test_policies_page_renders_and_crud_works(): void
    {
        $this->actingAs($this->user)->get(route('policies.index'))->assertOk();

        $payload = [
            'provider_name' => 'SehatKu',
            'policy_name' => 'Rawatinap Plus',
            'policy_number' => 'SK-2026-001',
            'category' => 'health',
            'premium_amount' => '450.000',
            'coverage_amount' => '250.000.000',
            'payment_period' => 'monthly',
            'reminder_days' => 14,
        ];

        $this->actingAs($this->user)->post(route('policies.store'), $payload)->assertRedirect(route('policies.index'));

        $policy = $this->user->policies()->firstOrFail();
        $this->assertEqualsWithDelta(450_000, (float) $policy->premium_amount, 0.01);

        $this->actingAs($this->user)
            ->put(route('policies.update', $policy->id), array_merge($payload, ['premium_amount' => '500.000']))
            ->assertRedirect(route('policies.index'));

        $this->assertEqualsWithDelta(500_000, (float) $policy->fresh()->premium_amount, 0.01);

        $this->actingAs($this->user)->delete(route('policies.destroy', $policy->id))->assertRedirect(route('policies.index'));
        $this->assertDatabaseCount('policies', 0);
    }

    public function test_policy_validation_rejects_invalid_category(): void
    {
        $this->actingAs($this->user)
            ->post(route('policies.store'), [
                'provider_name' => 'X',
                'category' => 'not-a-category',
                'premium_amount' => '100.000',
                'payment_period' => 'monthly',
            ])
            ->assertSessionHasErrors('category');
    }

    public function test_chat_page_renders(): void
    {
        $this->actingAs($this->user)->get(route('chat.index'))->assertOk()->assertSee('Asisten AI');
    }

    public function test_chat_store_returns_json_reply(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('chat.store'), ['message' => 'Bagaimana skor risiko saya?'])
            ->assertOk()
            ->assertJsonStructure(['user' => ['body_id'], 'assistant' => ['body_id', 'body_en', 'engine']]);

        $this->assertNotEmpty($response->json('assistant.body_id'));
        $this->assertDatabaseCount('chat_messages', 2);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('chat.index'))->assertRedirect(route('login'));
    }
}
