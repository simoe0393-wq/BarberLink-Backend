@extends('layouts.admin')

@section('title', 'إدارة المدفوعات')

@section('content')
<div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-white">سجل المدفوعات</h3>
        <form method="GET" class="flex gap-4">
            <select name="status" class="bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white text-sm focus:border-yellow-500 focus:outline-none">
                <option value="">كل الحالات</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبول (Approved)</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة (Pending)</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
            </select>
            <button type="submit" class="bg-yellow-500 text-zinc-950 px-4 py-2 rounded-lg font-bold text-sm">فلترة</button>
        </form>
    </div>

    <div class="overflow-x-auto" x-data="{ imageModalOpen: false, currentImage: '' }">
        <table class="w-full text-right text-sm text-zinc-300">
            <thead class="bg-zinc-950/50 text-zinc-400">
                <tr>
                    <th class="px-6 py-4 font-medium">الصالون</th>
                    <th class="px-6 py-4 font-medium">المالك</th>
                    <th class="px-6 py-4 font-medium">المبلغ</th>
                    <th class="px-6 py-4 font-medium">صورة الإثبات</th>
                    <th class="px-6 py-4 font-medium">الحالة</th>
                    <th class="px-6 py-4 font-medium">تاريخ الدفع</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($payments as $payment)
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-white">{{ $payment->salon_name }}</td>
                    <td class="px-6 py-4">{{ $payment->owner_name }}</td>
                    <td class="px-6 py-4 font-bold text-yellow-500">{{ $payment->amount }} MAD</td>
                    <td class="px-6 py-4">
                        @if($payment->proof_image)
                        <button @click="imageModalOpen = true; currentImage = '{{ asset('storage/' . $payment->proof_image) }}'" class="w-16 h-10 rounded-md overflow-hidden border border-zinc-700 hover:border-yellow-500 transition-colors">
                            <img src="{{ asset('storage/' . $payment->proof_image) }}" class="w-full h-full object-cover">
                        </button>
                        @else
                        <span class="text-zinc-500">لا يوجد</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($payment->payment_status == 'approved')
                            <span class="bg-green-500/10 text-green-500 px-2 py-1 rounded-full text-xs border border-green-500/20">Approved</span>
                        @elseif($payment->payment_status == 'pending')
                            <span class="bg-yellow-500/10 text-yellow-500 px-2 py-1 rounded-full text-xs border border-yellow-500/20">Pending</span>
                        @else
                            <span class="bg-red-500/10 text-red-500 px-2 py-1 rounded-full text-xs border border-red-500/20">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-zinc-400">{{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-zinc-500">لا توجد مدفوعات مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Image Modal -->
        <div x-show="imageModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm" style="display: none;" x-transition>
            <button @click="imageModalOpen = false" class="absolute top-6 right-6 text-white hover:text-yellow-500 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <img :src="currentImage" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" @click.away="imageModalOpen = false">
        </div>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
