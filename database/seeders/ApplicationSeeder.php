<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ServiceJob;
use App\Models\Application;

class ApplicationSeeder extends Seeder
{
    public function run()
    {
        $plumber = User::query()->where('category', 'Plumbing')->first();
        $electrician = User::query()->where('category', 'Electrical')->first();
        $developer = User::query()->where('category', 'Web Development')->first();
        $cleaner = User::query()->where('category', 'Cleaning')->first();

        // Plumber applies to Job 1 (pending)
        $job1 = ServiceJob::query()->where('title', 'Fix leaking kitchen pipe')->first();
        if ($plumber && $job1) {
            Application::create([
                'service_job_id' => $job1->id,
                'artisan_id' => $plumber->id,
                'proposal' => 'I have fixed hundreds of leaking pipes. I can be there in 30 minutes and will get it sorted quickly.',
                'status' => 'pending',
            ]);
        }

        // Electrician applies to Job 2 (in_progress)
        $job2 = ServiceJob::query()->where('title', 'Install new lighting fixtures')->first();
        if ($electrician && $job2) {
            Application::create([
                'service_job_id' => $job2->id,
                'artisan_id' => $electrician->id,
                'proposal' => 'Licensed electrician here. I will install them safely and cleanly.',
                'status' => 'accepted',
            ]);
        }

        // Developer applies to Job 3 (completed)
        $job3 = ServiceJob::query()->where('title', 'Build an e-commerce website')->first();
        if ($developer && $job3) {
            Application::create([
                'service_job_id' => $job3->id,
                'artisan_id' => $developer->id,
                'proposal' => 'I build high-converting Shopify stores. Check my portfolio.',
                'status' => 'accepted',
            ]);
        }

        // Cleaner applies to Job 4 (pending)
        $job4 = ServiceJob::query()->where('title', 'Deep cleaning for 3-bedroom apartment')->first();
        if ($cleaner && $job4) {
            Application::create([
                'service_job_id' => $job4->id,
                'artisan_id' => $cleaner->id,
                'proposal' => 'I provide premium deep cleaning services with eco-friendly products.',
                'status' => 'pending',
            ]);
        }
    }
}
