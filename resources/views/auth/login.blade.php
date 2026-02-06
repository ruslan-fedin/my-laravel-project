<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6">

    {{-- ОСНОВНОЙ КОНТЕНТ (ФОРМА) --}}
    <div class="flex-grow flex items-center justify-center w-full">
        <div class="bg-white p-10 border border-slate-200 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-3xl font-black uppercase tracking-tighter mb-8 text-slate-900">Вход</h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Email:</label>
                    <input type="email" name="email" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Пароль:</label>
                    <input type="password" name="password" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white h-12 rounded font-black text-[11px] uppercase hover:bg-black transition shadow-lg">Войти</button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end text-[10px] font-bold uppercase tracking-widest text-slate-400">
                <a href="{{ route('password.request') }}" class="hover:text-rose-600">Забыли пароль?</a>
            </div>
        </div>
    </div>

       <div class="mt-8">
@include('layouts.footer')
    </div>
</div> {{-- Закрытие index-page-wrapper --}}


</body>
</html>
