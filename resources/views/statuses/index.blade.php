@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Справочник статусов</h1>
        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Настройка цветовой индикации и кодов табеля</p>
    </div>

    <a href="{{ route('statuses.create') }}" class="bg-slate-900 text-white px-6 py-3 rounded text-[11px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl flex items-center gap-2">
        <i class="fas fa-plus text-[10px]"></i> Добавить статус
    </a>
</div>

{{-- Таблица статусов --}}
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-slate-200">
    <table class="min-w-full border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <th class="px-6 py-5 text-center w-20 border-r">№</th>
                <th class="px-6 py-5 text-center w-24 border-r">Индикатор</th>
                <th class="px-6 py-5 text-center w-24 border-r">Код</th>
                <th class="px-6 py-5 text-left border-r">Наименование статуса</th>
                <th class="px-6 py-5 text-right px-8">Управление</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($statuses as $status)
            <tr class="hover:bg-slate-50/50 transition-colors">
                {{-- № по порядку --}}
                <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">
                    {{ $loop->iteration }}
                </td>

                {{-- Визуальный цвет --}}
                <td class="px-6 py-5 text-center border-r">
                    <div class="w-8 h-8 rounded-full border shadow-inner mx-auto ring-4 ring-slate-50"
                         style="background-color: {{ $status->color }}"
                         title="{{ $status->color }}"></div>
                </td>

                {{-- Короткий код (например, Я, В, Б) --}}
                <td class="px-6 py-5 text-center font-black text-blue-600 border-r uppercase tracking-tighter text-base">
                    {{ $status->short_name }}
                </td>

                {{-- Полное название --}}
                <td class="px-6 py-5 font-black text-slate-700 uppercase tracking-tight">
                    {{ $status->name }}
                </td>

                {{-- Кнопки действий --}}
                <td class="px-6 py-5 text-right px-8">
                    <div class="flex justify-end items-center gap-5">
                        <a href="{{ route('statuses.edit', $status) }}" class="text-slate-400 hover:text-slate-900 transition" title="Редактировать">
                            <i class="fas fa-edit fa-lg"></i>
                        </a>

                        <form action="{{ route('statuses.destroy', $status) }}" method="POST"
                              onsubmit="return confirm('Вы уверены, что хотите удалить статус «{{ $status->name }}»?')">
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
                <td colspan="5" class="px-6 py-20 text-center">
                    <div class="text-slate-300 font-black uppercase text-[10px] tracking-[0.2em]">Статусы не определены</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Подвал с полным ФИО --}}
<div class="mt-8 pt-6 border-t border-slate-200 text-[10px] font-bold uppercase text-slate-400 flex justify-between items-center">
    <div>Система управления справочниками v1.0</div>
    <div>
        Текущий администратор: <span class="text-slate-900">{{ Auth::user()->name }}</span>
    </div>
</div>

  <div class="mt-8">
@include('layouts.footer')
    </div>
@endsection
