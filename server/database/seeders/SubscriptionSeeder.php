<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $gyms = User::where('role', 'user')->orderBy('id')->get();

        foreach ($gyms as $index => $gym) {
            if (Subscription::where('gym_id', $gym->id)->exists()) {
                continue;
            }

            // Alternate between an active (mid-period) gym and an already-expired one
            // so the admin UI shows a realistic mix of statuses.
            if ($index % 2 === 0) {
                // Active: paid 1 month ago for 3 months → ~2 months remaining.
                $paidAt   = Carbon::now()->subMonth();
                $startsAt = $paidAt->copy();
                Subscription::create([
                    'gym_id'    => $gym->id,
                    'plan_type' => Subscription::PLAN_MONTHLY,
                    'months'    => 3,
                    'amount'    => 1500,
                    'currency'  => 'ETB',
                    'starts_at' => $startsAt,
                    'ends_at'   => $startsAt->copy()->addMonths(3),
                    'paid_at'   => $paidAt,
                    'note'      => 'Quarterly plan',
                ]);
            } else {
                // Expired: paid 3 months ago for 1 month → lapsed.
                $paidAt   = Carbon::now()->subMonths(3);
                $startsAt = $paidAt->copy();
                Subscription::create([
                    'gym_id'    => $gym->id,
                    'plan_type' => Subscription::PLAN_MONTHLY,
                    'months'    => 1,
                    'amount'    => 500,
                    'currency'  => 'ETB',
                    'starts_at' => $startsAt,
                    'ends_at'   => $startsAt->copy()->addMonth(),
                    'paid_at'   => $paidAt,
                    'note'      => 'Single month',
                ]);
            }
        }
    }
}
