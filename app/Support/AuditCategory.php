<?php

namespace App\Support;

/**
 * List of Audit Log activity categories.
 *
 * Defined as constants so callers do not use arbitrary strings and categories
 * remain consistent with the filters on the Audit Log page.
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
     * All valid categories for filtering.
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
