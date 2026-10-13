<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan riwayat pemeriksaan sebuah kasus AI Quality Review.
 *
 * Setiap keputusan reviewer disimpan lengkap dengan identitas, waktu, dan
 * catatan agar alasan keputusan dapat ditelusuri. Tabel ini append-only.
 */
class AiReviewAction extends Model
{
    public const ACTIONS = [
        'claim',       // mulai meninjau
        'valid',       // tandai valid
        'needs_fix',   // perlu perbaikan
        'escalate',    // eskalasi
        'done',        // tandai selesai
        'reopen',      // dibuka kembali
        'note',        // catatan tanpa mengubah status
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ai_review_case_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'note',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(AiReviewCase::class, 'ai_review_case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
