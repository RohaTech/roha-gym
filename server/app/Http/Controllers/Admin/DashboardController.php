<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Platform-wide aggregate dashboard across all gyms.
     */
    public function index(Request $request): JsonResponse
    {
        $gyms = User::where('role', 'user');

        return response()->json([
            'snapshot' => [
                'total_gyms'        => (clone $gyms)->count(),
                'active_gyms'       => (clone $gyms)->where('status', USER_STATUS_ACTIVE)->count(),
                'suspended_gyms'    => (clone $gyms)->where('status', USER_STATUS_INACTIVE)->count(),
                'pending_gyms'      => (clone $gyms)->where('status', USER_STATUS_PENDING)->count(),
                'new_gyms_this_month' => (clone $gyms)
                                        ->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)->count(),
                'total_members'     => Member::count(),
                'active_members'    => Member::where('status', 'active')->count(),
                'checkins_today'    => Attendance::whereDate('checked_in_at', today())->count(),
            ],

            // Gyms registered per month (last 12 months)
            'gyms_growth' => User::where('role', 'user')
                ->where('created_at', '>=', today()->subMonths(12))
                ->selectRaw("TO_CHAR(created_at, 'Mon YYYY') as month, COUNT(*) as count")
                ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY'), DATE_TRUNC('month', created_at)")
                ->orderByRaw("DATE_TRUNC('month', created_at)")
                ->get(),

            // Members registered per month across all gyms (last 12 months)
            'members_growth' => Member::where('created_at', '>=', today()->subMonths(12))
                ->selectRaw("TO_CHAR(created_at, 'Mon YYYY') as month, COUNT(*) as count")
                ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY'), DATE_TRUNC('month', created_at)")
                ->orderByRaw("DATE_TRUNC('month', created_at)")
                ->get(),

            // Platform check-ins over the last 30 days
            'checkins_trend' => collect(range(29, 0))->map(fn ($i) => [
                'date'  => today()->subDays($i)->format('M d'),
                'count' => Attendance::whereDate('checked_in_at', today()->subDays($i))->count(),
            ])->values(),

            // Top gyms by member count
            'top_gyms_by_members' => User::where('role', 'user')
                ->withCount('members')
                ->orderByDesc('members_count')
                ->take(10)
                ->get()
                ->map(fn ($g) => [
                    'id'            => $g->id,
                    'name'          => $g->name,
                    'logo'          => $g->logo_path ? asset('storage/' . $g->logo_path) : null,
                    'members_count' => (int) $g->members_count,
                ]),

            // Top gyms by check-in activity (last 30 days)
            'top_gyms_by_activity' => Attendance::where('checked_in_at', '>=', today()->subDays(30))
                ->selectRaw('gym_id, COUNT(*) as checkin_count')
                ->groupBy('gym_id')
                ->orderByDesc('checkin_count')
                ->take(10)
                ->with('gym:id,name,logo_path')
                ->get()
                ->map(fn ($a) => [
                    'id'            => $a->gym_id,
                    'name'          => $a->gym?->name,
                    'logo'          => $a->gym?->logo_path ? asset('storage/' . $a->gym->logo_path) : null,
                    'checkin_count' => (int) $a->checkin_count,
                ]),
        ]);
    }
}
