<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, ServiceJob $serviceJob)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'artisan_id' => 'required|exists:users,id',
        ]);

        if ($request->user()->id !== $serviceJob->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review = Review::create([
            'service_job_id' => $serviceJob->id,
            'client_id' => $request->user()->id,
            'artisan_id' => $validated['artisan_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
        ]);

        return response()->json($review, 201);
    }

    public function artisanReviews($artisanId)
    {
        $reviews = Review::where('artisan_id', $artisanId)->with('client', 'serviceJob')->latest()->get();
        return response()->json($reviews);
    }
}
