<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ServiceJob;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Models\Category;

class ServiceJobSeeder extends Seeder
{
    public function run()
    {
        $clients = User::query()->where('role', 'client')->get();
        if ($clients->isEmpty()) return;
        
        $categories = Category::query()->pluck('name')->toArray();
        if (empty($categories)) {
            $categories = ['Plumbing', 'Electrical', 'Carpentry', 'Cleaning', 'Web Development'];
        }
        
        $faker = Faker::create();

        // 1. Specific Jobs (Keep the original ones for the walkthrough/testing context)
        ServiceJob::create([
            'client_id' => $clients[0]->id,
            'title' => 'Fix leaking kitchen pipe',
            'description' => 'The pipe under the kitchen sink is leaking heavily and needs urgent repair.',
            'category' => 'Plumbing',
            'budget' => 200.00,
            'location' => 'East Legon, Accra',
            'latitude' => 5.6350,
            'longitude' => -0.1500,
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        ServiceJob::create([
            'client_id' => $clients[1]->id ?? $clients[0]->id,
            'title' => 'Install new lighting fixtures',
            'description' => 'Need an electrician to install 5 new LED ceiling lights in the living room.',
            'category' => 'Electrical',
            'budget' => 500.00,
            'location' => 'Osu, Accra',
            'latitude' => 5.5550,
            'longitude' => -0.1800,
            'status' => 'in_progress',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        ServiceJob::create([
            'client_id' => $clients[2]->id ?? $clients[0]->id,
            'title' => 'Build an e-commerce website',
            'description' => 'Looking for a developer to build a Shopify store for my fashion business.',
            'category' => 'Web Development',
            'budget' => 2500.00,
            'location' => 'Remote',
            'latitude' => 5.6037,
            'longitude' => -0.1870,
            'status' => 'completed',
            'created_at' => Carbon::now()->subDays(20),
        ]);
        
        ServiceJob::create([
            'client_id' => $clients[0]->id,
            'title' => 'Deep cleaning for 3-bedroom apartment',
            'description' => 'Moving out soon, need a thorough deep clean of the entire apartment.',
            'category' => 'Cleaning',
            'budget' => 300.00,
            'location' => 'Cantonments, Accra',
            'latitude' => 5.5800,
            'longitude' => -0.1650,
            'status' => 'pending',
            'created_at' => Carbon::now()->subHours(5),
        ]);

        // 2. Generate 50 more pending jobs randomly spread around Accra
        for ($i = 0; $i < 50; $i++) {
            $lat = $faker->latitude(5.5000, 5.7000);
            $lng = $faker->longitude(-0.3000, 0.0000);
            
            ServiceJob::create([
                'client_id' => $clients->random()->id,
                'title' => "Need " . strtolower($faker->jobTitle()),
                'description' => $faker->paragraph(),
                'category' => $faker->randomElement($categories),
                'budget' => $faker->randomFloat(2, 50, 1000),
                'location' => $faker->city() . ', Accra',
                'latitude' => $lat,
                'longitude' => $lng,
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours($faker->numberBetween(1, 72)),
            ]);
        }
        
        // 3. Generate 10 more in_progress jobs
        for ($i = 0; $i < 10; $i++) {
            ServiceJob::create([
                'client_id' => $clients->random()->id,
                'title' => "Ongoing " . strtolower($faker->jobTitle()),
                'description' => $faker->paragraph(),
                'category' => $faker->randomElement($categories),
                'budget' => $faker->randomFloat(2, 50, 1000),
                'location' => $faker->city() . ', Accra',
                'latitude' => $faker->latitude(5.5000, 5.7000),
                'longitude' => $faker->longitude(-0.3000, 0.0000),
                'status' => 'in_progress',
                'created_at' => Carbon::now()->subDays($faker->numberBetween(2, 10)),
            ]);
        }
        
        // 4. Generate 10 more completed jobs
        for ($i = 0; $i < 10; $i++) {
            ServiceJob::create([
                'client_id' => $clients->random()->id,
                'title' => "Completed " . strtolower($faker->jobTitle()),
                'description' => $faker->paragraph(),
                'category' => $faker->randomElement($categories),
                'budget' => $faker->randomFloat(2, 50, 1000),
                'location' => $faker->city() . ', Accra',
                'latitude' => $faker->latitude(5.5000, 5.7000),
                'longitude' => $faker->longitude(-0.3000, 0.0000),
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays($faker->numberBetween(10, 30)),
            ]);
        }
    }
}
