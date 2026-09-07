<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Display a listing of client and artisan reviews.
     */
    public function index(Request $request)
    {
        $query = Review::with(['client', 'artisan', 'serviceJob']);

        // Rating filter
        if ($rating = $request->input('rating')) {
            if ($rating !== 'all') {
                $query->where('rating', (int) $rating);
            }
        }

        // Search in comments
        if ($search = $request->input('search')) {
            $query->where('comment', 'like', "%{$search}%");
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        $ratingDistribution = [
            5 => Review::query()->where('rating', 5)->count('*'),
            4 => Review::query()->where('rating', 4)->count('*'),
            3 => Review::query()->where('rating', 3)->count('*'),
            2 => Review::query()->where('rating', 2)->count('*'),
            1 => Review::query()->where('rating', 1)->count('*'),
        ];

        $averageRating = Review::query()->avg('rating') ? round((float) Review::query()->avg('rating'), 1) : 0;
        $totalReviews = Review::query()->count('*');

        return view('admin.reviews.index', compact('reviews', 'ratingDistribution', 'averageRating', 'totalReviews'));
    }

    /**
     * Remove an abusive or fraudulent review.
     */
    public function destroy(Review $review)
    {
        Review::destroy($review->id);

        return back()->with('success', 'Review deleted successfully.');
    }
}
