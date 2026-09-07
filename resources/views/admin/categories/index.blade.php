@extends('layouts.admin')

@section('title', 'Service Categories')
@section('subtitle', 'Configure trade classifications, icons, and theme accents')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Category List & Metrics -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm overflow-hidden">
                <div class="p-5 border-b border-surface-border flex items-center justify-between">
                    <h3 class="font-heading font-bold text-base text-slate-900">Configured Trades ({{ $categories->count() }})</h3>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm shadow-sm" style="background-color: {{ $cat->color_hex ?? '#a23900' }}20; color: {{ $cat->color_hex ?? '#a23900' }};">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-heading font-bold text-sm text-slate-900">{{ $cat->name }}</h4>
                                    <div class="flex items-center gap-3 text-xs text-slate-400 mt-0.5">
                                        <span>Icon: <code class="bg-slate-100 px-1 py-0.5 rounded text-[11px] text-slate-600">{{ $cat->icon_name }}</code></span>
                                        <span class="flex items-center gap-1">
                                            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ $cat->color_hex ?? '#a23900' }};"></span>
                                            {{ $cat->color_hex ?? '#a23900' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right text-xs">
                                    <p class="font-semibold text-slate-800">{{ $cat->jobs_count }} Jobs</p>
                                    <p class="text-slate-400">{{ $cat->artisans_count }} Artisans</p>
                                </div>

                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Remove category {{ $cat->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete category">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-8 text-center">No categories configured yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Add Category Form -->
        <div>
            <div class="bg-white rounded-2xl border border-surface-border shadow-sm p-6 sticky top-24">
                <h3 class="font-heading font-bold text-base text-slate-900 mb-4">Add New Trade Category</h3>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-600 mb-1 font-heading">Category Name</label>
                        <input type="text" id="name" name="name" required placeholder="e.g. Masonry, Roofing"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700">
                    </div>

                    <div>
                        <label for="icon_name" class="block text-xs font-semibold text-slate-600 mb-1 font-heading">Flutter Icon Identifier</label>
                        <input type="text" id="icon_name" name="icon_name" required placeholder="e.g. handyman, plumbing, electrical_services"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700">
                    </div>

                    <div>
                        <label for="color_hex" class="block text-xs font-semibold text-slate-600 mb-1 font-heading">Color Hex (#RRGGBB)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="color_picker" value="#a23900" onchange="document.getElementById('color_hex').value = this.value"
                                   class="w-8 h-8 rounded-lg cursor-pointer border-0">
                            <input type="text" id="color_hex" name="color_hex" value="#a23900" required placeholder="#a23900"
                                   class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-artisan-700 hover:bg-artisan-800 text-white font-heading font-semibold text-xs rounded-xl shadow-sm transition">
                        Create Category
                    </button>
                </form>
            </div>
        </div>

    </div>

@endsection
