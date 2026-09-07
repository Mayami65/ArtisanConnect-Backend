@extends('layouts.admin')

@section('title', 'Job: ' . $serviceJob->title)
@section('subtitle', 'Job ID #' . $serviceJob->id . ' • Contract & Bid Oversight')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Job Details, Applications & Reviews -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Job Card -->
            <div class="bg-white rounded-3xl p-6 border border-surface-border shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-artisan-100 text-artisan-800 mb-2">
                            {{ $serviceJob->category }}
                        </span>
                        <h2 class="font-heading font-bold text-2xl text-slate-900">{{ $serviceJob->title }}</h2>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block sm:text-right">Contract Budget</span>
                        <p class="font-heading font-extrabold text-2xl text-slate-900 sm:text-right">
                            GHS {{ number_format($serviceJob->budget, 2) }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 font-heading mb-1.5">Job Description</h4>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $serviceJob->description }}</p>
                </div>

                @if($serviceJob->location)
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>Location: <strong class="text-slate-700">{{ $serviceJob->location }}</strong></span>
                        @if($serviceJob->latitude && $serviceJob->longitude)
                            <span>({{ $serviceJob->latitude }}, {{ $serviceJob->longitude }})</span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Artisan Bids & Applications -->
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-base text-slate-900">
                        Artisan Proposals & Applications ({{ $serviceJob->applications->count() }})
                    </h3>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($serviceJob->applications as $app)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-800 font-heading font-bold flex items-center justify-center text-sm shrink-0">
                                    {{ strtoupper(substr($app->artisan?->name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.users.show', $app->artisan) }}" class="font-heading font-bold text-sm text-slate-900 hover:text-artisan-700">
                                            {{ $app->artisan?->name ?? 'Artisan' }}
                                        </a>
                                        @if($app->artisan?->is_verified)
                                            <span class="text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-semibold">Verified</span>
                                        @endif
                                    </div>
                                    @if($app->proposal)
                                        <p class="text-xs text-slate-600 mt-1 italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                            "{{ $app->proposal }}"
                                        </p>
                                    @endif
                                    <span class="text-[11px] text-slate-400 mt-1 block">Applied: {{ $app->created_at ? $app->created_at->diffForHumans() : 'Recently' }}</span>
                                </div>
                            </div>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold self-start sm:self-auto {{ $app->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($app->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ ucfirst($app->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-6 text-center">No artisan proposals submitted for this job.</p>
                    @endforelse
                </div>
            </div>

            <!-- Job Reviews -->
            @if($serviceJob->reviews->isNotEmpty())
                <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
                    <h3 class="font-heading font-bold text-base text-slate-900 mb-4">Reviews & Feedback</h3>
                    <div class="divide-y divide-slate-100">
                        @foreach($serviceJob->reviews as $rev)
                            <div class="py-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-xs text-slate-800">By {{ $rev->client?->name ?? 'Client' }}</span>
                                    <span class="text-xs font-bold text-amber-500">{{ $rev->rating }} ★</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">"{{ $rev->comment }}"</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Col: Client Card & Status Management -->
        <div class="space-y-6">

            <!-- Status Control Card -->
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
                <h3 class="font-heading font-bold text-base text-slate-900 mb-4">Status & Dispute Override</h3>

                <form action="{{ route('admin.jobs.update-status', $serviceJob) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 font-heading">Current Status</label>
                        <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700">
                            <option value="open" {{ $serviceJob->status === 'open' ? 'selected' : '' }}>Open (Accepting Bids)</option>
                            <option value="in_progress" {{ $serviceJob->status === 'in_progress' ? 'selected' : '' }}>In Progress (Work ongoing)</option>
                            <option value="completed" {{ $serviceJob->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $serviceJob->status === 'cancelled' ? 'selected' : '' }}>Cancelled (Terminated)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-heading font-semibold text-xs rounded-xl transition">
                        Update Job Status
                    </button>
                </form>
            </div>

            <!-- Client Information Card -->
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
                <h3 class="font-heading font-bold text-base text-slate-900 mb-3">Client Information</h3>

                @if($serviceJob->client)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 font-heading font-bold flex items-center justify-center text-sm">
                            {{ strtoupper(substr($serviceJob->client->name, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('admin.users.show', $serviceJob->client) }}" class="font-heading font-bold text-sm text-slate-900 hover:text-artisan-700">
                                {{ $serviceJob->client->name }}
                            </a>
                            <p class="text-xs text-slate-400">{{ $serviceJob->client->email }}</p>
                            @if($serviceJob->client->phone)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $serviceJob->client->phone }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400">Client details not available.</p>
                @endif
            </div>

        </div>

    </div>

@endsection
