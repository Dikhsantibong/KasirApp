<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@kasir.com'], [
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Admin Kasir',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'Kasir'
        ]);

        User::updateOrCreate(['email' => 'owner@kasir.com'], [
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Owner',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'Owner'
        ]);

        $this->call([
            CoffeeShopSeeder::class,
        ]);
    }
}
