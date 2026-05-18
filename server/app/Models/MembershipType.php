<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'name',
        'duration_days',
        'allowed_checkins_per_day',
        'description',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'allowed_checkins_per_day' => 'integer',
    ];

    /**
     * Get the gym that owns the membership type.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gym_id');
    }

    /**
     * Get the members for this membership type.
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
