<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInRequest;
use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class CheckInController extends Controller
{
    public function store(CheckInRequest $request): JsonResponse
    {
        $identifier = $request->input('identifier');
        $method     = $request->input('method');
        $gymId = $request->user()->id;

        $member = Member::where('gym_id', $gymId)
            ->where('unique_code', $identifier)
            ->with('membershipType')
            ->first();

        if (! $member) {
            return response()->json(['success' => false, 'reason' => 'not_found']);
        }

        $isExpired = Carbon::parse($member->expiry_date)->isBefore(Carbon::today());
        
        if ($isExpired) {
            return response()->json(['success' => false, 'reason' => 'expired']);
        }

        $todayCount = Attendance::where('member_id', $member->id)
            ->today()
            ->count();

        $limit = $member->membershipType->allowed_checkins_per_day ?? PHP_INT_MAX;

        if ($todayCount >= $limit) {
            return response()->json(['success' => false, 'reason' => 'limit_reached']);
        }

        $previousAttendance = Attendance::where('member_id', $member->id)
            ->latest('checked_in_at')
            ->first();

        Attendance::create([
            'gym_id'          => $gymId,
            'member_id'       => $member->id,
            'checked_in_at'   => now(),
            'check_in_method' => $method,
        ]);

        $remainingDays = now()->diffInDays($member->expiry_date);
        $lastCheckIn = $previousAttendance?->checked_in_at;

        return response()->json([
            'success' => true,
            'member'  => [
                'name'           => $member->full_name,
                'photo'          => $member->photo_path,
                'remaining_days' => $remainingDays,
                'last_check_in'  => $lastCheckIn,
                'today_count'    => $todayCount + 1,
            ],
        ]);
    }
}
