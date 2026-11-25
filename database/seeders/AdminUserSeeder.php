<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', '!=', config('portfolio.admin.email'))->delete();

        User::updateOrCreate(
            ['email' => config('portfolio.admin.email')],
            [
                'name' => 'Admin',
                'password' => Hash::make(config('portfolio.admin.password')),
            ],
        );
    }
}
