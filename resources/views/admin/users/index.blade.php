@extends('layouts.admin')

@section('title', 'Users & Artisans')
@section('subtitle', 'Manage registered clients, artisans, and KYC verification statuses')

@section('content')

    <!-- Top Stats & Filter Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 bg-white p-1.5 rounded-2xl border border-surface-border shadow-sm text-xs font-heading font-semibold">
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 rounded-xl transition {{ !request('role') && !request('verified') ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                All ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'artisan']) }}" 
               class="px-4 py-2 rounded-xl transition {{ request('role') === 'artisan' && !request('verified') ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                Artisans ({{ $counts['artisans'] }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'client']) }}" 
               class="px-4 py-2 rounded-xl transition {{ request('role') === 'client' ? 'bg-artisan-700 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                Clients ({{ $counts['clients'] }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'artisan', 'verified' => 0]) }}" 
               class="px-4 py-2 rounded-xl transition {{ request('verified') === '0' ? 'bg-amber-600 text-white' : 'text-amber-700 hover:bg-amber-50' }}">
                Pending KYC ({{ $counts['pending_verifications'] }})
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            @if(request('verified'))
                <input type="hidden" name="verified" value="{{ request('verified') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, trade..."
                       class="w-64 pl-9 pr-4 py-2 rounded-xl bg-white border border-surface-border text-xs focus:outline-none focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl border border-surface-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/70 border-b border-surface-border text-[11px] uppercase tracking-wider text-slate-500 font-heading">
                    <tr>
                        <th class="py-3.5 px-6 font-semibold">User</th>
                        <th class="py-3.5 px-6 font-semibold">Role & Trade</th>
                        <th class="py-3.5 px-6 font-semibold">Verification</th>
                        <th class="py-3.5 px-6 font-semibold">Activity</th>
                        <th class="py-3.5 px-6 font-semibold">Account Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- User Name & Contact -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-artisan-100 text-artisan-800 font-heading font-bold flex items-center justify-center text-sm shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $u) }}" class="font-heading font-bold text-slate-900 hover:text-artisan-700">
                                            {{ $u->name }}
                                        </a>
                                        <p class="text-xs text-slate-400">{{ $u->email }}</p>
                                        @if($u->phone)
                                            <p class="text-[11px] text-slate-400">{{ $u->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Role & Trade -->
                            <td class="py-4 px-6">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $u->role === 'artisan' ? 'bg-orange-100 text-orange-800' : ($u->role === 'client' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                    @if($u->category)
                                        <p class="text-xs text-slate-500 mt-1 font-medium">{{ $u->category }}</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Verification Status -->
                            <td class="py-4 px-6">
                                @if($u->role === 'artisan')
                                    @if($u->is_verified)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Verified Artisan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Pending Verification
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">N/A (Client)</span>
                                @endif
                            </td>

                            <!-- Activity Numbers -->
                            <td class="py-4 px-6 text-xs text-slate-600">
                                @if($u->role === 'client')
                                    <p><strong class="text-slate-800">{{ $u->jobs_count }}</strong> Jobs Posted</p>
                                @elseif($u->role === 'artisan')
                                    <p><strong class="text-slate-800">{{ $u->applications_count }}</strong> Bids / Applications</p>
                                    <p class="text-[11px] text-slate-400">{{ $u->reviews_received_count }} Reviews</p>
                                @else
                                    <p class="text-slate-400">System Staff</p>
                                @endif
                            </td>

                            <!-- Active Status -->
                            <td class="py-4 px-6">
                                @if($u->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-red-100 text-red-800">
                                        Suspended
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right space-x-1">
                                <a href="{{ route('admin.users.show', $u) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                    View
                                </a>

                                @if($u->role === 'artisan')
                                    <form action="{{ route('admin.users.verify', $u) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="is_verified" value="{{ $u->is_verified ? 0 : 1 }}">
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg {{ $u->is_verified ? 'bg-amber-100 hover:bg-amber-200 text-amber-800' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }} text-xs font-semibold transition">
                                            {{ $u->is_verified ? 'Revoke' : 'Verify' }}
                                        </button>
                                    </form>
                                @endif

                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-active', $u) }}" method="POST" class="inline-block" onsubmit="return confirm('Change active status for this user?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg {{ $u->is_active ? 'bg-red-50 hover:bg-red-100 text-red-700' : 'bg-slate-200 hover:bg-slate-300 text-slate-800' }} text-xs font-semibold transition">
                                            {{ $u->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                No users found matching the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 border-t border-surface-border">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection
