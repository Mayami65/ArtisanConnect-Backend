<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ArtisanController extends Controller
{
    public function show(User $artisan)
    {
        if ($artisan->role !== 'artisan') {
            return response()->json(['message' => 'User is not an artisan'], 404);
        }

        $artisan->load('reviewsReceived');
        
        return response()->json([
            'id' => $artisan->id,
            'name' => $artisan->name,
            'category' => $artisan->category,
            'rating' => $artisan->reviewsReceived->avg('rating') ?? 0,
            'jobsCompleted' => $artisan->applications()->where('status', 'accepted')->count(),
            'bio' => $artisan->bio,
            'portfolioImageUrls' => [],
            'reviews' => $artisan->reviewsReceived()->with('client')->latest()->take(5)->get()->map(function($review) {
                return [
                    'authorName' => $review->client->name ?? 'Anonymous',
                    'rating' => $review->rating,
                    'text' => $review->comment,
                    'date' => $review->created_at,
                ];
            }),
        ]);
    }
}
