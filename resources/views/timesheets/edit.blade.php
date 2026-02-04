<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать табель</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('timesheets.index') }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Отмена
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <div class="bg-gray-100 border-b border-gray-200 px-6 py-4">
                <h1 class="text-xl font-bold text-gray-800 uppercase tracking-tight">Редактирование параметров</h1>
            </div>

            <form action="{{ route('timesheets.update', $timesheet) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Дата начала</label>
                        <input type="date" name="start_date" value="{{ $timesheet->start_date }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm p-2 border">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Дата окончания</label>
                        <input type="date" name="end_date" value="{{ $timesheet->end_date }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm p-2 border">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded shadow hover:bg-orange-600 transition font-bold text-xs uppercase tracking-widest">
                        Сохранить изменения <i class="fas fa-save ml-2"></i>
                    </button>
                </div>
            </form>
        </div>


    {{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА ЧЕРЕЗ BLADE ШАБЛОН --}}
    <div class="mt-8">
        @include('partials.footer')
    </div>

</div> {{-- ЗАКРЫВАЮЩИЙ ТЕГ ОСНОВНОГО КОНТЕЙНЕРА (если он был открыт в начале страницы или в макете) --}}

</body>
</html>
