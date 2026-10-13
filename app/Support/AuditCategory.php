<?php

namespace App\Support;

/**
 * Daftar kategori aktivitas Audit Log.
 *
 * Didefinisikan sebagai konstan agar pemanggil tidak menulis string bebas
 * dan kategori tetap konsisten dengan filter pada halaman Audit Log.
 */
final class AuditCategory
{
    public const USER_MANAGEMENT = 'user_management';

    public const INSURANCE_PRODUCT = 'insurance_product';

    public const POLICY_DOCUMENT = 'policy_document';

    public const AI_QUALITY_REVIEW = 'ai_quality_review';

    public const AI_ANALYSIS = 'ai_analysis';

    public const SECURITY = 'security';

    public const AUTHENTICATION = 'authentication';

    public const CHAT = 'chat';

    public const AUDIT = 'audit';

    /**
     * Semua kategori yang valid untuk filter.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::USER_MANAGEMENT,
            self::INSURANCE_PRODUCT,
            self::POLICY_DOCUMENT,
            self::AI_QUALITY_REVIEW,
            self::AI_ANALYSIS,
            self::SECURITY,
            self::AUTHENTICATION,
            self::CHAT,
            self::AUDIT,
        ];
    }
}
