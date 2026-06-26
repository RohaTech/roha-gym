<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'logo_path',
        'address',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
        ];
    }


    /**
     * Members belonging to this gym (when the user is a gym owner).
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'gym_id');
    }

    /**
     * Membership types belonging to this gym.
     */
    public function membershipTypes(): HasMany
    {
        return $this->hasMany(MembershipType::class, 'gym_id');
    }

    /**
     * Attendance records belonging to this gym.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'gym_id');
    }

    /**
     * Subscription / payment records for this gym.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'gym_id');
    }

    /**
     * Generate a unique 5-digit code for a member within the same gym.
     *
     * @param int $gymId
     * @return int
     */
    public static function generateUniqueCode(int $gymId): int{
        do {
            $code = random_int(10000, 99999); // Generate a random 5-digit number
            $exists = Member::where('gym_id', $gymId)->where('unique_code', $code)->exists();
        } while ($exists); // Repeat until a unique code is found

        return $code;
    }
}
