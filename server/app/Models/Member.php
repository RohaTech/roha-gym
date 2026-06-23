<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'membership_type_id',
        'slug',
        'unique_code',
        'full_name',
        'phone',
        'photo_path',
        'gender',
        'start_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the gym that owns the member.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gym_id');
    }

    /**
     * Get the membership type for this member.
     */
    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
