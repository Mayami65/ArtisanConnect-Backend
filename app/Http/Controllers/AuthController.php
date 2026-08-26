<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerClient(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $request->filled('password') ? $validated['password'] : \Illuminate\Support\Str::random(32),
            'role' => 'client',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function registerArtisan(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'skill_category' => 'required|string',
            'hourly_rate' => 'required|numeric',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $request->filled('password') ? $validated['password'] : \Illuminate\Support\Str::random(32),
            'role' => 'artisan',
            'category' => $validated['skill_category'],
            'hourly_rate' => $validated['hourly_rate'],
            'bio' => $validated['bio'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $user = User::query()->where('email', $validated['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function firebaseAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $auth = app('firebase.auth');
            $verifiedIdToken = $auth->verifyIdToken($request->id_token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $auth->getUser($uid);
            $phone = $firebaseUser->phoneNumber;
            $email = $firebaseUser->email;

            if (!$phone && !$email) {
                return response()->json(['message' => 'No phone or email linked to this Firebase account'], 400);
            }

            // Check if user exists in our DB
            $user = null;
            if ($phone) {
                $user = User::query()->where('phone', $phone)->first();
            }
            if (!$user && $email) {
                $user = User::query()->where('email', $email)->first();
            }

            if (!$user) {
                // New user - require registration
                return response()->json([
                    'message' => 'User not registered. Please complete registration.',
                    'requires_registration' => true,
                    'phone' => $phone,
                    'email' => $email,
                    'full_name' => $firebaseUser->displayName,
                ], 200);
            }

            // User exists - log them in
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Firebase Token', 'error' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
