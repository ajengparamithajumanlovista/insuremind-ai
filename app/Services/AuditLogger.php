<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

/**
 * Penulis tunggal untuk Audit Log.
 *
 * Seluruh halaman admin memakai layanan ini alih-alih menulis langsung ke
 * tabel `audit_logs` agar:
 *
 * 1. Metadata sensitif (kata sandi, token, API key) tersaring otomatis.
 * 2. Identitas pelaku, alamat IP, dan ID permintaan tercatat konsisten.
 * 3. Kegagalan pencatatan log tidak pernah menggagalkan tindakan pengguna
 *    (bersifat best-effort, kesalahan dilaporkan ke berkas log aplikasi).
 */
class AuditLogger
{
    /**
     * Nama header berisi ID permintaan yang dipakai lintas sistem.
     */
    public const REQUEST_ID_HEADER = 'X-Request-Id';

    /**
     * Catat satu aktivitas ke dalam Audit Log.
     *
     * @param  array{
     *     category: string,
     *     action: string,
     *     description: string,
     *     subject?: Model|array{type: string, id: int|string|null, label?: string|null}|null,
     *     result?: string,
     *     before?: array<string, mixed>|null,
     *     after?: array<string, mixed>|null,
     *     reason?: string|null,
     *     actor?: Authenticatable|null,
     *     actorRole?: string|null,
     * }  $payload
     */
    public function log(array $payload): ?AuditLog
    {
        try {
            $request = $this->request();
            $actor = $payload['actor'] ?? Auth::user();
            $subject = $this->normalizeSubject($payload['subject'] ?? null);

            return AuditLog::create([
                'user_id' => $actor instanceof User ? $actor->getKey() : null,
                'actor_role' => $payload['actorRole'] ?? ($actor instanceof User ? $actor->role : 'system'),
                'actor_name' => $actor instanceof Authenticatable ? $actor->getAuthIdentifierName() !== null
                    ? $actor->getAttribute('name')
                    : null : null,
                'category' => $payload['category'],
                'action' => $payload['action'],
                'description' => Str::limit($payload['description'], 500, ''),
                'subject_type' => $subject['type'],
                'subject_id' => $subject['id'],
                'subject_label' => $subject['label'],
                'result' => $payload['result'] ?? 'success',
                'before' => $this->redact($payload['before'] ?? null),
                'after' => $this->redact($payload['after'] ?? null),
                'reason' => $payload['reason'] ?? null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? Str::limit((string) $request->userAgent(), 255, '') : null,
                'request_id' => $this->requestId($request),
            ]);
        } catch (Throwable $e) {
            // Audit log tidak boleh menggagalkan aksi utama pengguna.
            report($e);

            return null;
        }
    }

    /**
     * Catat perubahan nilai: hanya field yang benar-benar berubah.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array{before: array<string, mixed>, after: array<string, mixed>}
     */
    public function diff(array $before, array $after): array
    {
        $changedBefore = [];
        $changedAfter = [];

        foreach ($after as $key => $value) {
            $old = $before[$key] ?? null;

            if ($old !== $value) {
                $changedBefore[$key] = $old;
                $changedAfter[$key] = $value;
            }
        }

        return ['before' => $changedBefore, 'after' => $changedAfter];
    }

    /**
     * Buang kunci sensitif secara rekursif sebelum data disimpan.
     *
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>|null
     */
    public function redact(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        foreach (array_keys($data) as $key) {
            if (in_array(Str::lower((string) $key), AuditLog::REDACTED_KEYS, true)) {
                $data[$key] = '[disunting]';

                continue;
            }

            if (is_array($data[$key])) {
                $data[$key] = $this->redact($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @return array{type: string|null, id: int|string|null, label: string|null}
     */
    private function normalizeSubject(Model|array|null $subject): array
    {
        if ($subject instanceof Model) {
            return [
                'type' => $subject::class,
                'id' => $subject->getKey(),
                'label' => Arr::get($subject->getAttributes(), 'name')
                    ?? Arr::get($subject->getAttributes(), 'policy_name')
                    ?? Arr::get($subject->getAttributes(), 'original_name'),
            ];
        }

        return [
            'type' => $subject['type'] ?? null,
            'id' => $subject['id'] ?? null,
            'label' => $subject['label'] ?? null,
        ];
    }

    private function request(): ?Request
    {
        return app()->runningInConsole() ? null : request();
    }

    private function requestId(?Request $request): ?string
    {
        $header = $request?->header(self::REQUEST_ID_HEADER);

        return is_string($header) && $header !== '' ? Str::limit($header, 64, '') : null;
    }
}
