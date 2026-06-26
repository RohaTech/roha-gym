<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    public const PLAN_LIFETIME = 'lifetime';
    public const PLAN_MONTHLY  = 'monthly';

    protected $fillable = [
        'gym_id',
        'plan_type',
        'months',
        'amount',
        'currency',
        'starts_at',
        'ends_at',
        'paid_at',
        'note',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'paid_at'   => 'date',
        'amount'    => 'decimal:2',
        'months'    => 'integer',
    ];

    /**
     * The gym (owner user) this subscription belongs to.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gym_id');
    }

    /**
     * Whether this record grants lifetime access (no expiry).
     */
    public function isLifetime(): bool
    {
        return $this->plan_type === self::PLAN_LIFETIME;
    }
}
