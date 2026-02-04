@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Справочник должностей</h1>
        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Реестр штатных позиций организации</p>
    </div>

    <a href="{{ route('positions.create') }}" class="bg-slate-900 text-white px-6 py-3 rounded text-[11px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl flex items-center gap-2">
        <i class="fas fa-plus text-[10px]"></i> Добавить должность
    </a>
</div>

{{-- БЛОК УВЕДОМЛЕНИЙ --}}
@if(session('error'))
    <div class="mb-6 p-4 bg-rose-50 border-2 border-rose-200 rounded-lg flex items-center shadow-sm">
        <i class="fas fa-exclamation-triangle text-rose-600 mr-4 fa-lg"></i>
        <div class="text-rose-900 font-black text-[11px] uppercase tracking-wider">
            ОШИБКА: {{ session('error') }}
        </div>
    </div>
@endif

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-lg flex items-center shadow-sm">
        <i class="fas fa-check-circle text-emerald-600 mr-4 fa-lg"></i>
        <div class="text-emerald-900 font-black text-[11px] uppercase tracking-wider">
            УСПЕШНО: {{ session('success') }}
        </div>
    </div>
@endif

{{-- Таблица должностей --}}
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-slate-200">
    <table class="min-w-full border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <th class="px-6 py-5 text-center w-20 border-r">№</th>
                <th class="px-6 py-5 text-left border-r">Наименование должности</th>
                <th class="px-6 py-5 text-right px-8">Управление</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($positions as $pos)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-5 font-black text-slate-700 uppercase tracking-tight">
                    {{ $pos->name }}
                </td>

                <td class="px-6 py-5 text-right px-8">
                    <div class="flex justify-end items-center gap-5">
                        <a href="{{ route('positions.edit', $pos) }}" class="text-slate-400 hover:text-slate-900 transition" title="Редактировать">
                            <i class="fas fa-edit fa-lg"></i>
                        </a>

                        <form action="{{ route('positions.destroy', $pos) }}" method="POST" onsubmit="return confirm('ВЫ УВЕРЕНЫ, ЧТО ХОТИТЕ УДАЛИТЬ ДОЛЖНОСТЬ «{{ $pos->name }}»?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-300 hover:text-red-600 transition" title="Удалить">
                                <i class="fas fa-trash-alt fa-lg"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-20 text-center">
                    <div class="text-slate-300 font-black uppercase text-[10px] tracking-[0.2em]">Должности не найдены</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Пагинация (если используете) --}}
@if(method_exists($positions, 'links'))
    <div class="mt-4">
        {{ $positions->links() }}
    </div>
@endif

{{-- Информация о текущем пользователе --}}
<div class="mt-6 text-[10px] font-bold uppercase text-slate-400">
    Запись внес: <span class="text-slate-900">{{ Auth::user()->name }}</span>
</div>

{{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА --}}
<div class="mt-8">
    @include('partials.footer')
</div>

@endsection
