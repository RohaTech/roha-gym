<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'gym_id',
        'member_id',
        'checked_in_at',
        'check_in_method',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gym_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeToday(Builder $query): void
    {
        $query->whereDate('checked_in_at', Carbon::today());
    }
}
