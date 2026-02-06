@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<style>
    /* Базовые настройки шрифтов и фона */
    .page-root {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
        width: 100%;
    }

    /* Адаптивный контейнер */
    .adaptive-container {
        width: 100%;
        margin: 0 auto;
        /* Отступы 120px для больших экранов */
        padding: 40px 120px;
    }

    /* Плавный переход для планшетов и мобильных (включая альбомную ориентацию) */
    @media (max-width: 1280px) {
        .adaptive-container { padding: 30px 40px; }
    }
    @media (max-width: 768px) {
        .adaptive-container { padding: 20px 12px; }
    }

    /* Дизайн карточек */
    .content-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Стили таблицы для десктопа */
    .main-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .main-table th {
        background: #f1f5f9;
        color: #64748b;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 20px 24px;
        border-bottom: 2px solid #e2e8f0;
    }
    .main-table td {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* МОБИЛЬНАЯ ВЕРСИЯ: ТРАНСФОРМАЦИЯ В КАРТОЧКИ */
    @media (max-width: 640px) {
        .main-table thead { display: none; } /* Прячем заголовки */

        .main-table tr {
            display: block;
            padding: 15px;
            border-bottom: 10px solid #f8fafc; /* Разделитель между карточками */
        }

        .main-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 5px;
            border: none;
            text-align: right;
        }

        /* Добавляем подписи через data-label */
        .main-table td::before {
            content: attr(data-label);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
            color: #94a3b8;
            margin-right: 10px;
            text-align: left;
        }

        /* Название должности на мобильных — акцент на всю ширину */
        .main-table td[data-label="Наименование"] {
            display: block;
            text-align: left;
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            margin: 8px 0;
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
        }
        .main-table td[data-label="Наименование"]::before { display: block; margin-bottom: 4px; }

        .main-table td[data-label="Действия"] { justify-content: center; gap: 25px; padding-top: 15px; }
        .main-table td[data-label="Действия"]::before { display: none; }
    }

    /* Кнопки и уведомления */
    .btn-primary {
        background: #0f172a;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.2s ease;
    }
    .btn-primary:hover { background: #000; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
</style>

<div class="page-root">
    <div class="adaptive-container">

        {{-- ШАПКА --}}
        <div class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 uppercase tracking-tighter leading-none">
                    Справочник должностей
                </h1>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-3">
                    Реестр штатных позиций организации
                </p>
            </div>

            <a href="{{ route('positions.create') }}" class="btn-primary flex items-center gap-3">
                <i class="fas fa-plus text-[10px]"></i> Добавить позицию
            </a>
        </div>

        {{-- СТАТУСЫ --}}
        @if(session('success'))
            <div class="mb-8 p-5 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl flex items-center shadow-sm">
                <i class="fas fa-check-circle text-emerald-500 mr-4 text-xl"></i>
                <span class="text-emerald-900 font-black text-[11px] uppercase tracking-wider">{{ session('success') }}</span>
            </div>
        @endif

        {{-- ОСНОВНОЙ КОНТЕНТ --}}
        <div class="content-card">
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="text-center w-24">ID</th>
                        <th class="text-left">Наименование должности</th>
                        <th class="text-right px-10 w-64">Управление</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($positions as $pos)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="№" class="text-center text-slate-400 text-xs">
                            #{{ $loop->iteration }}
                        </td>

                        <td data-label="Наименование" class="font-black text-slate-800 uppercase tracking-tight">
                            {{ $pos->name }}
                        </td>

                        <td data-label="Действия" class="text-right px-10">
                            <div class="flex justify-end items-center gap-6">
                                <a href="{{ route('positions.edit', $pos) }}" class="text-slate-400 hover:text-slate-900 transition" title="Редактировать">
                                    <i class="fas fa-pen-nib fa-lg"></i>
                                </a>

                                <form action="{{ route('positions.destroy', $pos) }}" method="POST" onsubmit="return confirm('УДАЛИТЬ ДОЛЖНОСТЬ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-rose-600 transition">
                                        <i class="fas fa-trash-alt fa-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-24 text-center">
                            <div class="text-slate-300 font-black uppercase text-xs tracking-widest">Список пуст</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ПАГИНАЦИЯ --}}
        @if(method_exists($positions, 'links'))
            <div class="mt-10">
                {{ $positions->links() }}
            </div>
        @endif

        {{-- ФУТЕР И ИНФО --}}
        <div class="mt-12 flex flex-col md:flex-row justify-between items-center border-t border-slate-200 pt-8 gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-black text-[10px]">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="text-[10px] font-black uppercase text-slate-400">
                    Оператор: <span class="text-slate-900 ml-1">{{ Auth::user()->name }}</span>
                </div>
            </div>

            @include('layouts.footer')
        </div>

    </div>
</div>
@endsection
