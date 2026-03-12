@extends('layouts.app')
@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900">🗄️ Архив сотрудников</h1>
        <p class="text-slate-500 mt-2 text-sm md:text-base">Удалённые сотрудники</p>
    </div>
    <a href="{{ route('employees.create') }}" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-800 transition flex items-center gap-2 shadow-lg shadow-slate-200">
        <i class="fas fa-plus"></i>
        <span>Добавить сотрудника</span>
    </a>
</div>

{{-- ВКЛАДКИ --}}
<div class="flex gap-2 mb-6">
    <a href="{{ route('employees.index') }}" class="px-6 py-3 rounded-xl font-bold text-sm transition bg-white text-slate-600 border hover:bg-slate-50">
        <i class="fas fa-users mr-2"></i>Активный состав
    </a>
    <a href="{{ route('employees.archive') }}" class="px-6 py-3 rounded-xl font-bold text-sm transition bg-slate-900 text-white">
        <i class="fas fa-archive mr-2"></i>Архив (удаленные)
    </a>
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
                    <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Удалён</th>
                    <th class="text-center py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Управление</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($employees as $index => $employee)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6 text-sm font-bold text-slate-400">{{ $index + 1 }}</td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-black text-slate-600">{{ mb_substr($employee->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $employee->last_name }} {{ mb_substr($employee->first_name, 0, 1) }}.{{ $employee->middle_name ? mb_substr($employee->middle_name, 0, 1) . '.' : '' }}</div>
                                <div class="text-xs text-slate-500 xl:hidden">{{ $employee->phone ?? 'Нет телефона' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600 hidden xl:table-cell">{{ $employee->phone ?? '—' }}</td>
                    <td class="py-4 px-6">
                        <span class="text-sm font-bold text-slate-700">{{ $employee->position->name ?? 'Без должности' }}</span>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600">
                        @if($employee->deleted_at)
                            {{ $employee->deleted_at->format('d.m.Y H:i') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('employees.restore', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Восстановить сотрудника?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition" title="Восстановить">
                                    <i class="fas fa-undo text-sm"></i>
                                </button>
                            </form>
                            <form action="{{ route('employees.forceDelete', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Удалить навсегда? Это действие нельзя отменить!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition" title="Удалить навсегда">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-slate-400">
                        <i class="fas fa-archive text-5xl mb-4"></i>
                        <p class="font-bold text-lg">Архив пуст</p>
                        <p class="text-sm mt-2">Нет удалённых сотрудников</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- КАРТОЧКИ (МОБИЛЬНЫЕ) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:hidden gap-4">
    @forelse($employees as $employee)
    <div class="bg-white rounded-2xl p-5 shadow-lg border border-slate-100">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                    <span class="text-lg font-black text-slate-600">{{ mb_substr($employee->last_name, 0, 1) }}</span>
                </div>
                <div>
                    <div class="font-bold text-slate-900">{{ $employee->last_name }} {{ mb_substr($employee->first_name, 0, 1) }}.{{ $employee->middle_name ? mb_substr($employee->middle_name, 0, 1) . '.' : '' }}</div>
                    <div class="text-xs text-slate-500">{{ $employee->position->name ?? 'Без должности' }}</div>
                </div>
            </div>
            <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-100 text-slate-700">🗄️</span>
        </div>

        <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2 text-sm text-slate-600">
                <i class="fas fa-phone text-slate-400 w-4"></i>
                <span>{{ $employee->phone ?? 'Нет телефона' }}</span>
            </div>
            @if($employee->deleted_at)
            <div class="flex items-center gap-2 text-sm text-slate-600">
                <i class="fas fa-clock text-slate-400 w-4"></i>
                <span>Удалён: {{ $employee->deleted_at->format('d.m.Y H:i') }}</span>
            </div>
            @endif
        </div>

        <div class="flex gap-2 pt-4 border-t border-slate-100">
            <form action="{{ route('employees.restore', $employee->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Восстановить сотрудника?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-emerald-700 transition text-center">
                    <i class="fas fa-undo mr-1"></i> Восстановить
                </button>
            </form>
            <form action="{{ route('employees.forceDelete', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Удалить навсегда?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-rose-700 transition">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl p-16 shadow-lg text-center">
        <i class="fas fa-archive text-5xl text-slate-300 mb-4"></i>
        <p class="font-bold text-slate-600">Архив пуст</p>
    </div>
    @endforelse
</div>
@endsection
