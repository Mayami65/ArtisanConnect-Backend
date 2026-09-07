@extends('layouts.admin')

@section('title', 'Service Jobs')
@section('subtitle', 'Oversee marketplace jobs, contracts, and dispute resolutions')

@section('content')

    <!-- Top Status Tabs & Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex flex-wrap items-center gap-2 bg-white p-1.5 rounded-2xl border border-surface-border shadow-sm text-xs font-heading font-semibold">
            <a href="{{ route('admin.jobs.index') }}" 
               class="px-3.5 py-2 rounded-xl transition {{ !request('status') ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                All ({{ $statusCounts['total'] }})
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'open']) }}" 
               class="px-3.5 py-2 rounded-xl transition {{ request('status') === 'open' ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                Open ({{ $statusCounts['open'] }})
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'in_progress']) }}" 
               class="px-3.5 py-2 rounded-xl transition {{ request('status') === 'in_progress' ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                In Progress ({{ $statusCounts['in_progress'] }})
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'completed']) }}" 
               class="px-3.5 py-2 rounded-xl transition {{ request('status') === 'completed' ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                Completed ({{ $statusCounts['completed'] }})
            </a>
            <a href="{{ route('admin.jobs.index', ['status' => 'cancelled']) }}" 
               class="px-3.5 py-2 rounded-xl transition {{ request('status') === 'cancelled' ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                Cancelled ({{ $statusCounts['cancelled'] }})
            </a>
        </div>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('admin.jobs.index') }}" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <select name="category" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white border border-surface-border text-xs focus:outline-none focus:ring-2 focus:ring-artisan-700/20">
                <option value="all">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or location..."
                       class="w-56 pl-9 pr-4 py-2 rounded-xl bg-white border border-surface-border text-xs focus:outline-none focus:ring-2 focus:ring-artisan-700/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <button type="submit" class="px-3 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Jobs Table Card -->
    <div class="bg-white rounded-2xl border border-surface-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/70 border-b border-surface-border text-[11px] uppercase tracking-wider text-slate-500 font-heading">
                    <tr>
                        <th class="py-3.5 px-6 font-semibold">Job Title & Client</th>
                        <th class="py-3.5 px-6 font-semibold">Category</th>
                        <th class="py-3.5 px-6 font-semibold">Budget</th>
                        <th class="py-3.5 px-6 font-semibold">Applications</th>
                        <th class="py-3.5 px-6 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Job Title & Client -->
                            <td class="py-4 px-6">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="font-heading font-bold text-slate-900 hover:text-artisan-700 block">
                                    {{ $job->title }}
                                </a>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Client: <span class="text-slate-700 font-medium">{{ $job->client?->name ?? 'Unknown' }}</span>
                                    @if($job->location) • <span class="text-slate-500">{{ $job->location }}</span> @endif
                                </p>
                            </td>

                            <!-- Category -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $job->category }}
                                </span>
                            </td>

                            <!-- Budget -->
                            <td class="py-4 px-6 font-heading font-bold text-slate-900">
                                GHS {{ number_format($job->budget, 2) }}
                            </td>

                            <!-- Applications -->
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <span class="font-semibold text-slate-800">{{ $job->applications_count }}</span> proposals
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                @php
                                    $badgeStyle = match($job->status) {
                                        'open' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeStyle }}">
                                    {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right space-x-1">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                No service jobs found matching the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
            <div class="p-4 border-t border-surface-border">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>

@endsection
