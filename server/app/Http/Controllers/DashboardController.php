<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, int $gym): JsonResponse
    {
        if ($gym !== $request->user()->id) {
            abort(403);
        }

        return response()->json([
            'snapshot' => [
                'total_members'   => Member::where('gym_id', $gym)->count(),
                'active_members'  => Member::where('gym_id', $gym)->where('status', 'active')->count(),
                'expired_members' => Member::where('gym_id', $gym)->where('status', 'expired')->count(),
                'checkins_today'  => Attendance::where('gym_id', $gym)
                                        ->whereDate('checked_in_at', today())->count(),
            ],
            'recent_checkins' => Attendance::with('member')
                ->where('gym_id', $gym)
                ->whereDate('checked_in_at', today())
                ->latest('checked_in_at')
                ->take(10)
                ->get()
                ->map(fn($a) => [
                    'member_name'   => $a->member->full_name,
                    'member_photo'  => $a->member->photo_path
                        ? asset('storage/' . $a->member->photo_path)
                        : null,
                    'checked_in_at' => $a->checked_in_at->format('h:i A'),
                ]),
            'expiring_soon' => Member::where('gym_id', $gym)
                ->whereBetween('expiry_date', [today(), today()->addDays(7)])
                ->orderBy('expiry_date')
                ->get()
                ->map(fn($m) => [
                    'name'        => $m->full_name,
                    'photo'       => $m->photo_path
                        ? asset('storage/' . $m->photo_path)
                        : null,
                    'expiry_date' => $m->expiry_date->format('M d, Y'),
                    'days_left'   => (int) round(today()->diffInDays($m->expiry_date)),
                ]),
            'weekly_checkins' => collect(range(6, 0))->map(fn($i) => [
                'date'  => today()->subDays($i)->format('D'),
                'count' => Attendance::where('gym_id', $gym)
                    ->whereDate('checked_in_at', today()->subDays($i))
                    ->count(),
            ])->values(),
        ]);
    }
}
