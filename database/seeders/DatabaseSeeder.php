<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Contracts\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('roles')->insert([
            'name' => 'user',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => true
        ]);


        // \App\Models\User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ShieldSeeder::class,
        ]);
    }
}
