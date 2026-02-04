@extends('layouts.app')

@section('content')
{{-- Относительное позиционирование для всего контента --}}
<div class="relative min-h-[85vh] flex items-center justify-center px-4">

    {{-- Кнопка НАЗАД: Справа вверху (ниже меню), углы чуть скруглены --}}
    <div class="absolute top-0 right-0 mt-2 mr-2">
        <a href="{{ route('timesheets.index') }}"
           class="bg-blue-600 text-white px-5 py-2 rounded text-[10px] font-bold uppercase tracking-widest shadow-sm hover:bg-blue-700 transition inline-block">
            Назад в архив
        </a>
    </div>

    {{-- Карточка создания табеля --}}
    <div class="w-full max-w-md bg-white border border-gray-300 rounded shadow-sm overflow-hidden">
        {{-- Заголовок с серым фоном --}}
        <div class="bg-gray-100 border-b border-gray-300 px-4 py-3">
            <h2 class="text-[10px] font-bold uppercase text-gray-700 tracking-widest text-center">Параметры нового отчетного периода</h2>
        </div>

        <form action="{{ route('timesheets.store') }}" method="POST" class="p-8">
            @csrf

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-2 tracking-tight">Дата начала:</label>
                    <input type="date" name="start_date" required
                           value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-2 tracking-tight">Дата окончания:</label>
                    <input type="date" name="end_date" required
                           value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded text-[10px] font-bold uppercase hover:bg-blue-700 transition shadow-sm tracking-widest">
                    СФОРМИРОВАТЬ ТАБЕЛЬ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
