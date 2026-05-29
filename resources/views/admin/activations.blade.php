@extends('layouts.admin')

@section('title', 'التفعيلات المعلقة')

@section('content')
<div x-data="{ imageModalOpen: false, currentImage: '' }">
    <div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white">طلبات التفعيل بانتظار المراجعة</h3>
            <span class="bg-yellow-500/10 text-yellow-500 px-3 py-1 rounded-full text-sm border border-yellow-500/20">
                {{ $pendingPayments->count() }} طلب
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm text-zinc-300">
                <thead class="bg-zinc-950/50 text-zinc-400">
                    <tr>
                        <th class="px-6 py-4 font-medium">الصالون</th>
                        <th class="px-6 py-4 font-medium">المالك</th>
                        <th class="px-6 py-4 font-medium">إثبات الدفع</th>
                        <th class="px-6 py-4 font-medium">التاريخ</th>
                        <th class="px-6 py-4 font-medium">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($pendingPayments as $payment)
                    <tr class="hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-white">{{ $payment->salon_name }}</td>
                        <td class="px-6 py-4">
                            <div>{{ $payment->owner_name }}</div>
                            <div class="text-xs text-zinc-500">{{ $payment->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="imageModalOpen = true; currentImage = '{{ asset('storage/' . $payment->proof_image) }}'" class="relative group block w-20 h-16 rounded-lg overflow-hidden border border-zinc-700 hover:border-yellow-500 transition-colors">
                                <img src="{{ asset('storage/' . $payment->proof_image) }}" class="w-full h-full object-cover" alt="Proof">
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                </div>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-400">{{ \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.activations.activate', $payment->payment_id) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white border border-green-500/20 rounded-lg transition-colors text-xs font-bold">
                                        تفعيل
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.activations.reject', $payment->payment_id) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/20 rounded-lg transition-colors text-xs font-bold">
                                        رفض
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-zinc-500">لا توجد طلبات تفعيل معلقة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Image Modal -->
    <div x-show="imageModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm" style="display: none;" x-transition>
        <button @click="imageModalOpen = false" class="absolute top-6 right-6 text-white hover:text-yellow-500 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <img :src="currentImage" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" @click.away="imageModalOpen = false">
    </div>
</div>
@endsection
