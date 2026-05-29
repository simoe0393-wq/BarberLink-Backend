@extends('layouts.admin')

@section('title', 'إدارة الصالونات')

@section('content')
<div class="bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-white">قائمة الصالونات</h3>
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الصالون أو المالك..." class="bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white text-sm focus:border-yellow-500 focus:outline-none">
            <select name="status" class="bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white text-sm focus:border-yellow-500 focus:outline-none">
                <option value="">كل الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط (Active)</option>
                <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>معلق (Pending)</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف (Suspended)</option>
            </select>
            <button type="submit" class="bg-yellow-500 text-zinc-950 px-4 py-2 rounded-lg font-bold text-sm">فلترة</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right text-sm text-zinc-300">
            <thead class="bg-zinc-950/50 text-zinc-400">
                <tr>
                    <th class="px-6 py-4 font-medium">الصالون</th>
                    <th class="px-6 py-4 font-medium">المالك</th>
                    <th class="px-6 py-4 font-medium">QR Code</th>
                    <th class="px-6 py-4 font-medium">الحالة</th>
                    <th class="px-6 py-4 font-medium">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($salons as $salon)
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-white">{{ $salon->salon_name }}</td>
                    <td class="px-6 py-4">
                        <div>{{ $salon->owner_name }}</div>
                        <div class="text-xs text-zinc-500">{{ $salon->phone }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($salon->qr_image_path)
                        <a href="{{ asset('storage/' . $salon->qr_image_path) }}" target="_blank" class="text-yellow-500 hover:underline">عرض الـ QR</a>
                        @else
                        <span class="text-zinc-500">لا يوجد</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($salon->status == 'active')
                            <span class="bg-green-500/10 text-green-500 px-2 py-1 rounded-full text-xs border border-green-500/20">Active</span>
                        @elseif($salon->status == 'pending_review')
                            <span class="bg-yellow-500/10 text-yellow-500 px-2 py-1 rounded-full text-xs border border-yellow-500/20">Pending</span>
                        @elseif($salon->status == 'suspended')
                            <span class="bg-orange-500/10 text-orange-500 px-2 py-1 rounded-full text-xs border border-orange-500/20">Suspended</span>
                        @else
                            <span class="bg-red-500/10 text-red-500 px-2 py-1 rounded-full text-xs border border-red-500/20">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            @if($salon->status == 'active')
                            <form method="POST" action="{{ route('admin.salons.suspend', $salon->id) }}">
                                @csrf
                                <button class="text-orange-500 hover:underline text-xs">إيقاف</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.salons.activate', $salon->id) }}">
                                @csrf
                                <button class="text-green-500 hover:underline text-xs font-bold border border-green-500/50 px-2 py-1 rounded">تفعيل مباشر</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.salons.delete', $salon->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصالون؟');">
                                @csrf
                                <button class="text-red-500 hover:underline text-xs">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-zinc-500">لا توجد صالونات مسجلة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $salons->links() }}</div>
</div>
@endsection
