@extends('layouts.admin')

@section('title', 'Reviews & Moderation')
@section('subtitle', 'Oversee user ratings, monitor feedback, and moderate reported reviews')

@section('content')

    <!-- Rating Distribution Overview Card -->
    <div class="bg-white rounded-2xl p-6 border border-surface-border shadow-sm mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <!-- Average Rating -->
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold font-heading text-slate-900">{{ number_format($averageRating, 1) }}</div>
                <div>
                    <div class="flex items-center text-amber-500 text-lg">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= round($averageRating) ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Based on {{ $totalReviews }} total reviews</p>
                </div>
            </div>

            <!-- Star Bars Breakdown -->
            <div class="flex-1 max-w-md space-y-1.5 text-xs">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php
                        $count = $ratingDistribution[$star] ?? 0;
                        $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-12 text-slate-600 font-medium">{{ $star }} Stars</span>
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-8 text-right text-slate-400 text-[11px]">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filter & Reviews Table -->
    <div class="bg-white rounded-2xl border border-surface-border shadow-sm overflow-hidden">
        <!-- Filter Header -->
        <div class="p-4 border-b border-surface-border flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-1 text-xs font-semibold">
                <a href="{{ route('admin.reviews.index') }}" class="px-3 py-1.5 rounded-lg {{ !request('rating') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Reviews
                </a>
                @foreach([5, 4, 3, 2, 1] as $star)
                    <a href="{{ route('admin.reviews.index', ['rating' => $star]) }}" class="px-3 py-1.5 rounded-lg {{ request('rating') == $star ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $star }} ★
                    </a>
                @endforeach
            </div>

            <!-- Search in comments -->
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex items-center gap-2">
                @if(request('rating'))
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search comments..."
                       class="px-3 py-1.5 rounded-xl border border-surface-border text-xs focus:ring-2 focus:ring-artisan-700/20">
                <button type="submit" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-200">
                    Filter
                </button>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/70 border-b border-surface-border text-[11px] uppercase tracking-wider text-slate-500 font-heading">
                    <tr>
                        <th class="py-3 px-6 font-semibold">Artisan & Client</th>
                        <th class="py-3 px-6 font-semibold">Rating & Review</th>
                        <th class="py-3 px-6 font-semibold">Associated Job</th>
                        <th class="py-3 px-6 font-semibold">Date</th>
                        <th class="py-3 px-6 font-semibold text-right">Moderation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Artisan & Client -->
                            <td class="py-4 px-6">
                                <p class="font-heading font-bold text-slate-900">
                                    {{ $rev->artisan?->name ?? 'Artisan' }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Reviewed by <span class="text-slate-700 font-medium">{{ $rev->client?->name ?? 'Client' }}</span>
                                </p>
                            </td>

                            <!-- Rating & Comment -->
                            <td class="py-4 px-6 max-w-xs">
                                <div class="flex items-center text-amber-500 text-xs font-bold mb-1">
                                    {{ $rev->rating }} ★
                                </div>
                                <p class="text-xs text-slate-700 italic leading-relaxed">
                                    "{{ $rev->comment }}"
                                </p>
                            </td>

                            <!-- Job -->
                            <td class="py-4 px-6">
                                @if($rev->serviceJob)
                                    <a href="{{ route('admin.jobs.show', $rev->serviceJob) }}" class="text-xs font-semibold text-artisan-700 hover:underline">
                                        {{ $rev->serviceJob->title }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">N/A</span>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="py-4 px-6 text-xs text-slate-400">
                                {{ $rev->created_at ? $rev->created_at->format('M d, Y') : 'N/A' }}
                            </td>

                            <!-- Moderation Action -->
                            <td class="py-4 px-6 text-right">
                                <form action="{{ route('admin.reviews.destroy', $rev) }}" method="POST" onsubmit="return confirm('Delete this review permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold transition" title="Delete review">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                No reviews found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-surface-border">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

@endsection
