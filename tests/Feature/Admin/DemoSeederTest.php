<?php

namespace Tests\Feature\Admin;

use App\Models\AiReviewCase;
use App\Models\AuditLog;
use App\Models\ChatFeedback;
use App\Models\ChatMessage;
use App\Models\InsuranceProduct;
use App\Models\Policy;
use App\Models\PolicyDocument;
use App\Models\ProductHistory;
use App\Models\RiskAssessment;
use App\Models\RiskSimulation;
use App\Models\User;
use App\Models\UserProfile;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies that DatabaseSeeder populates demo data for ALL application
 * features, so every page can be tried immediately without empty data.
 *
 * Also ensures table names remain in sync with models — particularly
 * `chat_feedbacks`, which cannot be inferred automatically because "feedback"
 * is an uncountable noun.
 */
class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_every_feature_table_is_populated(): void
    {
        $this->assertGreaterThanOrEqual(3, User::query()->count());
        $this->assertGreaterThan(0, UserProfile::query()->count());
        $this->assertGreaterThan(0, InsuranceProduct::query()->count());
        $this->assertGreaterThan(0, Policy::query()->count());
        $this->assertGreaterThan(0, RiskAssessment::query()->count());
        $this->assertGreaterThan(0, RiskSimulation::query()->count());
        $this->assertGreaterThan(0, ChatMessage::query()->count());
        $this->assertGreaterThan(0, ChatFeedback::query()->count());
        $this->assertGreaterThan(0, PolicyDocument::query()->count());
        $this->assertGreaterThan(0, AiReviewCase::query()->count());
        $this->assertGreaterThan(0, ProductHistory::query()->count());
        $this->assertGreaterThan(0, AuditLog::query()->count());
    }

    public function test_seeders_create_all_role_and_status_variants(): void
    {
        $this->assertTrue(User::query()->where('role', 'admin')->exists());
        $this->assertTrue(User::query()->where('role', 'reviewer')->exists());
        $this->assertTrue(User::query()->where('role', 'user')->exists());

        $this->assertTrue(User::query()->where('status', 'active')->exists());
        $this->assertTrue(User::query()->where('status', 'inactive')->exists());
        $this->assertTrue(User::query()->where('status', 'suspended')->exists());
    }

    public function test_demo_profile_is_ready_for_risk_analysis(): void
    {
        $user = User::query()->where('email', 'user@insuremind.ai')->firstOrFail();

        $this->assertNotNull($user->profile);
        $this->assertTrue($user->profile->isReady());
        $this->assertGreaterThan(0, $user->policies()->count());
        $this->assertGreaterThan(0, $user->chatMessages()->count());
        $this->assertGreaterThan(0, $user->policyDocuments()->count());
    }

    public function test_policies_cover_all_reminder_states(): void
    {
        $statuses = Policy::query()->get()
            ->map(fn (Policy $policy) => $policy->reminderStatus())
            ->unique();

        $this->assertTrue($statuses->contains('overdue'));
        $this->assertTrue($statuses->contains('due_soon'));
        $this->assertTrue($statuses->contains('upcoming'));
    }

    public function test_chat_seed_contains_message_without_reference(): void
    {
        // At least one assistant answer without a reference keeps the AI Quality
        // Review queue non-empty and makes reference detection testing meaningful.
        $withoutReference = ChatMessage::query()
            ->where('role', 'assistant')
            ->get()
            ->filter(fn (ChatMessage $message) => ! $message->hasVerifiableReference());

        $this->assertGreaterThan(0, $withoutReference->count());
    }

    public function test_review_cases_link_to_chat_and_documents(): void
    {
        $this->assertTrue(
            AiReviewCase::query()->whereNotNull('chat_message_id')->exists(),
            'Harus ada kasus review yang berasal dari jawaban chat.'
        );

        $this->assertTrue(
            AiReviewCase::query()->whereNotNull('policy_document_id')->exists(),
            'Harus ada kasus review yang berasal dari dokumen.'
        );
    }

    public function test_documents_cover_success_review_and_failure_states(): void
    {
        $this->assertTrue(PolicyDocument::query()->where('status', 'analysed')->exists());
        $this->assertTrue(PolicyDocument::query()->where('status', 'review')->exists());
        $this->assertTrue(PolicyDocument::query()->where('status', 'failed')->exists());
    }

    public function test_audit_log_covers_multiple_categories(): void
    {
        $categories = AuditLog::query()->pluck('category')->unique();

        $this->assertGreaterThanOrEqual(5, $categories->count());
    }

    public function test_seeder_is_idempotent(): void
    {
        $countsBefore = $this->tableCounts();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($countsBefore, $this->tableCounts(), 'Seeder harus aman dijalankan ulang.');
    }

    /**
     * @return array<string, int>
     */
    private function tableCounts(): array
    {
        return [
            'users' => User::query()->count(),
            'user_profiles' => UserProfile::query()->count(),
            'insurance_products' => InsuranceProduct::query()->count(),
            'policies' => Policy::query()->count(),
            'chat_messages' => ChatMessage::query()->count(),
            'chat_feedbacks' => ChatFeedback::query()->count(),
            'policy_documents' => PolicyDocument::query()->count(),
            'ai_review_cases' => AiReviewCase::query()->count(),
            'product_histories' => ProductHistory::query()->count(),
            'audit_logs' => AuditLog::query()->count(),
        ];
    }
}
