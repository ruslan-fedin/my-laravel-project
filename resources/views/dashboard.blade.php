@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-6 py-8 space-y-8">

    {{-- 1. ШАПКА И ПЕРЕКЛЮЧАТЕЛЬ ДАТЫ --}}
    <div class="{{ $isHistory ?? false ? 'bg-slate-800' : 'bg-white' }} rounded-[3.5rem] p-10 shadow-sm border border-slate-200 transition-colors duration-500 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
            <div>
                <span class="text-[10px] font-black {{ $isHistory ?? false ? 'text-blue-400' : 'text-blue-600' }} uppercase tracking-[0.4em] mb-4 block">
                    {{ $isHistory ?? false ? 'Архивный просмотр' : 'Оперативный дашборд' }}
                </span>
                <h1 class="text-4xl md:text-6xl font-black {{ $isHistory ?? false ? 'text-white' : 'text-slate-900' }} leading-none uppercase tracking-tighter">
                    {{ Auth::user()->last_name }} {{ Auth::user()->first_name }} {{ Auth::user()->middle_name }}
                </h1>
            </div>

            <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-4 bg-slate-50 p-4 rounded-3xl border border-slate-100 shadow-inner">
                <input type="date" name="view_date" value="{{ $viewDate->format('Y-m-d') }}"
                       class="bg-transparent border-none text-slate-900 font-black uppercase text-xs focus:ring-0">
                <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase hover:bg-blue-600 transition-all">
                    Перейти
                </button>
                <a href="{{ route('dashboard') }}" class="text-[9px] font-black uppercase text-slate-500 hover:text-slate-900 transition-colors">
                    Сегодня
                </a>
            </form>
        </div>
    </div>

    {{-- 2. ОСНОВНЫЕ ПОКАЗАТЕЛИ (STATS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Штат (актив)</span>
            <div class="flex items-end gap-2">
                <span class="text-5xl font-black text-slate-900 leading-none">{{ $stats['active_count'] ?? 0 }}</span>
                <span class="text-xs font-bold text-slate-400 mb-1">чел.</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Новички (30д)</span>
            <div class="text-5xl font-black text-blue-600 leading-none">+{{ $stats['new_this_month'] ?? 0 }}</div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Загрузка мощностей</span>
            <div class="w-full bg-slate-100 h-2 rounded-full mt-4 overflow-hidden">
                <div class="bg-slate-900 h-full" style="width: {{ (($stats['active_count'] ?? 0) / ($stats['total_capacity'] ?? 1)) * 100 }}%"></div>
            </div>
            <div class="mt-2 text-[10px] font-black text-slate-900 uppercase italic">Max: {{ $stats['total_capacity'] ?? 100 }}</div>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-4 text-center">Архив увольнений</span>
            <div class="text-5xl font-black text-center leading-none">{{ $stats['archive_count'] ?? 0 }}</div>
        </div>
    </div>

    {{-- 3. ССЫЛКА НА КАЛЕНДАРЬ ОТПУСКОВ --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-[3rem] p-10 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-black uppercase tracking-tighter mb-2">📅 Календарь отпусков</h3>
                <p class="text-white/80">Планирование и учёт отпусков сотрудников</p>
            </div>
            <a href="{{ route('vacations.index') }}" class="bg-white text-indigo-600 px-8 py-4 rounded-2xl text-sm font-black uppercase hover:bg-slate-100 transition-all">
                Открыть календарь →
            </a>
        </div>
    </div>

    {{-- 4. ИМЕНИННИКИ И ЮБИЛЯРЫ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        {{-- Дни рождения --}}
        <div class="bg-slate-900 rounded-[3rem] p-10 text-white relative overflow-hidden">
            <h3 class="text-2xl font-black uppercase tracking-tighter mb-10 flex items-center gap-4">
                <i class="fas fa-birthday-cake text-rose-500"></i> Именинники (3 мес.)
            </h3>
            <div class="space-y-6 relative z-10">
                @forelse($birthdaySaints ?? [] as $emp)
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight">
                                {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
                            </p>
                            <p class="text-[9px] font-bold text-slate-500 uppercase mt-1 italic">
                                {{ \Carbon\Carbon::parse($emp->birth_date)->translatedFormat('d F') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black text-rose-500 uppercase tracking-tighter">
                                Через {{ (int) ($emp->days_until ?? 0) }} дн.
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] text-white/30 uppercase font-bold text-center">Событий не найдено</p>
                @endforelse
            </div>
        </div>

        {{-- Юбилеи стажа --}}
        <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-200">
            <h3 class="text-2xl font-black uppercase tracking-tighter text-slate-900 mb-10">Юбиляры предприятия</h3>
            <div class="space-y-4">
                @forelse($anniversaries ?? [] as $emp)
                    <div class="flex items-center justify-between p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                        <div>
                            <p class="text-xs font-black uppercase text-slate-900">
                                {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
                            </p>
                            <p class="text-[9px] font-bold text-blue-600 uppercase mt-1 italic">
                                Работает с {{ \Carbon\Carbon::parse($emp->hire_date)->format('Y') }} года
                            </p>
                        </div>
                        <div class="bg-blue-600 text-white w-12 h-12 rounded-2xl flex flex-col items-center justify-center">
                            <span class="text-lg font-black leading-none">{{ now()->year - \Carbon\Carbon::parse($emp->hire_date)->year }}</span>
                            <span class="text-[8px] font-black uppercase">лет</span>
                        </div>
                    </div>
                @empty
                    <p class="text-[11px] text-slate-400 font-black uppercase text-center py-10 italic">В ближайший месяц юбилеев нет</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 5. МОДУЛЬ ПРОВЕРКИ КРИТИЧЕСКИХ ДАННЫХ --}}
    @php
        $criticalErrors = \App\Models\Employee::whereNull('deleted_at')
            ->where(function($q) {
                $q->whereNull('birth_date')
                  ->orWhere('birth_date', '0000-00-00')
                  ->orWhereRaw('LENGTH(TRIM(last_name)) < 1');
            })
            ->limit(5)
            ->get();
    @endphp

    @if($criticalErrors->count() > 0)
    <div class="bg-amber-50 rounded-[3rem] p-10 border border-amber-200">
        <div class="flex items-center gap-4 mb-8 text-amber-700">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
            <h3 class="text-2xl font-black uppercase tracking-tighter">Ошибки в личных делах</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($criticalErrors as $err)
            <div class="bg-white p-4 rounded-2xl flex justify-between items-center shadow-sm">
                <span class="text-[10px] font-black uppercase text-slate-700">
                    {{ $err->last_name ?? 'БЕЗ ФАМИЛИИ' }} {{ $err->first_name ?? 'БЕЗ ИМЕНИ' }}
                </span>
                <a href="{{ route('employees.edit', $err->id) }}"
                   class="text-[9px] font-black uppercase bg-amber-100 px-3 py-1 rounded-lg hover:bg-slate-900 hover:text-white transition-colors">
                    Исправить
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
