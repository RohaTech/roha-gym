<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, int $gym): JsonResponse
    {
        if ($gym !== $request->user()->id) {
            abort(403);
        }

        $range = $request->query('range', '30d');
        $startDate = match ($range) {
            '7d'  => today()->subDays(7),
            '30d' => today()->subDays(30),
            '3m'  => today()->subMonths(3),
            '12m' => today()->subMonths(12),
            default => today()->subDays(30),
        };

        $total   = Member::where('gym_id', $gym)->count();
        $renewed = Member::where('gym_id', $gym)->whereHas('attendances')->count();

        return response()->json([
            'snapshot' => [
                'total_members'      => $total,
                'active_members'     => Member::where('gym_id', $gym)->where('status', 'active')->count(),
                'expired_members'    => Member::where('gym_id', $gym)->where('status', 'expired')->count(),
                'new_this_month'     => Member::where('gym_id', $gym)
                                            ->whereMonth('created_at', now()->month)
                                            ->whereYear('created_at', now()->year)->count(),
                'checkins_today'     => Attendance::where('gym_id', $gym)
                                            ->whereDate('checked_in_at', today())->count(),
                'avg_daily_checkins' => (int) round(
                    Attendance::where('gym_id', $gym)
                        ->where('checked_in_at', '>=', $startDate)
                        ->count() / max(today()->diffInDays($startDate), 1)
                ),
            ],

            'daily_checkins' => Attendance::where('gym_id', $gym)
                ->where('checked_in_at', '>=', $startDate)
                ->selectRaw('DATE(checked_in_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'checkins_by_day_of_week' => Attendance::where('gym_id', $gym)
                ->where('checked_in_at', '>=', $startDate)
                ->selectRaw("TO_CHAR(checked_in_at, 'Dy') as day, COUNT(*) as count")
                ->groupByRaw("TO_CHAR(checked_in_at, 'Dy'), EXTRACT(DOW FROM checked_in_at)")
                ->orderByRaw('EXTRACT(DOW FROM checked_in_at)')
                ->get(),

            'checkins_by_hour' => Attendance::where('gym_id', $gym)
                ->where('checked_in_at', '>=', $startDate)
                ->selectRaw('EXTRACT(HOUR FROM checked_in_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),

            'checkin_method_ratio' => [
                'qr'     => Attendance::where('gym_id', $gym)
                                ->where('checked_in_at', '>=', $startDate)
                                ->where('check_in_method', 'qr')->count(),
                'manual' => Attendance::where('gym_id', $gym)
                                ->where('checked_in_at', '>=', $startDate)
                                ->where('check_in_method', 'manual')->count(),
            ],

            'monthly_registrations' => Member::where('gym_id', $gym)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("TO_CHAR(created_at, 'Mon YYYY') as month, COUNT(*) as count")
                ->groupByRaw("TO_CHAR(created_at, 'Mon YYYY'), DATE_TRUNC('month', created_at)")
                ->orderByRaw("DATE_TRUNC('month', created_at)")
                ->get(),

            'cumulative_members' => Member::where('gym_id', $gym)
                ->where('created_at', '>=', $startDate)
                ->selectRaw("DATE_TRUNC('month', created_at) as month, COUNT(*) as count")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn($m) => [
                    'month' => \Carbon\Carbon::parse($m->month)->format('M Y'),
                    'count' => $m->count,
                ]),

            'retention_rate' => $total > 0
                ? (int) round(($renewed / $total) * 100)
                : 0,

            'membership_distribution' => Member::where('gym_id', $gym)
                ->with('membershipType')
                ->get()
                ->groupBy('membership_type_id')
                ->map(fn($group) => [
                    'type'  => $group->first()->membershipType->name,
                    'count' => $group->count(),
                ])->values(),

            'expiring_soon' => Member::where('gym_id', $gym)
                ->whereBetween('expiry_date', [today(), today()->addDays(7)])
                ->orderBy('expiry_date')
                ->get()
                ->map(fn($m) => [
                    'name'      => $m->full_name,
                    'photo'     => $m->photo_path
                        ? asset('storage/' . $m->photo_path)
                        : null,
                    'days_left' => (int) round(today()->diffInDays($m->expiry_date)),
                ]),

            'expired_this_month' => Member::where('gym_id', $gym)
                ->whereMonth('expiry_date', now()->month)
                ->whereYear('expiry_date', now()->year)
                ->where('expiry_date', '<', today())
                ->count(),

            'most_active_members' => Attendance::where('gym_id', $gym)
                ->where('checked_in_at', '>=', $startDate)
                ->selectRaw('member_id, COUNT(*) as checkin_count')
                ->groupBy('member_id')
                ->orderByDesc('checkin_count')
                ->take(10)
                ->with('member')
                ->get()
                ->map(fn($a) => [
                    'name'          => $a->member->full_name,
                    'photo'         => $a->member->photo_path
                        ? asset('storage/' . $a->member->photo_path)
                        : null,
                    'checkin_count' => (int) $a->checkin_count,
                ]),

            'inactive_members' => Member::where('gym_id', $gym)
                ->where('status', 'active')
                ->whereDoesntHave('attendances', fn($q) =>
                    $q->where('checked_in_at', '>=', today()->subDays(14))
                )
                ->get()
                ->map(fn($m) => [
                    'name'  => $m->full_name,
                    'photo' => $m->photo_path
                        ? asset('storage/' . $m->photo_path)
                        : null,
                ]),

            'avg_checkins_per_member_per_week' => (function () use ($gym) {
                $active   = Member::where('gym_id', $gym)->where('status', 'active')->count();
                $checkins = Attendance::where('gym_id', $gym)
                    ->where('checked_in_at', '>=', today()->subDays(7))->count();
                return $active > 0 ? round($checkins / $active, 1) : 0;
            })(),
        ]);
    }
}
