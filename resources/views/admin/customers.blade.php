@extends('layouts.admin')

@section('title', 'إدارة العملاء')

@section('content')
<div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-white">قائمة العملاء (Customers)</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right text-sm text-zinc-300">
            <thead class="bg-zinc-950/50 text-zinc-400">
                <tr>
                    <th class="px-6 py-4 font-medium">اسم العميل</th>
                    <th class="px-6 py-4 font-medium">رقم الهاتف</th>
                    <th class="px-6 py-4 font-medium">الصالون المرتبط به</th>
                    <th class="px-6 py-4 font-medium text-center">عدد الحجوزات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($customers as $customer)
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-white flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-yellow-500 font-bold">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        {{ $customer->name }}
                    </td>
                    <td class="px-6 py-4 text-zinc-400">{{ $customer->phone }}</td>
                    <td class="px-6 py-4">
                        @if($customer->linked_salon_id)
                            <span class="text-yellow-500 bg-yellow-500/10 px-3 py-1 rounded-full text-xs border border-yellow-500/20">
                                {{ $customer->salon_name }}
                            </span>
                        @else
                            <span class="text-zinc-500">غير مرتبط</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-white">
                        {{ $customer->bookings_count }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-zinc-500">لا يوجد عملاء مسجلين.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
</div>
@endsection
