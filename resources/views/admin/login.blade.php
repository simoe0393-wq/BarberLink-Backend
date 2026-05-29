@extends('layouts.admin')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="flex-1 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-zinc-900/80 backdrop-blur-xl border border-yellow-500/20 rounded-2xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-yellow-500 tracking-wider mb-2">Barber<span class="text-white">Link</span></h1>
            <p class="text-zinc-400">تسجيل الدخول للإدارة</p>
        </div>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
            @csrf
            
            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-3 rounded-lg text-sm">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">رقم الهاتف</label>
                <input type="text" name="phone" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-colors" placeholder="أدخل رقم الهاتف" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">كلمة المرور</label>
                <input type="password" name="password" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-colors" placeholder="أدخل كلمة المرور" required>
            </div>

            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-zinc-950 font-bold py-3 rounded-lg transition-colors shadow-[0_0_15px_rgba(234,179,8,0.4)]">
                تسجيل الدخول
            </button>
        </form>
    </div>
</div>
@endsection
