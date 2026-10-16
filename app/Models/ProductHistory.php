<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Product catalog change history.
 *
 * Used by the "History" tab on product details; significant changes
 * (premiums, benefits, status) are also recorded in the Audit Log.
 */
class ProductHistory extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'insurance_product_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'reason',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(InsuranceProduct::class, 'insurance_product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
