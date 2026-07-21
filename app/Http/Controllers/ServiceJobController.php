<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use Illuminate\Http\Request;

class ServiceJobController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user('sanctum') ?? $request->user();
        $query = ServiceJob::with('client')->where('status', 'pending');

        if ($user) {
            $query->where('client_id', '!=', $user->id);
        }

        $jobs = $query->latest()->get();

        if ($request->has('lat') && $request->has('lng')) {
            $userLat = (float) $request->input('lat');
            $userLng = (float) $request->input('lng');

            $jobs = $jobs->map(function ($job) use ($userLat, $userLng) {
                if ($job->latitude !== null && $job->longitude !== null) {
                    $jobLat = (float) $job->latitude;
                    $jobLng = (float) $job->longitude;

                    // Haversine formula
                    $earthRadius = 6371; // km
                    $latDelta = deg2rad($jobLat - $userLat);
                    $lngDelta = deg2rad($jobLng - $userLng);

                    $a = sin($latDelta / 2) * sin($latDelta / 2) +
                        cos(deg2rad($userLat)) * cos(deg2rad($jobLat)) *
                        sin($lngDelta / 2) * sin($lngDelta / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    
                    $job->setAttribute('distance_in_km', round($earthRadius * $c, 1));
                } else {
                    $job->setAttribute('distance_in_km', 9999); // Put at bottom if no location
                }
                return $job;
            })->sortBy('distance_in_km')->values();
        }

        return response()->json($jobs);
    }

    public function clientJobs(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'artisan') {
            return response()->json(
                ServiceJob::query()->whereHas('applications', function ($q) use ($user) {
                    $q->where('artisan_id', $user->id);
                })->with(['client', 'applications' => function ($q) use ($user) {
                    $q->where('artisan_id', $user->id);
                }])->latest()->get()
            );
        } else {
            return response()->json(ServiceJob::query()->where('client_id', $user->id)->with('applications.artisan')->latest()->get());
        }
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'client') {
            return response()->json(['message' => 'Only clients can post jobs'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'budget' => 'required|numeric',
            'location' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048', // 2MB Max per image
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('jobs', 'public');
            }
        }

        $job = ServiceJob::create([
            'client_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'budget' => $validated['budget'],
            'location' => $validated['location'],
            'images' => empty($imagePaths) ? null : $imagePaths,
            'status' => 'pending',
        ]);

        return response()->json($job, 201);
    }

    public function show(Request $request, ServiceJob $serviceJob)
    {
        $user = $request->user();
        if ($user && $user->id === $serviceJob->client_id) {
            return response()->json($serviceJob->load('client', 'applications.artisan'));
        }
        
        // For artisans, only load their own application so the frontend knows they applied
        $serviceJob->load(['client', 'applications' => function ($query) use ($user) {
            if ($user && $user->role === 'artisan') {
                $query->where('artisan_id', $user->id);
            } else {
                // Ensure no applications are loaded for unauthenticated or non-owner clients
                $query->whereRaw('1 = 0'); 
            }
        }]);

        return response()->json($serviceJob);
    }

    public function update(Request $request, ServiceJob $serviceJob)
    {
        if ($request->user()->id !== $serviceJob->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'budget' => 'required|numeric',
            'location' => 'required|string',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048', // 2MB Max per image
        ]);

        $imagePaths = $validated['existing_images'] ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('jobs', 'public');
            }
        }

        $serviceJob->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'budget' => $validated['budget'],
            'location' => $validated['location'],
            'images' => empty($imagePaths) ? null : $imagePaths,
        ]);

        return response()->json($serviceJob);
    }

    public function destroy(Request $request, ServiceJob $serviceJob)
    {
        if ($request->user()->id !== $serviceJob->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        ServiceJob::destroy($serviceJob->id);

        return response()->json(['message' => 'Job deleted']);
    }

    public function updateStatus(Request $request, ServiceJob $serviceJob)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        if ($request->user()->id !== $serviceJob->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $serviceJob->update(['status' => $validated['status']]);

        return response()->json($serviceJob);
    }
}
