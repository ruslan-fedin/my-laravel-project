@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    {{-- ЗАГОЛОВОК --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">🌷 Все работы</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">Учёт работ на клумбах</p>
        </div>
        <a href="{{ route('flower-beds.works.create') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg shadow-emerald-200">
            <i class="fas fa-plus"></i>
            <span>Добавить работу</span>
        </a>
    </div>

    {{-- УВЕДОМЛЕНИЯ --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ФИЛЬТРЫ --}}
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-6 border border-slate-100">
        <form action="{{ route('flower-beds.works.index') }}" method="GET" class="flex flex-wrap items-end gap-4">

            {{-- Поиск --}}
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">🔍 Поиск</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Название или описание..."
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm">
            </div>

            {{-- Год --}}
            <div class="w-full md:w-40">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">📅 Год</label>
                <select name="year" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">Все годы</option>
                    @foreach($years ?? [] as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Вид работы --}}
            <div class="w-full md:w-48">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">🔧 Вид работы</label>
                <select name="work_type_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">Все виды</option>
                    @foreach($workTypes ?? [] as $type)
                    <option value="{{ $type->id }}" {{ request('work_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Статус --}}
            <div class="w-full md:w-40">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">📊 Статус</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="">Все</option>
                    <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>🟡 Запланировано</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>🔵 В работе</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Завершено</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>⚪ Отменено</option>
                </select>
            </div>

            {{-- Кнопки --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-emerald-700 transition">
                    Найти
                </button>
                <a href="{{ route('flower-beds.works.index') }}" class="bg-slate-100 text-slate-700 px-4 py-3 rounded-xl font-bold text-sm hover:bg-slate-200 transition" title="Сбросить фильтры">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- СПИСОК РАБОТ --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Дата</th>
                    <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Работа</th>
                    <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Клумба</th>
                    <th class="text-center py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Цветов</th>
                    <th class="text-center py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Статус</th>
                    <th class="text-center py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($workRecords as $record)
                <tr class="hover:bg-slate-50 transition-colors">
                    {{-- Дата --}}
                    <td class="py-4 px-6">
                        <div class="text-sm font-bold text-slate-700">
                            {{ $record->actual_start ? $record->actual_start->format('d.m.Y') : '—' }}
                        </div>
                        @if($record->planned_start && !$record->actual_start)
                        <div class="text-xs text-slate-400">план: {{ $record->planned_start->format('d.m.Y') }}</div>
                        @endif
                    </td>

                    {{-- Работа --}}
                    <td class="py-4 px-6">
                        <div class="font-bold text-slate-900">{{ $record->title }}</div>
                        <div class="text-xs text-slate-500">{{ $record->workType->name }}</div>
                    </td>

                    {{-- Клумба --}}
                    <td class="py-4 px-6">
                        <div class="text-sm font-bold text-slate-700">{{ $record->flowerBed->short_name ?? '—' }}</div>
                        @if($record->flowerBed->district)
                        <div class="text-xs text-slate-500">{{ $record->flowerBed->district }}</div>
                        @endif
                    </td>

                    {{-- Цветов --}}
                    <td class="py-4 px-6 text-center">
                        @if($record->total_quantity > 0)
                        <span class="text-sm font-bold text-emerald-600">{{ number_format($record->total_quantity, 0, ',', ' ') }} шт.</span>
                        @else
                        <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>

                    {{-- Статус --}}
                    <td class="py-4 px-6 text-center">
                        <span class="text-xs font-bold px-3 py-1.5 rounded-lg {{ $record->status_color }}">
                            {{ $record->status_label }}
                        </span>
                    </td>

                    {{-- Действия --}}
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('flower-beds.works.show', $record->id) }}"
                               class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition" title="Просмотр">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('flower-beds.works.edit', $record->id) }}"
                               class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center hover:bg-indigo-200 transition" title="Редактировать">
                                <i class="fas fa-pen text-sm"></i>
                            </a>
                            <form action="{{ route('flower-beds.works.destroy', $record->id) }}" method="POST"
                                  onsubmit="return confirm('Удалить эту работу?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition" title="Удалить">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-slate-400">
                        <i class="fas fa-clipboard-list text-5xl mb-4"></i>
                        <p class="font-bold text-lg">Нет работ</p>
                        <p class="text-sm mt-2">Добавьте первую запись о работе</p>
                        <a href="{{ route('flower-beds.works.create') }}" class="inline-block mt-4 bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-emerald-700 transition">
                            Добавить работу
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ПАГИНАЦИЯ --}}
    @if($workRecords->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $workRecords->links() }}
    </div>
    @endif

    {{-- НАЗАД К КЛУМБАМ --}}
    <div class="mt-6">
        <a href="{{ route('flower-beds.index') }}" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-bold border hover:bg-slate-50 transition inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Назад к клумбам</span>
        </a>
    </div>

</div>
@endsection
