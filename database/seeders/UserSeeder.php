<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@misguppi.sch.id',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Kepala Sekolah',
                'email'    => 'kepala@misguppi.sch.id',
                'password' => Hash::make('kepala123'),
                'role'     => 'kepala_sekolah',
            ],
            [
                'name'     => 'Guru Demo',
                'email'    => 'guru@misguppi.sch.id',
                'password' => Hash::make('guru123'),
                'role'     => 'guru',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}