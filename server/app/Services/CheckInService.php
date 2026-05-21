<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Support\Carbon;

class CheckInService
{
    public function handle(string $identifier, string $method, int $gymId): array
    {
        $member = Member::where('gym_id', $gymId)
            ->where(function ($query) use ($identifier) {
                $query->where('slug', $identifier)
                      ->orWhere('unique_code', $identifier);
            })
            ->with('membershipType')
            ->first();

        if (!$member) {
            return ['success' => false, 'reason' => 'not_found'];
        }

        if (Carbon::parse($member->expiry_date)->isBefore(Carbon::today())) {
            return ['success' => false, 'reason' => 'expired'];
        }

        $todayCount = Attendance::where('member_id', $member->id)
            ->today()
            ->count();

        $limit = $member->membershipType->allowed_checkins_per_day ?? PHP_INT_MAX;

        if ($todayCount >= $limit) {
            return ['success' => false, 'reason' => 'limit_reached'];
        }

        $previousAttendance = Attendance::where('member_id', $member->id)
            ->latest('checked_in_at')
            ->first();

        Attendance::create([
            'gym_id' => $gymId,
            'member_id' => $member->id,
            'checked_in_at' => now(),
            'check_in_method' => $method,
        ]);

        return [
            'success' => true,
            'member' => [
                'name' => $member->full_name,
                'photo' => $member->photo_path,
                'remaining_days' => now()->diffInDays($member->expiry_date),
                'last_check_in' => $previousAttendance?->checked_in_at,
                'today_count' => $todayCount + 1,
            ],
        ];
    }
}
