@extends('layouts.admin')

@section('title', 'لوحة القيادة')

@section('content')
<div class="space-y-8">
    
    <!-- Today Overview -->
    <div>
        <h3 class="text-lg font-bold text-yellow-500 mb-4">نظرة عامة اليوم</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-yellow-500/10 rounded-2xl p-6 shadow-lg hover:border-yellow-500/30 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-zinc-400 font-medium">حجوزات اليوم</h4>
                    <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
                <h2 class="text-4xl font-bold text-white">{{ $todayBookings }}</h2>
            </div>

            <div class="bg-zinc-900/80 backdrop-blur-xl border border-yellow-500/10 rounded-2xl p-6 shadow-lg hover:border-yellow-500/30 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-zinc-400 font-medium">تفعيلات اليوم</h4>
                    <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h2 class="text-4xl font-bold text-white">{{ $todayActivations }}</h2>
            </div>

            <div class="bg-zinc-900/80 backdrop-blur-xl border border-yellow-500/10 rounded-2xl p-6 shadow-lg hover:border-yellow-500/30 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-zinc-400 font-medium">أرباح اليوم (MAD)</h4>
                    <div class="p-2 bg-yellow-500/10 rounded-lg text-yellow-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h2 class="text-4xl font-bold text-yellow-500">{{ number_format($todayEarnings, 2) }}</h2>
            </div>

        </div>
    </div>

    <!-- Overall Statistics -->
    <div>
        <h3 class="text-lg font-bold text-white mb-4">الإحصائيات العامة</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl p-6">
                <p class="text-sm text-zinc-400 mb-1">إجمالي الصالونات</p>
                <h3 class="text-2xl font-bold text-white">{{ $totalSalons }}</h3>
            </div>
            
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl p-6">
                <p class="text-sm text-zinc-400 mb-1">الصالونات المفعلة</p>
                <h3 class="text-2xl font-bold text-white">{{ $activeSalons }}</h3>
            </div>
            
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl p-6">
                <p class="text-sm text-zinc-400 mb-1">الطلبات المعلقة</p>
                <h3 class="text-2xl font-bold text-white">{{ $pendingSalons }}</h3>
            </div>
            
            <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl p-6">
                <p class="text-sm text-zinc-400 mb-1">إجمالي الأرباح</p>
                <h3 class="text-2xl font-bold text-yellow-500">{{ number_format($totalEarnings, 2) }} MAD</h3>
            </div>
            
        </div>
    </div>

</div>
@endsection
