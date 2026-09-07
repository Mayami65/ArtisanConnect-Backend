@extends('layouts.admin')

@section('title', $user->name)
@section('subtitle', ucfirst($user->role) . ' Profile & Activity Record')

@section('content')

    <!-- Top Profile Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-surface-border shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-artisan-100 border border-artisan-300 flex items-center justify-center font-heading font-extrabold text-2xl text-artisan-800">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="font-heading font-bold text-2xl text-slate-900">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->role === 'artisan' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->role === 'artisan')
                            @if($user->is_verified)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Verified
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    Unverified
                                </span>
                            @endif
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-4 mt-1.5 text-xs text-slate-500">
                        <span>{{ $user->email }}</span>
                        @if($user->phone) <span>• {{ $user->phone }}</span> @endif
                        @if($user->category) <span>• Trade: <strong class="text-slate-800">{{ $user->category }}</strong></span> @endif
                        @if($user->hourly_rate) <span>• Rate: <strong class="text-slate-800">GHS {{ number_format($user->hourly_rate, 2) }}/hr</strong></span> @endif
                        @if($averageRating) <span>• Rating: <strong class="text-amber-600">{{ $averageRating }} ★</strong></span> @endif
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2">
                @if($user->role === 'artisan')
                    <form action="{{ route('admin.users.verify', $user) }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_verified" value="{{ $user->is_verified ? 0 : 1 }}">
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition {{ $user->is_verified ? 'bg-amber-100 hover:bg-amber-200 text-amber-800' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                            {{ $user->is_verified ? 'Revoke Verification' : 'Approve Verification' }}
                        </button>
                    </form>
                @endif

                @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" onsubmit="return confirm('Confirm account status change?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ $user->is_active ? 'bg-red-50 hover:bg-red-100 text-red-700' : 'bg-slate-200 hover:bg-slate-300 text-slate-800' }}">
                            {{ $user->is_active ? 'Suspend Account' : 'Activate Account' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($user->bio)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 font-heading">Bio / Professional Statement</h4>
                <p class="text-sm text-slate-700 mt-1">{{ $user->bio }}</p>
            </div>
        @endif

        @if($user->latitude && $user->longitude)
            <div class="mt-4 text-xs text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Coordinates: {{ $user->latitude }}, {{ $user->longitude }}</span>
            </div>
        @endif
    </div>

    <!-- Details Grid based on Role -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        @if($user->role === 'client')
            <!-- Jobs Posted Section -->
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6 lg:col-span-2">
                <h3 class="font-heading font-bold text-base text-slate-900 mb-4">Jobs Posted by {{ $user->name }}</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($jobs as $job)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <a href="{{ route('admin.jobs.show', $job) }}" class="font-semibold text-sm text-slate-900 hover:text-artisan-700">
                                    {{ $job->title }}
                                </a>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $job->category }} • Budget: GHS {{ number_format($job->budget, 2) }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">No service jobs posted yet.</p>
                    @endforelse
                </div>
            </div>
        @endif

        @if($user->role === 'artisan')
            <!-- Applications & Proposals -->
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
                <h3 class="font-heading font-bold text-base text-slate-900 mb-4">Job Applications & Proposals</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <div class="py-3">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('admin.jobs.show', $app->serviceJob) }}" class="font-semibold text-xs text-slate-900 hover:text-artisan-700">
                                    {{ $app->serviceJob?->title ?? 'Job #' . $app->service_job_id }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $app->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($app->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </div>
                            @if($app->proposal)
                                <p class="text-xs text-slate-500 mt-1 italic">"{{ $app->proposal }}"</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-6 text-center">No bids or proposals submitted yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Client Reviews Received -->
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
                <h3 class="font-heading font-bold text-base text-slate-900 mb-4">Reviews & Client Ratings</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($reviewsReceived as $rev)
                        <div class="py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-800">{{ $rev->client?->name ?? 'Client' }}</span>
                                <span class="text-xs font-bold text-amber-500">{{ $rev->rating }} ★</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1">"{{ $rev->comment }}"</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $rev->created_at ? $rev->created_at->format('M d, Y') : '' }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-6 text-center">No reviews received yet.</p>
                    @endforelse
                </div>
            </div>
        @endif

    </div>

@endsection
