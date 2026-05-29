@extends('layouts.admin')

@section('title', 'مركز الإشعارات')

@section('content')
<div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-white">أحدث الإشعارات في النظام</h3>
        <p class="text-sm text-zinc-400">يعرض كل التنبيهات التي تم إرسالها للزبائن أو أصحاب الصالونات.</p>
    </div>

    <div class="space-y-4">
        @forelse($notifications as $notification)
        <div class="bg-zinc-950/50 border border-zinc-800 rounded-lg p-4 flex gap-4 hover:border-yellow-500/30 transition-colors">
            <div class="mt-1">
                @if($notification->role == 'customer')
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-full bg-yellow-500/10 text-yellow-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="font-bold text-white">إلى: {{ $notification->name }} <span class="text-xs text-zinc-500 font-normal ml-2">({{ ucfirst($notification->role) }})</span></h4>
                    <span class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-zinc-300">{{ $notification->message }}</p>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-zinc-500 border border-dashed border-zinc-800 rounded-lg">لا توجد إشعارات حالياً.</div>
        @endforelse
    </div>
    
    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
@endsection
