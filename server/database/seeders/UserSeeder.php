<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'phone' => '0911111111',
                'password' => Hash::make('12345678'),
                'status' => USER_STATUS_ACTIVE,
            ],
            [
                'name' => 'Active User',
                'phone' => '0912345678',
                'password' => Hash::make('12345678'),
                'status' => USER_STATUS_ACTIVE,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['phone' => $user['phone']],
                $user
            );
        }
    }
}
