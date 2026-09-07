<!DOCTYPE html>
<html lang="en" class="h-full bg-[#fbf7f4]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | ArtisanConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        artisan: {
                            500: '#f84b23',
                            600: '#c4501a',
                            700: '#a23900',
                            800: '#7f2b00',
                        }
                    },
                    fontFamily: {
                        sans: ['"Work Sans"', 'sans-serif'],
                        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-800">

    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-artisan-700 items-center justify-center text-white shadow-lg shadow-artisan-700/30 mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <h1 class="font-heading font-extrabold text-2xl text-slate-900 tracking-tight">Artisan<span class="text-artisan-700">Connect</span></h1>
            <p class="text-sm text-slate-500 mt-1">Platform Administrative Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 font-heading">Admin Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="admin@artisanconnect.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700 transition @error('email') border-red-400 bg-red-50/20 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 font-heading">Password</label>
                    </div>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-artisan-700/20 focus:border-artisan-700 transition">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-artisan-700 focus:ring-artisan-700 border-slate-300">
                        <span class="text-xs text-slate-600 font-medium">Remember me</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full mt-2 py-3 px-4 bg-artisan-700 hover:bg-artisan-800 active:scale-[0.99] text-white font-heading font-bold text-sm rounded-xl shadow-md shadow-artisan-700/25 transition-all duration-150">
                    Sign In to Admin
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <span class="text-xs text-slate-400">Default Seed Account: <code class="text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">admin@artisanconnect.com</code> / <code class="text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">password</code></span>
            </div>
        </div>
    </div>

</body>
</html>
