<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\AdminMetricsService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly AdminMetricsService $metricsService
    ) {}

    /**
     * Show main executive admin dashboard.
     */
    public function index()
    {
        $kpis = $this->metricsService->getSummaryKpis();
        $trends = $this->metricsService->getMonthlyTrends();
        $categoryDist = $this->metricsService->getCategoryDistribution();
        $recentActivity = $this->metricsService->getRecentActivity();

        // Top 5 unverified artisans requiring administrative attention
        $pendingArtisans = User::query()
            ->where('role', 'artisan')
            ->where('is_verified', false)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('kpis', 'trends', 'categoryDist', 'recentActivity', 'pendingArtisans'));
    }

    /**
     * JSON endpoint for dashboard analytics data (for mobile apps or async chart reload).
     */
    public function metricsJson()
    {
        return response()->json([
            'kpis' => $this->metricsService->getSummaryKpis(),
            'trends' => $this->metricsService->getMonthlyTrends(),
            'category_distribution' => $this->metricsService->getCategoryDistribution(),
            'recent_activity' => $this->metricsService->getRecentActivity(),
        ]);
    }
}
