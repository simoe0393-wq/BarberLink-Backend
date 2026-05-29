@extends('layouts.admin')

@section('title', 'سجل الحجوزات')

@section('content')
<div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-white">إدارة الحجوزات</h3>
        <form method="GET" class="flex flex-wrap gap-4">
            <input type="date" name="date" value="{{ request('date') }}" class="bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white text-sm focus:border-yellow-500 focus:outline-none">
            
            <select name="salon_id" class="bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white text-sm focus:border-yellow-500 focus:outline-none max-w-[150px]">
                <option value="">كل الصالونات</option>
                @foreach($salons as $salon)
                    <option value="{{ $salon->id }}" {{ request('salon_id') == $salon->id ? 'selected' : '' }}>{{ $salon->salon_name }}</option>
                @endforeach
            </select>

            <select name="status" class="bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white text-sm focus:border-yellow-500 focus:outline-none">
                <option value="">كل الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار (Pending)</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد (Confirmed)</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل (Completed)</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى (Cancelled)</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>مُعاد جدولته (Rescheduled)</option>
            </select>
            
            <button type="submit" class="bg-yellow-500 text-zinc-950 px-4 py-2 rounded-lg font-bold text-sm">تطبيق</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right text-sm text-zinc-300">
            <thead class="bg-zinc-950/50 text-zinc-400">
                <tr>
                    <th class="px-6 py-4 font-medium">العميل</th>
                    <th class="px-6 py-4 font-medium">الصالون</th>
                    <th class="px-6 py-4 font-medium">التاريخ</th>
                    <th class="px-6 py-4 font-medium">الوقت</th>
                    <th class="px-6 py-4 font-medium">نوع الخدمة</th>
                    <th class="px-6 py-4 font-medium">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($bookings as $booking)
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-white">{{ $booking->customer_name }}</td>
                    <td class="px-6 py-4 text-yellow-500">{{ $booking->salon_name }}</td>
                    <td class="px-6 py-4">{{ $booking->booking_date }}</td>
                    <td class="px-6 py-4 text-white font-bold">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</td>
                    <td class="px-6 py-4 text-zinc-400">{{ $booking->service_type }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                                'confirmed' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                'completed' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                'cancelled' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'rejected' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'rescheduled' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                            ];
                            $colorClass = $statusColors[$booking->status] ?? 'bg-zinc-800 text-zinc-400 border-zinc-700';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs border {{ $colorClass }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-zinc-500">لا توجد حجوزات مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
@endsection
