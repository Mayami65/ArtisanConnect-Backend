<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceJob;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // Find completed job
        $job3 = ServiceJob::query()->where('title', 'Build an e-commerce website')->where('status', 'completed')->first();
        if ($job3) {
            // Find the accepted application to know the artisan
            $application = $job3->applications()->where('status', 'accepted')->first();
            if ($application) {
                // Client leaves a review for the artisan
                Review::create([
                    'service_job_id' => $job3->id,
                    'client_id' => $job3->client_id,
                    'artisan_id' => $application->artisan_id,
                    'rating' => 5.0,
                    'comment' => 'Absolutely fantastic work! The store looks amazing and was delivered ahead of schedule.',
                ]);
            }
        }
    }
}
