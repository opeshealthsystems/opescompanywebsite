<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $demos = [
            ['email' => 'admin@demo.opes',    'name' => 'Demo Admin',    'role' => 'admin'],
            ['email' => 'support@demo.opes',  'name' => 'Demo Support',  'role' => 'support'],
            ['email' => 'customer@demo.opes', 'name' => 'Demo Customer', 'role' => 'customer'],
            ['email' => 'tester@demo.opes',   'name' => 'Demo Tester',   'role' => 'tester'],
        ];

        foreach ($demos as $demo) {
            $user = User::firstOrCreate(
                ['email' => $demo['email']],
                ['name' => $demo['name'], 'password' => Hash::make('demo1234')]
            );
            $user->syncRoles([$demo['role']]);
        }
    }
}
