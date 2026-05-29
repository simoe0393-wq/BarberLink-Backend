@extends('layouts.admin')

@section('title', 'الإعدادات العامة')

@section('content')
<div class="max-w-3xl mx-auto bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-2xl overflow-hidden p-8 shadow-xl">
    <div class="mb-8 border-b border-zinc-800 pb-4">
        <h3 class="text-xl font-bold text-yellow-500">إعدادات النظام وطرق الدفع</h3>
        <p class="text-sm text-zinc-400 mt-1">قم بتحديث بيانات التحويل البنكي التي ستظهر لأصحاب الصالونات في تطبيق Flutter.</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">اسم المستفيد (Account Holder)</label>
                <input type="text" name="bank_account_name" value="{{ $settings['bank_account_name'] ?? 'BarberLink LLC' }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">رقم الحساب البنكي / RIB</label>
                <input type="text" name="bank_rib" value="{{ $settings['bank_rib'] ?? '007 123 4567890123456789 10' }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" required dir="ltr">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">مبلغ التفعيل (MAD)</label>
            <input type="number" name="activation_fee" value="{{ $settings['activation_fee'] ?? '100' }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors max-w-xs" required>
            <p class="text-xs text-zinc-500 mt-1">يُدفع شهرياً أو سنوياً لتمكين الصالون من استخدام النظام.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">رسالة الدفع (تظهر في التطبيق)</label>
            <textarea name="payment_message" rows="3" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" required>{{ $settings['payment_message'] ?? 'الرجاء تحويل مبلغ 100 درهم مغربي إلى الحساب أعلاه ثم رفع إيصال الدفع لتفعيل صالونك في BarberLink.' }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300 mb-2">بيانات التواصل (دعم فني)</label>
            <input type="text" name="contact_email" value="{{ $settings['contact_email'] ?? 'support@barberlink.com' }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 transition-colors" required>
        </div>

        <div class="pt-4 border-t border-zinc-800 flex justify-end">
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-zinc-950 font-bold py-3 px-8 rounded-lg transition-colors shadow-lg">
                حفظ الإعدادات
            </button>
        </div>
    </form>
</div>
@endsection
