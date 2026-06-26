<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GymDataSeeder extends Seeder
{
    public function run(): void
    {
        $gyms = User::where('role', 'user')->get();

        foreach ($gyms as $gym) {
            $this->seedGym($gym);
        }
    }

    private function seedGym(User $gym): void
    {
        // Membership types
        $monthly = MembershipType::firstOrCreate(
            ['gym_id' => $gym->id, 'name' => 'Monthly'],
            ['duration_days' => 30, 'allowed_checkins_per_day' => 1, 'description' => 'Standard monthly plan']
        );

        $daily = MembershipType::firstOrCreate(
            ['gym_id' => $gym->id, 'name' => 'Daily Pass'],
            ['duration_days' => 1, 'allowed_checkins_per_day' => 1, 'description' => 'Single day access']
        );

        $annual = MembershipType::firstOrCreate(
            ['gym_id' => $gym->id, 'name' => 'Annual'],
            ['duration_days' => 365, 'allowed_checkins_per_day' => 2, 'description' => 'Full year membership']
        );

        $names = [
            'Abebe Kebede', 'Tigist Haile', 'Dawit Tesfaye', 'Meron Alemu',
            'Yonas Girma', 'Hana Bekele', 'Solomon Tadesse', 'Selam Worku',
            'Biruk Mengistu', 'Feven Assefa', 'Natnael Abebe', 'Liya Tekeste',
            'Mikias Getachew', 'Eden Mulugeta', 'Robel Hailu', 'Sara Desta',
            'Eyob Tesfaye', 'Rahel Woldemariam', 'Belay Gizaw', 'Ayana Negash',
            'Tsion Gebre', 'Habtamu Lakew', 'Kidist Belachew', 'Fikadu Teshome',
            'Selamawit Mamo', 'Girma Wolde', 'Hiwot Yosef', 'Tesfaye Admasu',
            'Almaz Bekele', 'Wondosen Tilahun',
        ];

        $phones = array_map(fn ($i) => '091' . str_pad($gym->id * 100 + $i, 7, '0', STR_PAD_LEFT), range(0, count($names) - 1));
        $types  = [$monthly, $monthly, $monthly, $annual, $daily]; // weight towards monthly

        foreach ($names as $i => $name) {
            $type      = $types[$i % count($types)];
            $startDate = Carbon::now()->subDays(rand(0, 180));
            $expiry    = $startDate->copy()->addDays($type->duration_days);
            $status    = $expiry->isFuture() ? 'active' : 'expired';

            $code = User::generateUniqueCode($gym->id);
            $slug = Str::slug($name) . '-' . $code;

            $member = Member::firstOrCreate(
                ['gym_id' => $gym->id, 'phone' => $phones[$i]],
                [
                    'gym_id'             => $gym->id,
                    'membership_type_id' => $type->id,
                    'slug'               => $slug,
                    'unique_code'        => $code,
                    'full_name'          => $name,
                    'phone'              => $phones[$i],
                    'gender'             => $i % 2 === 0 ? 'male' : 'female',
                    'start_date'         => $startDate,
                    'expiry_date'        => $expiry,
                    'status'             => $status,
                    'created_at'         => $startDate,
                    'updated_at'         => $startDate,
                ]
            );

            // Seed attendance records for active members over last 30 days
            if ($status === 'active') {
                for ($day = 29; $day >= 0; $day--) {
                    if (rand(0, 2) !== 0) { // ~67% attendance rate
                        $checkedInAt = Carbon::now()->subDays($day)->setTime(rand(6, 20), rand(0, 59));
                        Attendance::firstOrCreate(
                            ['gym_id' => $gym->id, 'member_id' => $member->id, 'checked_in_at' => $checkedInAt->toDateString()],
                            [
                                'gym_id'          => $gym->id,
                                'member_id'       => $member->id,
                                'checked_in_at'   => $checkedInAt,
                                'check_in_method' => rand(0, 1) ? 'qr' : 'manual',
                            ]
                        );
                    }
                }
            }
        }
    }
}
