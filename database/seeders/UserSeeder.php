<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ];

        // User::insert($users);

        foreach ($users as $data) {
            $user = User::create($data);

            Profile::create([
                'user_id' => $user->id,
                'avatar' => 'https://i.pravatar.cc/150?u=' . $user->email,
                'birth_date' => now()->subYears(rand(18, 35))->format('Y-m-d'),
                'gender' => rand(0, 1) ? 'female' : 'male',
                'about_me' => 'Hi, I’m ' . $user->name . ' and I love games!',
                'location' => 'Tokyo',
            ]);
        }
    }
}
