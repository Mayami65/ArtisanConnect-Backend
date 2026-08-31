<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'phone' => $user->phone,
            'joined_at' => $user->created_at,
        ];

        if ($user->role === 'artisan') {
            $user->load('reviewsReceived.client');
            
            $profile['category'] = $user->category ?? 'Artisan';
            $profile['bio'] = $user->bio ?? '';
            $profile['rating'] = $user->reviewsReceived->avg('rating') ?? 0;
            $profile['jobsCompleted'] = $user->applications()->where('status', 'accepted')->count();
            $profile['portfolioImageUrls'] = [];
            $profile['reviews'] = $user->reviewsReceived()->latest()->take(5)->get()->map(function($review) {
                return [
                    'authorName' => $review->client->name ?? 'Anonymous',
                    'rating' => $review->rating,
                    'text' => $review->comment,
                    'date' => $review->created_at,
                ];
            });
        } elseif ($user->role === 'client') {
            $user->load(['jobs' => function($query) {
                $query->whereIn('status', ['pending', 'accepted', 'in_progress'])->latest()->take(10);
            }]);
            
            $profile['jobsPosted'] = $user->jobs->map(function($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'category' => $job->category,
                    'budget' => $job->budget,
                    'location' => $job->location,
                    'status' => $job->status,
                    'created_at' => $job->created_at,
                ];
            });
        }

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
}
