<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация в системе</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            padding: 0 120px; /* Твои стандартные отступы по бокам */
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

    <div class="auth-card w-full max-w-lg p-10">
        <header class="mb-8">
            <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-900">Регистрация</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1">Создание новой учетной записи</p>
        </header>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            {{-- ФИО Полностью --}}
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Фамилия Имя Отчество (полностью):</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="Иванов Иван Иванович"
                    class="w-full border border-slate-300 rounded h-11 px-4 text-sm font-semibold outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition">
                @error('name') <p class="text-rose-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Электронная почта (Email):</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-slate-300 rounded h-11 px-4 text-sm font-semibold outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition">
                @error('email') <p class="text-rose-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            {{-- Пароль --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Пароль:</label>
                    <input type="password" name="password" required
                        class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase ml-1 block mb-1.5">Повтор пароля:</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
            </div>
            @error('password') <p class="text-rose-600 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror

            {{-- Кнопка --}}
            <div class="pt-4">
                <button type="submit" class="w-full bg-slate-900 text-white h-12 rounded font-black text-[11px] uppercase hover:bg-black transition shadow-lg shadow-slate-200">
                    Зарегистрироваться
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <a href="{{ route('login') }}" class="text-[10px] font-black text-slate-400 uppercase hover:text-slate-900 transition tracking-widest">
                Уже есть аккаунт? Войти
            </a>
        </div>
    </div>

</body>
</html>
