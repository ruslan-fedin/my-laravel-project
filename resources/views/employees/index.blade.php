@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Сотрудники</h1>
        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Управление кадровым составом организации</p>
    </div>

    <a href="{{ route('employees.create') }}" class="bg-slate-900 text-white px-6 py-3 rounded text-[11px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl flex items-center gap-2">
        <i class="fas fa-plus text-[10px]"></i> Добавить сотрудника
    </a>
</div>

{{-- КНОПКИ ПЕРЕКЛЮЧЕНИЯ АРХИВА --}}
<div class="flex gap-2 mb-6">
    <a href="{{ route('employees.index') }}"
       class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2 {{ !request('show_deleted') ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200 hover:bg-slate-50' }}">
        <i class="fas fa-users"></i> Активный состав
    </a>
    <a href="{{ route('employees.index', ['show_deleted' => 1]) }}"
       class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2 {{ request('show_deleted') ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200 hover:bg-slate-50' }}">
        <i class="fas fa-archive"></i> Архив (Удаленные)
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-slate-200">
    <table class="min-w-full border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <th class="px-6 py-5 text-center w-20 border-r">№</th>

                <th class="px-6 py-5 text-left border-r">
                    <a href="{{ route('employees.index', ['sort' => 'last_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc', 'show_deleted' => request('show_deleted')]) }}" class="hover:text-slate-900 flex items-center gap-2">
                        Фамилия Имя Отчество полностью <i class="fas fa-sort opacity-30 text-[9px]"></i>
                    </a>
                </th>

                <th class="px-6 py-5 text-left border-r">Должность</th>
                <th class="px-6 py-5 text-center border-r w-32">Стаж</th>
                <th class="px-6 py-5 text-center border-r w-32">Статус</th>
                <th class="px-6 py-5 text-right px-8 w-48">Управление</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($employees as $emp)
            <tr class="hover:bg-slate-50/50 transition-colors {{ !$emp->is_active ? 'opacity-60' : '' }}">
                <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">
                    {{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}
                </td>

                <td class="px-6 py-5 border-r">
                    <div class="font-black text-slate-700 uppercase tracking-tight">
                        {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $emp->phone ?? 'нет телефона' }}</div>
                </td>

                <td class="px-6 py-5 text-slate-600 font-medium border-r">
                    {{ $emp->position->name ?? '---' }}
                </td>

                <td class="px-6 py-5 text-center border-r font-bold text-blue-600 text-xs">
                    {{ $emp->experience ?? '---' }}
                </td>

                <td class="px-6 py-5 text-center border-r">
                    @if($emp->trashed())
                        <span class="px-3 py-1 rounded text-[9px] font-black uppercase border bg-rose-50 text-rose-600 border-rose-200">
                            В архиве
                        </span>
                    @else
                        <span class="px-3 py-1 rounded text-[9px] font-black uppercase border {{ $emp->is_active ? 'bg-green-50 text-green-600 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                            {{ $emp->is_active ? 'Активен' : 'Неактивен' }}
                        </span>
                    @endif
                </td>

                <td class="px-6 py-5 text-right px-8">
                    <div class="flex justify-end items-center gap-5">
                        @if($emp->trashed())
                            {{-- КНОПКА ВОССТАНОВЛЕНИЯ (ТОЛЬКО ДЛЯ АРХИВА) --}}
                            <form action="{{ route('employees.restore', $emp->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-emerald-500 text-white px-3 py-1.5 rounded text-[9px] font-black uppercase tracking-wider hover:bg-emerald-600 transition shadow-sm" title="Восстановить">
                                    Восстановить
                                </button>
                            </form>
                        @else
                            {{-- ОБЫЧНЫЕ КНОПКИ (ДЛЯ АКТИВНЫХ) --}}
                            <a href="{{ route('employees.edit', $emp) }}" class="text-slate-400 hover:text-slate-900 transition" title="Редактировать">
                                <i class="fas fa-edit fa-lg"></i>
                            </a>

                            <form action="{{ route('employees.destroy', $emp) }}" method="POST"
                                  onsubmit="return confirm('Переместить сотрудника {{ $emp->last_name }} в архив?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-red-600 transition" title="Удалить">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-20 text-center">
                    <div class="text-slate-300 font-black uppercase text-[10px] tracking-[0.2em]">
                        {{ request('show_deleted') ? 'Архив пуст' : 'Сотрудники не найдены' }}
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-8">
    {{ $employees->links() }}
</div>

<div class="mt-8">
    @include('partials.footer')
</div>

@endsection
