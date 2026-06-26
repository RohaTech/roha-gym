<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin
                            {--name= : Admin display name}
                            {--phone= : Admin phone (login identifier)}
                            {--password= : Admin password (min 8 chars)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a platform admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name     = $this->option('name') ?: $this->ask('Admin name');
        $phone    = $this->option('phone') ?: $this->ask('Admin phone');
        $password = $this->option('password') ?: $this->secret('Admin password (min 8 chars)');

        $validator = Validator::make(
            compact('name', 'phone', 'password'),
            [
                'name'     => 'required|string|max:255',
                'phone'    => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:8',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name'     => $name,
            'phone'    => $phone,
            'password' => Hash::make($password),
            'status'   => USER_STATUS_ACTIVE,
            'role'     => 'admin',
        ]);

        $this->info("Admin created: {$user->name} ({$user->phone}) [id: {$user->id}]");

        return self::SUCCESS;
    }
}
