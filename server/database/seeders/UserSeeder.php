<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Platform super-admin
        User::updateOrCreate(
            ['phone' => '0911111111'],
            [
                'name'     => 'Admin',
                'phone'    => '0911111111',
                'password' => Hash::make('12345678'),
                'status'   => USER_STATUS_ACTIVE,
                'role'     => 'admin',
            ]
        );

        // Gym owner accounts
        User::updateOrCreate(
            ['phone' => '0912345678'],
            [
                'name'     => 'FitZone Gym',
                'phone'    => '0912345678',
                'password' => Hash::make('12345678'),
                'status'   => USER_STATUS_ACTIVE,
                'address'  => 'Bole, Addis Ababa',
                'role'     => 'user',
            ]
        );

        User::updateOrCreate(
            ['phone' => '0913456789'],
            [
                'name'     => 'PowerHouse Gym',
                'phone'    => '0913456789',
                'password' => Hash::make('12345678'),
                'status'   => USER_STATUS_ACTIVE,
                'address'  => 'Piazza, Addis Ababa',
                'role'     => 'user',
            ]
        );
    }
}
