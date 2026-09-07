<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    /**
     * Display a listing of service jobs with filters and search.
     */
    public function index(Request $request)
    {
        $query = ServiceJob::with(['client'])->withCount('applications');

        // Search by title or description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->where('category', $category);
            }
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $jobs = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        $statusCounts = [
            'total' => ServiceJob::query()->count('*'),
            'open' => ServiceJob::query()->where('status', 'open')->count('*'),
            'in_progress' => ServiceJob::query()->where('status', 'in_progress')->count('*'),
            'completed' => ServiceJob::query()->where('status', 'completed')->count('*'),
            'cancelled' => ServiceJob::query()->where('status', 'cancelled')->count('*'),
        ];

        return view('admin.jobs.index', compact('jobs', 'categories', 'statusCounts'));
    }

    /**
     * Display detailed job specifications, applications, and client info.
     */
    public function show(ServiceJob $serviceJob)
    {
        $serviceJob->load(['client', 'applications.artisan', 'reviews.client', 'reviews.artisan']);

        return view('admin.jobs.show', compact('serviceJob'));
    }

    /**
     * Update job status manually (e.g. dispute resolution or cancellation).
     */
    public function updateStatus(Request $request, ServiceJob $serviceJob)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,completed,cancelled'],
        ]);

        $serviceJob->update([
            'status' => $validated['status'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Job status updated to {$validated['status']}.",
                'job' => $serviceJob,
            ]);
        }

        return back()->with('success', "Job \"{$serviceJob->title}\" status changed to {$validated['status']}.");
    }
}
