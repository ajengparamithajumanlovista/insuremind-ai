<?php

namespace Tests\Feature\Admin;

use App\Models\AiReviewCase;
use App\Models\AuditLog;
use App\Models\InsuranceProduct;
use App\Models\PolicyDocument;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\AuditCategory;
use Database\Seeders\InsuranceProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memverifikasi fondasi Admin Dashboard: skema aditif, peran/status akun,
 * gate otorisasi, dan integritas Audit Log.
 */
class AdminFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_table_has_admin_management_columns(): void
    {
        $user = User::factory()->admin()->create([
            'phone' => '081200000000',
            'admin_note' => 'Catatan internal.',
        ]);

        $this->assertSame('admin', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertTrue($user->isActive());
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isReviewer());
        $this->assertSame('Admin', $user->roleLabel()['id']);
        $this->assertSame('Aktif', $user->statusLabel()['id']);
        $this->assertSame('green', $user->statusTone());
        $this->assertSame('081200000000', $user->phone);
    }

    public function test_existing_rows_default_to_active_status(): void
    {
        $user = User::factory()->create();

        $this->assertSame('active', $user->fresh()->status);
        $this->assertTrue($user->fresh()->isActive());
    }

    public function test_reviewer_role_has_limited_privileges(): void
    {
        $reviewer = User::factory()->reviewer()->create();

        $this->assertTrue($reviewer->isReviewer());
        $this->assertFalse($reviewer->isAdmin());
        $this->assertSame('Reviewer', $reviewer->roleLabel()['id']);
    }

    public function test_inactive_user_is_not_active(): void
    {
        $user = User::factory()->inactive()->create();

        $this->assertFalse($user->isActive());
        $this->assertSame('Nonaktif', $user->statusLabel()['id']);
        $this->assertSame('grey', $user->statusTone());
    }

    public function test_gates_enforce_least_privilege(): void
    {
        $admin = User::factory()->admin()->create();
        $reviewer = User::factory()->reviewer()->create();
        $user = User::factory()->create();

        $this->assertTrue($admin->can('access-admin-dashboard'));
        $this->assertFalse($reviewer->can('access-admin-dashboard'));
        $this->assertFalse($user->can('access-admin-dashboard'));

        $this->assertTrue($admin->can('access-ai-review'));
        $this->assertTrue($reviewer->can('access-ai-review'));
        $this->assertFalse($user->can('access-ai-review'));

        $this->assertFalse($reviewer->can('manage-products'));
        $this->assertFalse($reviewer->can('manage-users'));
        $this->assertFalse($reviewer->can('view-audit-log'));
        $this->assertTrue($admin->can('manage-products'));
        $this->assertTrue($admin->can('manage-users'));
        $this->assertTrue($admin->can('view-audit-log'));
    }

    public function test_review_case_gate_scopes_reviewer_to_own_records(): void
    {
        $reviewer = User::factory()->reviewer()->create();
        $owner = User::factory()->create();

        $this->assertTrue($reviewer->can('review-case', [null]));
        $this->assertTrue($reviewer->can('review-case', [$reviewer]));
        $this->assertFalse($reviewer->can('review-case', [$owner]));
    }

    public function test_insurance_product_has_catalog_admin_fields(): void
    {
        $this->seed(InsuranceProductSeeder::class);

        $product = InsuranceProduct::query()->firstOrFail();
        $total = InsuranceProduct::query()->count();

        $this->assertTrue($product->is_active);
        $this->assertSame('Data Contoh', $product->sourceBadge()['label']['id']);
        $this->assertSame($total, InsuranceProduct::query()->active()->count());

        $product->update([
            'is_active' => false,
            'description' => ['id' => 'Deskripsi contoh.', 'en' => 'Sample description.'],
            'entry_age_min' => 17,
            'entry_age_max' => 60,
            'source_url' => 'https://example.test/produk',
        ]);

        $product->refresh();

        $this->assertFalse($product->is_active);
        $this->assertSame('Deskripsi contoh.', $product->description('id'));
        $this->assertSame('Sample description.', $product->description('en'));
        $this->assertSame(17, $product->entry_age_min);
        $this->assertSame(60, $product->entry_age_max);
        $this->assertNotNull($product->source_url);
        $this->assertSame($total - 1, InsuranceProduct::query()->active()->count());
    }

    public function test_policy_document_status_helpers(): void
    {
        $user = User::factory()->create();

        $document = PolicyDocument::create([
            'user_id' => $user->id,
            'original_name' => 'polisi-kesehatan.pdf',
            'path' => 'policy-documents/polisi-kesehatan.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 512_000,
            'category' => 'health',
            'status' => 'failed',
            'failure_reason' => 'Berkas terenkripsi sehingga teks tidak dapat diekstraksi.',
        ]);

        $this->assertSame('rose', $document->statusTone());
        $this->assertTrue($document->isFinished());
        $this->assertSame($user->id, $document->user->id);

        $document->update(['status' => 'analysed']);
        $this->assertSame('green', $document->fresh()->statusTone());

        $document->update(['status' => 'processing']);
        $this->assertFalse($document->fresh()->isFinished());
        $this->assertSame('blue', $document->fresh()->statusTone());
    }

    public function test_ai_review_case_scopes_and_tone(): void
    {
        $user = User::factory()->create();

        $open = AiReviewCase::create([
            'user_id' => $user->id,
            'issue_type' => 'missing_reference',
            'issues' => ['missing_reference', 'negative_feedback'],
            'question' => 'Apakah klaim saya ditanggung?',
            'answer' => 'Klaim ditanggung.',
            'status' => 'pending',
            'has_reference' => false,
            'negative_feedback_count' => 1,
        ]);

        $done = AiReviewCase::create([
            'user_id' => $user->id,
            'issue_type' => 'negative_feedback',
            'status' => 'valid',
            'has_reference' => true,
        ]);

        $this->assertFalse($open->has_reference);
        $this->assertSame(['missing_reference', 'negative_feedback'], $open->issues);
        $this->assertSame('amber', $open->statusTone());
        $this->assertFalse($open->isFinished());

        $this->assertTrue($done->isFinished());
        $this->assertSame('green', $done->statusTone());

        $this->assertSame(1, AiReviewCase::query()->open()->count());
        $this->assertSame(1, AiReviewCase::query()->finished()->count());
    }

    public function test_audit_logger_records_actor_and_redacts_secrets(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Uji']);

        $log = app(AuditLogger::class)->log([
            'actor' => $admin,
            'category' => AuditCategory::USER_MANAGEMENT,
            'action' => 'user.role_changed',
            'description' => 'Mengubah peran pengguna.',
            'subject' => ['type' => User::class, 'id' => 42, 'label' => 'Budi'],
            'before' => ['role' => 'user', 'password' => 'rahasia123', 'nested' => ['token' => 'abc']],
            'after' => ['role' => 'reviewer', 'password' => 'rahasia456'],
            'reason' => 'Rotasi tim reviewer.',
        ]);

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertNotNull($log->event_id);
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame('admin', $log->actor_role);
        $this->assertSame('Admin Uji', $log->actor_name);
        $this->assertSame('user_management', $log->category);
        $this->assertSame('success', $log->result);
        $this->assertNull($log->updated_at);
        $this->assertNotNull($log->created_at);

        // Nilai sensitif disunting secara rekursif.
        $this->assertSame('[disunting]', $log->before['password']);
        $this->assertSame('[disunting]', $log->before['nested']['token']);
        $this->assertSame('[disunting]', $log->after['password']);
        $this->assertSame('user', $log->before['role']);
        $this->assertSame('reviewer', $log->after['role']);

        // Metadata tetap dapat ditelusuri.
        $this->assertSame('Manajemen Pengguna', $log->categoryLabel()['id']);
        $this->assertSame('green', $log->resultTone());
        $this->assertSame(1, AuditLog::query()->count());
    }

    public function test_audit_logger_diff_only_reports_changed_fields(): void
    {
        $diff = app(AuditLogger::class)->diff(
            ['monthly_premium' => 385_000, 'is_active' => true, 'provider' => 'SehatKu'],
            ['monthly_premium' => 400_000, 'is_active' => true, 'provider' => 'SehatKu'],
        );

        $this->assertSame(['monthly_premium' => 385_000], $diff['before']);
        $this->assertSame(['monthly_premium' => 400_000], $diff['after']);
    }

    public function test_audit_log_is_append_only_without_updated_at(): void
    {
        $log = AuditLog::create([
            'category' => 'security',
            'action' => 'login.failed',
            'description' => 'Percobaan masuk gagal.',
            'result' => 'failed',
        ]);

        $this->assertNull($log->updated_at);
        $this->assertSame('rose', $log->resultTone());

        // Eloquent tidak boleh menyentuh kolom updated_at yang tidak ada.
        $log->touch();

        $this->assertDatabaseHas('audit_logs', ['id' => $log->id, 'result' => 'failed']);
    }

    public function test_change_detection_on_user_role_is_captured(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $before = $target->only(['role', 'status']);
        $target->update(['role' => 'reviewer']);
        $after = $target->only(['role', 'status']);

        $diff = app(AuditLogger::class)->diff($before, $after);

        app(AuditLogger::class)->log([
            'actor' => $admin,
            'category' => AuditCategory::USER_MANAGEMENT,
            'action' => 'user.role_changed',
            'description' => 'Mengubah peran pengguna dari user menjadi reviewer.',
            'subject' => $target,
            'before' => $diff['before'],
            'after' => $diff['after'],
        ]);

        $log = AuditLog::query()->latest('id')->firstOrFail();

        $this->assertSame('user', $log->before['role']);
        $this->assertSame('reviewer', $log->after['role']);
        $this->assertArrayNotHasKey('status', $log->before);
        $this->assertSame(User::class, $log->subject_type);
        $this->assertSame($target->id, $log->subject_id);
    }
}
