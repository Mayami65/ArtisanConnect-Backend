<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ServiceJob;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, ServiceJob $serviceJob)
    {
        $validated = $request->validate([
            'proposal' => 'nullable|string',
        ]);

        if ($request->user()->role !== 'artisan') {
            return response()->json(['message' => 'Only artisans can apply'], 403);
        }

        if ($request->user()->id === $serviceJob->client_id) {
            return response()->json(['message' => 'You cannot apply for your own job'], 403);
        }

        // Prevent duplicate applications
        if (Application::query()->where('service_job_id', $serviceJob->id)->where('artisan_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'You have already applied for this job'], 400);
        }

        $application = Application::create([
            'service_job_id' => $serviceJob->id,
            'artisan_id' => $request->user()->id,
            'proposal' => $validated['proposal'] ?? '',
            'status' => 'pending',
        ]);

        return response()->json($application, 201);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $job = $application->serviceJob;
        if ($request->user()->id !== $job->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $application->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted') {
            $job->update(['status' => 'in_progress']);
        }

        return response()->json($application);
    }
}
