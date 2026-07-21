<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 3 Clients
        $clients = [
            ['name' => 'Alice Client', 'email' => 'alice@example.com', 'phone' => '0241111111', 'latitude' => 5.6037, 'longitude' => -0.1870],
            ['name' => 'Bob Client', 'email' => 'bob@example.com', 'phone' => '0242222222', 'latitude' => 5.6000, 'longitude' => -0.1800],
            ['name' => 'Charlie Client', 'email' => 'charlie@example.com', 'phone' => '0243333333', 'latitude' => 5.6100, 'longitude' => -0.1900],
        ];

        foreach ($clients as $clientData) {
            User::create([
                'name' => $clientData['name'],
                'email' => $clientData['email'],
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => $clientData['phone'],
                'latitude' => $clientData['latitude'],
                'longitude' => $clientData['longitude'],
            ]);
        }

        // 5 Artisans
        $artisans = [
            ['name' => 'Kwame Plumber', 'email' => 'kwame@example.com', 'category' => 'Plumbing', 'phone' => '0551111111', 'hourly_rate' => 150.00, 'bio' => 'Expert plumber with 10 years of experience in Greater Accra.', 'latitude' => 5.5900, 'longitude' => -0.1700],
            ['name' => 'Yaw Electrician', 'email' => 'yaw@example.com', 'category' => 'Electrical', 'phone' => '0552222222', 'hourly_rate' => 200.00, 'bio' => 'Licensed electrician available for emergency repairs.', 'latitude' => 5.6050, 'longitude' => -0.1850],
            ['name' => 'Ama Carpenter', 'email' => 'ama@example.com', 'category' => 'Carpentry', 'phone' => '0553333333', 'hourly_rate' => 120.00, 'bio' => 'Custom furniture design and repair.', 'latitude' => 5.6150, 'longitude' => -0.1950],
            ['name' => 'Kofi Developer', 'email' => 'kofi@example.com', 'category' => 'Web Development', 'phone' => '0554444444', 'hourly_rate' => 300.00, 'bio' => 'Full-stack web developer building beautiful apps.', 'latitude' => 5.6000, 'longitude' => -0.1800],
            ['name' => 'Esi Cleaning', 'email' => 'esi@example.com', 'category' => 'Cleaning', 'phone' => '0555555555', 'hourly_rate' => 80.00, 'bio' => 'Professional house and office cleaning services.', 'latitude' => 5.6020, 'longitude' => -0.1820],
        ];

        foreach ($artisans as $artisanData) {
            User::create([
                'name' => $artisanData['name'],
                'email' => $artisanData['email'],
                'password' => Hash::make('password'),
                'role' => 'artisan',
                'category' => $artisanData['category'],
                'phone' => $artisanData['phone'],
                'hourly_rate' => $artisanData['hourly_rate'],
                'bio' => $artisanData['bio'],
                'latitude' => $artisanData['latitude'],
                'longitude' => $artisanData['longitude'],
            ]);
        }
    }
}
