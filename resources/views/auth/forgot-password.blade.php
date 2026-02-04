<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Восстановление доступа</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            padding: 0 120px; /* Твои стандартные отступы */
        }
        .auth-card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            border-radius: 8px;
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center">

    <div class="auth-card w-full max-w-md p-10">
        <header class="mb-6">
            <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-900">Забыли пароль?</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-2 leading-relaxed">
                Введите ваш Email, и мы отправим ссылку для установки нового пароля.
            </p>
        </header>

        {{-- Сообщение об успешной отправке (если настроена почта) --}}
        @if (session('status'))
            <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase rounded">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Email --}}
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Электронная почта:</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="example@mail.com"
                    class="w-full border border-slate-300 rounded h-11 px-4 text-sm font-semibold outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition">
                @error('email')
                    <p class="text-rose-600 text-[10px] font-bold mt-2 uppercase">{{ $message }}</p>
                @enderror
            </div>

            {{-- Кнопка --}}
            <button type="submit" class="w-full bg-slate-900 text-white h-12 rounded font-black text-[11px] uppercase hover:bg-black transition shadow-lg shadow-slate-200">
                Сбросить пароль
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-center">
            <a href="{{ route('login') }}" class="text-[10px] font-black text-slate-400 uppercase hover:text-slate-900 transition tracking-widest flex items-center gap-2">
                <i class="fas fa-arrow-left text-[8px]"></i> Вернуться ко входу
            </a>
        </div>
    </div>

</body>
</html>
