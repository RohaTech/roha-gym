<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend gyms whose subscription has lapsed (no active or lifetime coverage)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $gyms = User::where('role', 'user')
            ->where('status', USER_STATUS_ACTIVE)
            ->with('subscriptions')
            ->get();

        $suspended = 0;

        foreach ($gyms as $gym) {
            $hasLifetime = $gym->subscriptions
                ->contains(fn (Subscription $s) => $s->isLifetime());

            if ($hasLifetime) {
                continue;
            }

            $maxEndsAt = $gym->subscriptions->whereNotNull('ends_at')->max('ends_at');

            // A gym with coverage that ends today is still active; only past-due lapses.
            $lapsed = $maxEndsAt
                ? \Illuminate\Support\Carbon::parse($maxEndsAt)->isBefore(today())
                : true; // no subscription at all → not paid

            if ($lapsed) {
                $gym->update(['status' => USER_STATUS_INACTIVE]);
                $suspended++;
                $this->line("Suspended: {$gym->name} (id {$gym->id})");
            }
        }

        $this->info("Done. {$suspended} gym(s) suspended for lapsed subscriptions.");

        return self::SUCCESS;
    }
}
