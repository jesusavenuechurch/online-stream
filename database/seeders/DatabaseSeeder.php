<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StreamSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@church.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // Create initial stream settings
        if (!StreamSettings::exists()) {
            StreamSettings::create([
                'stream_key' => StreamSettings::generateNewKey(),
                'rtmp_url' => config('streaming.rtmp_url', 'rtmp://localhost/live'),
                'updated_by' => User::first()->id,
            ]);
        }

        $this->command->info('Admin user created: admin@church.com / password');
        $this->command->info('Stream settings initialized');

                $this->call([
            ZonesAndGroupsSeeder::class, 
        ]);
    }
}