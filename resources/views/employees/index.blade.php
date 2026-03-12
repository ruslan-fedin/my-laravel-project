@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 p-4 md:p-6">

    {{-- ЗАГОЛОВОК --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 md:mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">👥 Сотрудники</h1>
            <p class="text-slate-500 mt-2 text-xs md:text-sm">Управление составом организации</p>
        </div>
        <a href="{{ route('employees.create') }}" class="w-full md:w-auto bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-800 transition flex items-center justify-center gap-2 shadow-lg shadow-slate-200">
            <i class="fas fa-plus"></i>
            <span>Добавить</span>
        </a>
    </div>

    {{-- ВКЛАДКИ --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('employees.index') }}" class="flex-1 px-4 py-3 rounded-xl font-bold text-xs md:text-sm transition {{ request()->routeIs('employees.index') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border hover:bg-slate-50' }}">
            <i class="fas fa-users mr-2"></i>Активные
        </a>
        <a href="{{ route('employees.archive') }}" class="flex-1 px-4 py-3 rounded-xl font-bold text-xs md:text-sm transition {{ request()->routeIs('employees.archive') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border hover:bg-slate-50' }}">
            <i class="fas fa-archive mr-2"></i>Архив
        </a>
    </div>

    {{-- ПОИСК --}}
    <div class="bg-white rounded-2xl p-4 shadow-lg mb-6">
        <form action="{{ route('employees.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                      placeholder="Поиск по ФИО или должности..."
                      class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            @if(request('search'))
            <a href="{{ route('employees.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-sm text-center">
               Сбросить
            </a>
            @endif
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition text-sm">
               Найти
            </button>
        </form>
    </div>

    {{-- ТАБЛИЦА (ДЕСКТОП) --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hidden lg:block">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">№</th>
                        <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">ФИО</th>
                        <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden xl:table-cell">Телефон</th>
                        <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Должность</th>
                        <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden lg:table-cell">Стаж</th>
                        <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Статус</th>
                        <th class="text-center py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Управление</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $index => $employee)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6 text-sm font-bold text-slate-400">{{ $index + 1 + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-black text-indigo-600">{{ mb_substr($employee->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $employee->last_name }} {{ mb_substr($employee->first_name, 0, 1) }}.{{ $employee->middle_name ? mb_substr($employee->middle_name, 0, 1) . '.' : '' }}</div>
                                    <div class="text-xs text-slate-500">{{ $employee->phone ?? 'Нет телефона' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600 hidden xl:table-cell">{{ $employee->phone ?? '—' }}</td>
                        <td class="py-4 px-6">
                            <span class="text-sm font-bold text-slate-700">{{ $employee->position->name ?? 'Без должности' }}</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600 hidden lg:table-cell">
                            @if($employee->hire_date)
                                {{ \Carbon\Carbon::parse($employee->hire_date)->diffForHumans(short: true) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($employee->is_active)
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">✅ Актив</span>
                            @else
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">⏸️ Выкл</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('employees.edit', $employee->id) }}" class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center hover:bg-indigo-200 transition">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Деактивировать сотрудника?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">
                            <i class="fas fa-users-slash text-5xl mb-4"></i>
                            <p class="font-bold text-lg">Сотрудники не найдены</p>
                            <p class="text-sm mt-2">Добавьте первого сотрудника</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="border-t border-slate-200 px-6 py-4">
            {{ $employees->links() }}
        </div>
        @endif
    </div>

    {{-- КАРТОЧКИ (МОБИЛЬНЫЕ И ПЛАНШЕТЫ) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:hidden gap-4">
        @forelse($employees as $employee)
        <div class="bg-white rounded-2xl p-5 shadow-lg border border-slate-100 active:scale-[0.98] transition-transform">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <span class="text-lg font-black text-indigo-600">{{ mb_substr($employee->last_name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-slate-900 text-sm truncate">{{ $employee->last_name }} {{ mb_substr($employee->first_name, 0, 1) }}.{{ $employee->middle_name ? mb_substr($employee->middle_name, 0, 1) . '.' : '' }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ $employee->position->name ?? 'Без должности' }}</div>
                    </div>
                </div>
                @if($employee->is_active)
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 flex-shrink-0">✅</span>
                @else
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-100 text-slate-700 flex-shrink-0">⏸️</span>
                @endif
            </div>

            <div class="space-y-2 mb-4 pb-4 border-b border-slate-100">
                @if($employee->phone)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <i class="fas fa-phone text-slate-400 w-4 text-center"></i>
                    <a href="tel:{{ $employee->phone }}" class="hover:text-indigo-600 transition">{{ $employee->phone }}</a>
                </div>
                @endif
                @if($employee->hire_date)
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <i class="fas fa-briefcase text-slate-400 w-4 text-center"></i>
                    <span>Стаж: {{ \Carbon\Carbon::parse($employee->hire_date)->diffForHumans(short: true) }}</span>
                </div>
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('employees.edit', $employee->id) }}" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition text-center flex items-center justify-center gap-2">
                    <i class="fas fa-pen"></i> <span>Ред.</span>
                </a>
                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Деактивировать сотрудника?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-rose-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-rose-700 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl p-16 shadow-lg text-center">
            <i class="fas fa-users-slash text-5xl text-slate-300 mb-4"></i>
            <p class="font-bold text-slate-600">Сотрудники не найдены</p>
        </div>
        @endforelse
    </div>

    {{-- ПАГИНАЦИЯ ДЛЯ МОБИЛЬНЫХ --}}
    @if($employees->hasPages())
    <div class="mt-6 lg:hidden">
        <div class="bg-white rounded-2xl p-4 shadow-lg">
            {{ $employees->links() }}
        </div>
    </div>
    @endif

</div>
@endsection
