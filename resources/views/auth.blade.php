<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация в системе</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .auth-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-10">

    <div class="grid grid-cols-2 gap-10 w-full max-w-5xl">

        <div class="auth-card p-10">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-6 text-slate-900">Авторизация</h2>
            <form action="{{ url('/login') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Email:</label>
                    <input type="email" name="email" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Пароль:</label>
                    <input type="password" name="password" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <button type="submit" class="bg-slate-900 text-white h-11 rounded font-black text-[11px] uppercase hover:bg-black transition mt-2 shadow-lg">Войти в систему</button>
            </form>
        </div>

        <div class="auth-card p-10">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-6 text-slate-400">Зарегистрироваться</h2>
            <form action="{{ url('/register') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Полное имя (ФИО):</label>
                    <input type="text" name="name" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Email:</label>
                    <input type="email" name="email" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Придумайте пароль:</label>
                    <input type="password" name="password" required class="w-full border border-slate-300 rounded h-11 px-4 text-sm outline-none focus:border-slate-900 transition">
                </div>
                <button type="submit" class="border-2 border-slate-900 text-slate-900 h-11 rounded font-black text-[11px] uppercase hover:bg-slate-900 hover:text-white transition mt-2">Создать аккаунт</button>
            </form>
        </div>

    </div>

</body>
</html>
