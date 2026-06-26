<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Translation\Message;

class SubscriptionController extends Controller
{
    /** Days-before-expiry threshold that flags a subscription as "expiring soon". */
    private const EXPIRING_SOON_DAYS = 7;

    /**
     * List all gyms with their derived subscription summary.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'user')->with('subscriptions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $summaries = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (User $gym) => $this->summarize($gym));

        if ($request->filled('status')) {
            $summaries = $summaries
                ->where('subscription_status', $request->status)
                ->values();
        }

        // Manual pagination over the derived collection (matches PaginatedGyms shape).
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = max(1, (int) $request->input('per_page', 15));
        $total   = $summaries->count();

        return response()->json([
            'data'         => $summaries->forPage($page, $perPage)->values(),
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
            'per_page'     => $perPage,
            'total'        => $total,
        ]);
    }

    /**
     * Show a single gym's subscription summary plus full payment history.
     */
    public function show(int $gym): JsonResponse
    {
        $gymUser = User::where('role', 'user')->with('subscriptions')->findOrFail($gym);

        $payments = $gymUser->subscriptions
            ->sortByDesc('paid_at')
            ->values()
            ->map(fn (Subscription $s) => [
                'id'        => $s->id,
                'plan_type' => $s->plan_type,
                'months'    => $s->months,
                'amount'    => (float) $s->amount,
                'currency'  => $s->currency,
                'starts_at' => $s->starts_at?->format('M d, Y'),
                'ends_at'   => $s->ends_at?->format('M d, Y'),
                'paid_at'   => $s->paid_at?->format('M d, Y'),
                'note'      => $s->note,
            ]);

        return response()->json([
            'summary'  => $this->summarize($gymUser),
            'payments' => $payments,
        ]);
    }

    /**
     * Record a payment for a gym, extending (or starting) its subscription.
     */
    public function store(Request $request, int $gym): JsonResponse
    {
        $gymUser = User::where('role', 'user')->findOrFail($gym);

        $validated = $request->validate([
            'plan_type' => 'required|in:lifetime,monthly',
            'months'    => 'required_if:plan_type,monthly|nullable|integer|min:1|max:120',
            'amount'    => 'required|numeric|min:0',
            'currency'  => 'nullable|string|max:8',
            'paid_at'   => 'required|date',
            'note'      => 'nullable|string|max:500',
        ]);

        $paidAt = Carbon::parse($validated['paid_at'])->startOfDay();

        if ($validated['plan_type'] === Subscription::PLAN_LIFETIME) {
            $months   = null;
            $startsAt = today();
            $endsAt   = null;
        } else {
            $months = (int) $validated['months'];

            // Stack onto any remaining time so consecutive months don't overlap.
            $latestEnd = $gymUser->subscriptions()
                ->whereNotNull('ends_at')
                ->max('ends_at');

            $base = ($latestEnd && Carbon::parse($latestEnd)->isAfter(today()))
                ? Carbon::parse($latestEnd)
                : today();

            $startsAt = $base->copy();
            $endsAt   = $base->copy()->addMonths($months);
        }

        $gymUser->subscriptions()->create([
            'plan_type' => $validated['plan_type'],
            'months'    => $months,
            'amount'    => $validated['amount'],
            'currency'  => $validated['currency'] ?? 'ETB',
            'starts_at' => $startsAt,
            'ends_at'   => $endsAt,
            'paid_at'   => $paidAt,
            'note'      => $validated['note'] ?? null,
        ]);

        // A fresh payment reactivates a gym that was suspended for non-payment.
        if ((int) $gymUser->status !== USER_STATUS_ACTIVE) {
            $gymUser->update(['status' => USER_STATUS_ACTIVE]);
        }

        return response()->json([
            'message' => Message::get('subscription_recorded'),
            'data'    => $this->summarize($gymUser->fresh('subscriptions')),
        ], 201);
    }

    /**
     * Delete a (mis-entered) payment record. Derived status recomputes automatically.
     */
    public function destroy(int $payment): JsonResponse
    {
        $subscription = Subscription::findOrFail($payment);
        $subscription->delete();

        return response()->json([
            'message' => Message::get('subscription_deleted'),
        ]);
    }

    /**
     * Shape a gym + its subscriptions into a consistent summary payload.
     */
    private function summarize(User $gym): array
    {
        $subs = $gym->subscriptions;

        $hasLifetime = $subs->contains(fn (Subscription $s) => $s->isLifetime());
        $latest      = $subs->sortByDesc('paid_at')->first();

        $maxEndsAt = $subs->whereNotNull('ends_at')->max('ends_at');
        $maxEndsAt = $maxEndsAt ? Carbon::parse($maxEndsAt) : null;

        if ($hasLifetime) {
            $status       = 'lifetime';
            $expiryDate   = null;
            $daysLeft     = null;
        } elseif ($subs->isEmpty()) {
            $status       = 'none';
            $expiryDate   = null;
            $daysLeft     = null;
        } else {
            $daysLeft   = (int) today()->diffInDays($maxEndsAt, false);
            $expiryDate = $maxEndsAt->format('M d, Y');

            if ($daysLeft < 0) {
                $status = 'expired';
            } elseif ($daysLeft <= self::EXPIRING_SOON_DAYS) {
                $status = 'expiring_soon';
            } else {
                $status = 'active';
            }
        }

        return [
            'id'                  => $gym->id,
            'name'                => $gym->name,
            'phone'               => $gym->phone,
            'logo'                => $gym->logo_path ? asset('storage/' . $gym->logo_path) : null,
            'gym_status'          => (int) $gym->status,
            'subscription_status' => $status,
            'plan_type'           => $latest?->plan_type,
            'expiry_date'         => $expiryDate,
            'days_until_expiry'   => $daysLeft,
            'total_paid'          => (float) $subs->sum('amount'),
            'currency'            => $latest?->currency ?? 'ETB',
            'last_paid_at'        => $latest?->paid_at?->format('M d, Y'),
        ];
    }
}
