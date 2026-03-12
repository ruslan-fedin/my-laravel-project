<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>

    <script src="{{ asset('vendor/tailwind.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/inter.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-slate-100 min-h-screen flex flex-col">

    <div class="flex-1 flex items-center justify-center py-8">
        <div class="bg-white p-8 border border-slate-200 rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-user-lock text-xl text-white"></i>
                </div>
                <h2 class="text-2xl font-black uppercase tracking-tighter text-slate-900">Вход</h2>
                <p class="text-slate-500 text-xs mt-2">Система учёта сотрудников</p>
            </div>

            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 mb-4">
                <p class="text-rose-700 font-bold text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 mb-4">
                <ul class="text-rose-700 font-bold text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li><i class="fas fa-times-circle mr-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-slate-300 rounded-lg h-10 px-4 text-sm outline-none focus:border-slate-800 focus:ring-2 focus:ring-slate-200 transition @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Пароль</label>
                    <input type="password" name="password" required
                           class="w-full border border-slate-300 rounded-lg h-10 px-4 text-sm outline-none focus:border-slate-800 focus:ring-2 focus:ring-slate-200 transition @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="text-rose-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-slate-800 focus:ring-slate-500">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Запомнить</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-slate-800 transition">Забыли пароль?</a>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-slate-800 to-slate-900 text-white h-10 rounded-lg font-black text-[11px] uppercase hover:from-slate-900 hover:to-black transition shadow-lg shadow-slate-300">
                    Войти
                </button>
            </form>
        </div>
    </div>

    <footer class="w-full py-6 border-t border-slate-200 bg-white/80 backdrop-blur-sm mt-auto">
        @include('layouts.footer')
    </footer>

</body>
</html>
