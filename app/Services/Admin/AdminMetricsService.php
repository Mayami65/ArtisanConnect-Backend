<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\ServiceJob;
use App\Models\Category;
use App\Models\Review;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminMetricsService
{
    /**
     * Get high-level summary KPIs.
     */
    public function getSummaryKpis(): array
    {
        $totalUsers = User::query()->count('*');
        $totalArtisans = User::query()->where('role', 'artisan')->count('*');
        $totalClients = User::query()->where('role', 'client')->count('*');
        $verifiedArtisans = User::query()->where('role', 'artisan')->where('is_verified', true)->count('*');
        $pendingVerifications = User::query()->where('role', 'artisan')->where('is_verified', false)->count('*');

        $totalJobs = ServiceJob::query()->count('*');
        $openJobs = ServiceJob::query()->where('status', 'open')->count('*');
        $inProgressJobs = ServiceJob::query()->where('status', 'in_progress')->count('*');
        $completedJobs = ServiceJob::query()->where('status', 'completed')->count('*');
        $cancelledJobs = ServiceJob::query()->where('status', 'cancelled')->count('*');
        $totalBudgetVolume = (float) ServiceJob::query()->sum('budget');

        $totalReviews = Review::query()->count('*');
        $averageRating = Review::query()->avg('rating') ? round((float) Review::query()->avg('rating'), 1) : 0.0;
        $totalCategories = Category::query()->count('*');

        return [
            'total_users' => $totalUsers,
            'total_artisans' => $totalArtisans,
            'total_clients' => $totalClients,
            'verified_artisan_rate' => $totalArtisans > 0 ? round(($verifiedArtisans / $totalArtisans) * 100) : 0,
            'pending_verifications' => $pendingVerifications,
            'total_jobs' => $totalJobs,
            'open_jobs' => $openJobs,
            'in_progress_jobs' => $inProgressJobs,
            'completed_jobs' => $completedJobs,
            'cancelled_jobs' => $cancelledJobs,
            'total_budget_volume' => $totalBudgetVolume,
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'total_categories' => $totalCategories,
        ];
    }

    /**
     * Get monthly job and user trends for the past 6 months.
     */
    public function getMonthlyTrends(): array
    {
        $months = [];
        $jobCounts = [];
        $userCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $months[] = $monthLabel;

            $jobCounts[] = ServiceJob::query()->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count('*');

            $userCounts[] = User::query()->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count('*');
        }

        return [
            'labels' => $months,
            'jobs' => $jobCounts,
            'users' => $userCounts,
        ];
    }

    /**
     * Get job distribution by category.
     */
    public function getCategoryDistribution(): array
    {
        $categories = Category::all();
        $labels = [];
        $data = [];
        $colors = [];

        foreach ($categories as $cat) {
            $count = ServiceJob::query()->where('category', $cat->name)->count('*');
            if ($count > 0 || count($labels) < 8) {
                $labels[] = $cat->name;
                $data[] = $count;
                $colors[] = $cat->color_hex ?: '#a23900';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    /**
     * Get recent platform activity across jobs, applications, reviews, and signups.
     */
    public function getRecentActivity(): array
    {
        $activities = [];

        // Recent Jobs
        $recentJobs = ServiceJob::query()->with('client')->latest('created_at')->take(4)->get();
        foreach ($recentJobs as $job) {
            $activities[] = [
                'type' => 'job_created',
                'title' => 'New Job Posted: ' . $job->title,
                'subtitle' => 'By ' . ($job->client?->name ?? 'Unknown Client') . ' • Budget: GHS ' . number_format($job->budget, 2),
                'status' => $job->status,
                'time' => $job->created_at ? $job->created_at->diffForHumans() : 'Recently',
                'timestamp' => $job->created_at ? $job->created_at->timestamp : 0,
                'badge_color' => 'bg-amber-100 text-amber-800',
            ];
        }

        // Recent Users
        $recentUsers = User::query()->latest('created_at')->take(4)->get();
        foreach ($recentUsers as $u) {
            $activities[] = [
                'type' => 'user_registered',
                'title' => 'New ' . ucfirst($u->role) . ' Registered: ' . $u->name,
                'subtitle' => ($u->category ? $u->category . ' • ' : '') . $u->email,
                'status' => $u->role,
                'time' => $u->created_at ? $u->created_at->diffForHumans() : 'Recently',
                'timestamp' => $u->created_at ? $u->created_at->timestamp : 0,
                'badge_color' => $u->role === 'artisan' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800',
            ];
        }

        // Recent Reviews
        $recentReviews = Review::query()->with(['client', 'artisan'])->latest('created_at')->take(3)->get();
        foreach ($recentReviews as $rev) {
            $activities[] = [
                'type' => 'review_submitted',
                'title' => 'Review (' . $rev->rating . ' ★) for ' . ($rev->artisan?->name ?? 'Artisan'),
                'subtitle' => 'By ' . ($rev->client?->name ?? 'Client') . ': "' . \Illuminate\Support\Str::limit($rev->comment, 40) . '"',
                'status' => $rev->rating . ' Stars',
                'time' => $rev->created_at ? $rev->created_at->diffForHumans() : 'Recently',
                'timestamp' => $rev->created_at ? $rev->created_at->timestamp : 0,
                'badge_color' => 'bg-emerald-100 text-emerald-800',
            ];
        }

        // Sort combined activity chronologically descending
        usort($activities, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return array_slice($activities, 0, 8);
    }
}
