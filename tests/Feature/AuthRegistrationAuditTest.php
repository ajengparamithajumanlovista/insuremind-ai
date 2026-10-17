<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifies that account registration is recorded in the Audit Log using the
 * same pattern as other authentication activity (category `authentication`).
 */
class AuthRegistrationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_registration_writes_an_auth_register_audit_log(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Siti Rahma',
            'email' => 'siti@insuremind.ai',
            'password' => 'rahasia12345',
            'password_confirmation' => 'rahasia12345',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::query()->where('email', 'siti@insuremind.ai')->firstOrFail();

        $log = AuditLog::query()->where('action', 'auth.register')->firstOrFail();

        $this->assertSame(AuditCategory::AUTHENTICATION, $log->category);
        $this->assertSame('success', $log->result);
        $this->assertSame('Pengguna baru mendaftar', $log->description);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('user', $log->actor_role);
        $this->assertSame('Siti Rahma', $log->actor_name);
        $this->assertSame(User::class, $log->subject_type);
        $this->assertSame($user->id, $log->subject_id);
        $this->assertNotNull($log->event_id);
        $this->assertNotNull($log->created_at);
        $this->assertSame(1, AuditLog::query()->count());

        // Passwords must never reach the Audit Log.
        $this->assertStringNotContainsString('rahasia12345', json_encode($log->toArray()));
    }

    public function test_failed_registration_writes_a_failed_auth_register_audit_log(): void
    {
        User::factory()->create(['email' => 'siti@insuremind.ai']);

        $response = $this->post(route('register'), [
            'name' => 'Siti Rahma',
            'email' => 'siti@insuremind.ai',
            'password' => 'rahasia12345',
            'password_confirmation' => 'rahasia12345',
        ]);

        $response->assertSessionHasErrors('email');

        $log = AuditLog::query()->where('action', 'auth.register')->firstOrFail();

        $this->assertSame(AuditCategory::AUTHENTICATION, $log->category);
        $this->assertSame('failed', $log->result);
        $this->assertSame('Pengguna baru mendaftar', $log->description);
        $this->assertNull($log->user_id);
        $this->assertStringContainsString('email', (string) $log->reason);
        $this->assertStringNotContainsString('rahasia12345', json_encode($log->toArray()));
        $this->assertDatabaseCount('users', 1);
    }
}
