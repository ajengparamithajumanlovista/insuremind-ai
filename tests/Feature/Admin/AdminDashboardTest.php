<?php

namespace Tests\Feature\Admin;

use App\Models\AiReviewCase;
use App\Models\AuditLog;
use App\Models\InsuranceProduct;
use App\Models\PolicyDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies the Admin Dashboard flow: role-based permissions, user/product
 * management, AI case review, and Audit Log readability.
 */
class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_open_admin_area(): void
    {
        $user = User::factory()->create();

        // The admin summary redirects regular users to the review queue, while
        // admin-only pages reject them with a 403.
        $this->actingAs($user)->get(route('admin.dashboard'))->assertRedirect(route('admin.review.index'));
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.review.index'))->assertForbidden();
    }

    public function test_inactive_user_is_signed_out_by_active_middleware(): void
    {
        $user = User::factory()->admin()->create(['status' => 'inactive']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_can_open_every_admin_page(): void
    {
        $admin = $this->admin();
        $product = InsuranceProduct::create([
            'slug' => 'uji-produk',
            'provider' => 'Uji',
            'name' => 'Produk Uji',
            'category' => 'health',
            'monthly_premium' => 100_000,
            'coverage_limit' => 10_000_000,
            'benefits' => [],
            'exclusions' => [],
            'claim_documents' => [],
            'is_active' => true,
        ]);
        $case = AiReviewCase::create([
            'user_id' => $admin->id,
            'issue_type' => 'missing_reference',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.show', $admin))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.show', $product))->assertOk();
        $this->actingAs($admin)->get(route('admin.documents.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.audit.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.review.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.review.show', $case))->assertOk();
    }

    public function test_reviewer_reaches_review_queue_but_not_admin_pages(): void
    {
        $reviewer = User::factory()->reviewer()->create();

        $this->actingAs($reviewer)->get(route('admin.review.index'))->assertOk();
        $this->actingAs($reviewer)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($reviewer)->get(route('admin.products.index'))->assertForbidden();
        $this->actingAs($reviewer)->get(route('admin.audit.index'))->assertForbidden();
    }

    public function test_reviewer_dashboard_redirects_to_queue(): void
    {
        $reviewer = User::factory()->reviewer()->create();

        $this->actingAs($reviewer)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.review.index'));
    }

    public function test_admin_can_suspend_and_reactivate_a_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['status' => 'active']);

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $target))
            ->assertRedirect();

        $this->assertSame('suspended', $target->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.users.activate', $target))
            ->assertRedirect();

        $this->assertSame('active', $target->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'category' => 'user_management',
            'action' => 'user.suspended',
            'subject_id' => $target->id,
        ]);
    }

    public function test_admin_cannot_suspend_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.suspend', $admin))->assertRedirect();

        $this->assertSame('active', $admin->fresh()->status);
    }

    public function test_admin_can_delete_a_user_and_audit_is_preserved(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['role' => 'reviewer']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseHas('audit_logs', [
            'category' => 'user_management',
            'action' => 'user.deleted',
            'subject_id' => $target->id,
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_last_admin_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $other = User::factory()->admin()->create();

        // With two admins, deletion is still allowed.
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $other))
            ->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $other->id]);

        // As the last remaining admin, this account cannot be deleted by another admin.
        $another = User::factory()->create(['role' => 'user']);
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $another))
            ->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_regular_user_cannot_delete_accounts(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.users.destroy', $target))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_role_change_is_recorded_in_audit_log(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'role' => 'reviewer',
            'status' => 'active',
        ])->assertRedirect();

        $this->assertSame('reviewer', $target->fresh()->role);

        $log = AuditLog::query()->where('action', 'user.updated')->latest('id')->firstOrFail();
        $this->assertSame('user', $log->before['role']);
        $this->assertSame('reviewer', $log->after['role']);
    }

    public function test_product_toggle_hides_from_catalog_and_writes_history(): void
    {
        $admin = $this->admin();
        $product = InsuranceProduct::create([
            'slug' => 'toggle-produk',
            'provider' => 'Uji',
            'name' => 'Produk Toggle',
            'category' => 'life',
            'monthly_premium' => 50_000,
            'coverage_limit' => 5_000_000,
            'benefits' => [],
            'exclusions' => [],
            'claim_documents' => [],
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.products.toggle', $product))->assertRedirect();

        $this->assertFalse($product->fresh()->is_active);
        $this->assertDatabaseHas('product_histories', [
            'insurance_product_id' => $product->id,
            'field' => 'is_active',
            'new_value' => '0',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'category' => 'insurance_product',
            'action' => 'product.is_active_changed',
        ]);
    }

    public function test_admin_can_create_a_product_with_generated_slug_and_audit(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'provider' => 'Mitra Sehat',
            'name' => 'Asuransi Jiwa Sejahtera',
            'category' => 'life',
            'monthly_premium' => 75_000,
            'annual_premium' => 800_000,
            'coverage_limit' => 20_000_000,
            'description_id' => 'Perlindungan jiwa menyeluruh.',
            'benefits' => "- Santunan meninggal\n- Nilai tunai",
            'exclusions' => 'Bunuh diri tahun pertama',
            'source_url' => 'https://example.com/produk',
            'is_active' => '1',
            'reason' => 'Produk baru dari mitra resmi.',
        ]);

        $product = InsuranceProduct::query()->where('name', 'Asuransi Jiwa Sejahtera')->firstOrFail();

        $response->assertRedirect(route('admin.products.show', $product));

        // Slug is generated automatically from the provider and name.
        $this->assertSame('mitra-sehat-asuransi-jiwa-sejahtera', $product->slug);
        $this->assertSame('life', $product->category);
        $this->assertTrue($product->is_active);
        $this->assertFalse($product->is_sample);
        $this->assertSame(['Santunan meninggal', 'Nilai tunai'], $product->benefits);

        $this->assertDatabaseHas('product_histories', [
            'insurance_product_id' => $product->id,
            'action' => 'created',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'category' => 'insurance_product',
            'action' => 'product.created',
            'subject_id' => $product->id,
        ]);
    }

    public function test_product_slug_stays_unique_when_creating_duplicates(): void
    {
        $admin = $this->admin();

        $payload = [
            'provider' => 'Mitra Sama',
            'name' => 'Produk Sama',
            'category' => 'health',
            'monthly_premium' => 10_000,
            'coverage_limit' => 1_000_000,
        ];

        $this->actingAs($admin)->post(route('admin.products.store'), $payload)->assertRedirect();
        $this->actingAs($admin)->post(route('admin.products.store'), $payload)->assertRedirect();

        $this->assertSame(2, InsuranceProduct::query()->count());
        $this->assertDatabaseHas('insurance_products', ['slug' => 'mitra-sama-produk-sama']);
        $this->assertDatabaseHas('insurance_products', ['slug' => 'mitra-sama-produk-sama-2']);
    }

    public function test_reviewer_cannot_create_a_product(): void
    {
        $reviewer = User::factory()->reviewer()->create();

        $this->actingAs($reviewer)->get(route('admin.products.create'))->assertForbidden();
        $this->actingAs($reviewer)->post(route('admin.products.store'), [
            'provider' => 'Mitra',
            'name' => 'Terlarang',
            'category' => 'health',
            'monthly_premium' => 1_000,
            'coverage_limit' => 1_000_000,
        ])->assertForbidden();

        $this->assertDatabaseCount('insurance_products', 0);
    }

    public function test_review_workflow_records_actions_and_audit(): void
    {
        $admin = $this->admin();
        $case = AiReviewCase::create([
            'user_id' => $admin->id,
            'issue_type' => 'negative_feedback',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.review.claim', $case))->assertRedirect();
        $this->assertSame('in_review', $case->fresh()->status);

        $this->actingAs($admin)->post(route('admin.review.decide', $case), [
            'decision' => 'needs_fix',
            'note' => 'Referensi pasal tidak sesuai.',
        ])->assertRedirect();

        $case->refresh();
        $this->assertSame('needs_fix', $case->status);
        $this->assertSame($admin->id, $case->reviewed_by);
        $this->assertNotNull($case->reviewed_at);
        $this->assertSame('Referensi pasal tidak sesuai.', $case->review_note);
        $this->assertSame(2, $case->actions()->count());
        $this->assertDatabaseHas('audit_logs', [
            'category' => 'ai_quality_review',
            'action' => 'review.needs_fix',
        ]);
    }

    public function test_review_decision_requires_a_note(): void
    {
        $admin = $this->admin();
        $case = AiReviewCase::create([
            'user_id' => $admin->id,
            'issue_type' => 'missing_reference',
            'status' => 'in_review',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.review.decide', $case), ['decision' => 'valid'])
            ->assertSessionHasErrors('note');

        $this->assertSame('in_review', $case->fresh()->status);
    }

    public function test_audit_log_export_streams_csv_with_filters(): void
    {
        $admin = $this->admin();

        AuditLog::create([
            'category' => 'security',
            'action' => 'login.failed',
            'description' => 'Percobaan masuk gagal.',
            'result' => 'failed',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.audit.export', ['result' => 'failed']));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('login.failed', $response->streamedContent());
    }

    public function test_document_download_logs_audit_entry(): void
    {
        $admin = $this->admin();
        $document = PolicyDocument::create([
            'user_id' => $admin->id,
            'original_name' => 'polisi.pdf',
            'disk' => 'local',
            'path' => 'missing/polisi.pdf',
            'status' => 'uploaded',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.documents.download', $document))
            ->assertRedirect();

        $this->assertDatabaseHas('audit_logs', ['action' => 'document.downloaded']);
    }
}
