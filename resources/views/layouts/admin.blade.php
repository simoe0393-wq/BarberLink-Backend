<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberLink Admin - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-white min-h-screen flex">

    @auth
    <!-- Sidebar -->
    <aside class="w-64 bg-zinc-900/80 backdrop-blur-xl border-l border-yellow-500/20 fixed h-full z-20 flex flex-col transition-transform">
        <div class="p-6 border-b border-yellow-500/20 text-center">
            <h1 class="text-2xl font-bold text-yellow-500 tracking-wider">Barber<span class="text-white">Link</span></h1>
            <p class="text-xs text-zinc-400 mt-1">Luxury Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                الرئيسية
            </a>
            
            <a href="{{ route('admin.activations') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.activations') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                التفعيلات المعلقة
                @php $pendingCount = \App\Models\Payment::where('payment_status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                <span class="mr-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.salons') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.salons') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                الصالونات
            </a>
            
            <a href="{{ route('admin.payments') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.payments') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                المدفوعات
            </a>
            
            <a href="{{ route('admin.customers') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.customers') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                العملاء
            </a>
            
            <a href="{{ route('admin.bookings') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.bookings') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                الحجوزات
            </a>
            
            <a href="{{ route('admin.notifications') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.notifications') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                الإشعارات
            </a>
            
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20' : 'text-zinc-300 hover:bg-zinc-800' }} transition-all">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                الإعدادات
            </a>
        </nav>

        <div class="p-4 border-t border-yellow-500/20">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-500/10 text-red-500 rounded-lg hover:bg-red-500/20 transition-all">
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 mr-64 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="h-20 bg-zinc-900/50 backdrop-blur-md border-b border-yellow-500/10 flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-bold text-white">@yield('title', 'لوحة القيادة')</h2>
                <p class="text-sm text-zinc-400">{{ \Carbon\Carbon::now()->format('l, j F Y') }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <button class="relative p-2 text-zinc-300 hover:text-yellow-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-1 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="flex items-center gap-3 pl-4 border-l border-zinc-700">
                    <div class="text-left">
                        <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-yellow-500">مدير النظام</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-zinc-950 font-bold text-lg">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 p-8">
            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </div>
    </main>
    @else
        @yield('content')
    @endauth

</body>
</html>
