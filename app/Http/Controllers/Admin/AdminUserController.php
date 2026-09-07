<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users with filters and search.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name, email, or phone
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->input('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        // Verification status filter
        if ($request->has('verified') && $request->input('verified') !== '') {
            $query->where('is_verified', (bool) $request->input('verified'));
        }

        // Active status filter
        if ($request->has('active') && $request->input('active') !== '') {
            $query->where('is_active', (bool) $request->input('active'));
        }

        $users = $query->withCount(['jobs', 'applications', 'reviewsReceived'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'total' => User::query()->count('*'),
            'artisans' => User::query()->where('role', 'artisan')->count('*'),
            'clients' => User::query()->where('role', 'client')->count('*'),
            'pending_verifications' => User::query()->where('role', 'artisan')->where('is_verified', false)->count('*'),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    /**
     * Display the specified user profile.
     */
    public function show(User $user)
    {
        $user->loadCount(['jobs', 'applications', 'reviewsReceived', 'reviewsGiven']);

        $jobs = $user->jobs()->latest()->take(10)->get();
        $applications = $user->applications()->with('serviceJob')->latest()->take(10)->get();
        $reviewsReceived = $user->reviewsReceived()->with('client')->latest()->take(10)->get();

        $averageRating = $user->reviewsReceived()->avg('rating') ? round((float) $user->reviewsReceived()->avg('rating'), 1) : null;

        return view('admin.users.show', compact('user', 'jobs', 'applications', 'reviewsReceived', 'averageRating'));
    }

    /**
     * Update verification status of an artisan.
     */
    public function updateVerification(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_verified' => ['required', 'boolean'],
            'verification_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'is_verified' => $validated['is_verified'],
            'verification_notes' => $validated['verification_notes'] ?? $user->verification_notes,
        ]);

        $statusText = $validated['is_verified'] ? 'verified' : 'unverified';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "User {$user->name} has been marked as {$statusText}.",
                'user' => $user,
            ]);
        }

        return back()->with('success', "Artisan {$user->name} verification status updated to: {$statusText}.");
    }

    /**
     * Toggle active/suspended status of a user.
     */
    public function toggleActive(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own administrator account.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusText = $user->is_active ? 'activated' : 'suspended';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "User {$user->name} has been {$statusText}.",
                'user' => $user,
            ]);
        }

        return back()->with('success', "User {$user->name} has been {$statusText}.");
    }
}
