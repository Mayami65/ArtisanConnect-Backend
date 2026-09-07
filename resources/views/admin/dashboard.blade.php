@extends('layouts.admin')

@section('title', 'Executive Dashboard')
@section('subtitle', 'Platform overview, marketplace metrics, and verification queue')

@section('content')

    <!-- Top KPI Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Users Card -->
        <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-heading">Total Users</span>
                <span class="w-10 h-10 rounded-xl bg-artisan-50 text-artisan-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold font-heading text-slate-900">{{ number_format($kpis['total_users']) }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-500">
                    <span class="text-artisan-700 font-medium">{{ $kpis['total_artisans'] }} Artisans</span>
                    <span>•</span>
                    <span class="text-slate-600 font-medium">{{ $kpis['total_clients'] }} Clients</span>
                </div>
            </div>
        </div>

        <!-- Total Service Jobs Card -->
        <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-heading">Service Jobs</span>
                <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold font-heading text-slate-900">{{ number_format($kpis['total_jobs']) }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-500">
                    <span class="text-emerald-600 font-medium">{{ $kpis['completed_jobs'] }} Completed</span>
                    <span>•</span>
                    <span class="text-amber-600 font-medium">{{ $kpis['open_jobs'] }} Open</span>
                </div>
            </div>
        </div>

        <!-- Total GMV Volume Card -->
        <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-heading">Marketplace Volume</span>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold font-heading text-slate-900">GHS {{ number_format($kpis['total_budget_volume'], 2) }}</p>
                <div class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-500">
                    <span>Cumulative Job Budgets</span>
                </div>
            </div>
        </div>

        <!-- Verification Queue Card -->
        <div class="bg-white rounded-2xl p-5 border border-surface-border shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-heading">Verification Queue</span>
                <span class="w-10 h-10 rounded-xl {{ $kpis['pending_verifications'] > 0 ? 'bg-amber-50 text-amber-600 animate-pulse' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-bold font-heading {{ $kpis['pending_verifications'] > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                    {{ $kpis['pending_verifications'] }}
                </p>
                <div class="flex items-center justify-between mt-1.5 text-xs">
                    <span class="text-slate-500">{{ $kpis['verified_artisan_rate'] }}% Artisans Verified</span>
                    @if($kpis['pending_verifications'] > 0)
                        <a href="{{ route('admin.users.index', ['role' => 'artisan', 'verified' => 0]) }}" class="text-artisan-700 font-medium hover:underline">Review &rarr;</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Growth Trend -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-surface-border shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Marketplace Activity & Job Trends</h2>
                    <p class="text-xs text-slate-400">Monthly job creations over the past 6 months</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white p-6 rounded-2xl border border-surface-border shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Jobs by Category</h2>
                    <p class="text-xs text-slate-400">Distribution across trades</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-artisan-700 hover:underline">View all</a>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryDistChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Two-Column Feed: Pending Verifications & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Verifications Action List -->
        <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Pending Artisan Verifications</h2>
                    <p class="text-xs text-slate-400">Artisans awaiting identity & skill validation</p>
                </div>
                <a href="{{ route('admin.users.index', ['role' => 'artisan', 'verified' => 0]) }}" class="text-xs font-semibold text-artisan-700 hover:underline">
                    View all ({{ $kpis['pending_verifications'] }})
                </a>
            </div>

            @if($pendingArtisans->isEmpty())
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">All Artisans Verified!</p>
                    <p class="text-xs text-slate-400 mt-0.5">There are no pending artisan approvals at this moment.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($pendingArtisans as $artisan)
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-artisan-100 text-artisan-800 font-heading font-bold flex items-center justify-center text-sm">
                                    {{ strtoupper(substr($artisan->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $artisan) }}" class="text-sm font-semibold text-slate-900 hover:text-artisan-700">
                                        {{ $artisan->name }}
                                    </a>
                                    <div class="text-xs text-slate-400 flex items-center gap-2">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">{{ $artisan->category ?? 'General' }}</span>
                                        <span>GHS {{ number_format($artisan->hourly_rate ?? 0, 2) }}/hr</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.users.verify', $artisan) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="is_verified" value="1">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                                        Approve
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.show', $artisan) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                    Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Recent Platform Activity Feed -->
        <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-heading font-bold text-base text-slate-900">Recent Platform Activity</h2>
                    <p class="text-xs text-slate-400">Live events across jobs, users, and ratings</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start gap-3.5">
                        <div class="mt-0.5 w-2 h-2 rounded-full bg-artisan-700 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $activity['title'] }}</p>
                                <span class="text-[11px] text-slate-400 shrink-0">{{ $activity['time'] }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $activity['subtitle'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">No platform activity recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Monthly Trends Chart
        const trendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
        const trendsData = @json($trends);

        new Chart(trendsCtx, {
            type: 'bar',
            data: {
                labels: trendsData.labels,
                datasets: [
                    {
                        label: 'Jobs Posted',
                        data: trendsData.jobs,
                        backgroundColor: '#a23900',
                        borderRadius: 8,
                    },
                    {
                        label: 'Users Registered',
                        data: trendsData.users,
                        backgroundColor: '#ffb599',
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { family: 'Work Sans', size: 12 } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // 2. Category Distribution Chart
        const catCtx = document.getElementById('categoryDistChart').getContext('2d');
        const catData = @json($categoryDist);

        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catData.labels,
                datasets: [{
                    data: catData.data,
                    backgroundColor: catData.colors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { family: 'Work Sans', size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endpush
