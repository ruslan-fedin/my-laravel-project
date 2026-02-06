@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="flex justify-between items-center mb-8 px-4">
    <div>
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Архив сотрудников</h1>
        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Список записей с пометкой на удаление</p>
    </div>

    {{-- Кнопка возврата в стиле основной страницы --}}
    <a href="{{ route('employees.index') }}" class="bg-slate-900 text-white px-6 py-3 rounded text-[11px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl flex items-center gap-2">
        <i class="fas fa-arrow-left text-[10px]"></i> Вернуться к списку
    </a>
</div>

{{-- КНОПКИ ПЕРЕКЛЮЧЕНИЯ (ДИЗАЙН 1-В-1 КАК В ИНДЕКСЕ) --}}
<div class="flex gap-2 mb-6 px-4">
    <a href="{{ route('employees.index') }}"
       class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2 bg-white text-slate-400 border border-slate-200 hover:bg-slate-50">
        <i class="fas fa-users"></i> Активный состав
    </a>
    <a href="{{ route('employees.archive') }}"
       class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-2 bg-rose-600 text-white shadow-md">
        <i class="fas fa-archive"></i> Архив (Удаленные)
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-slate-200 mx-4">
    {{-- ОПОВЕЩЕНИЕ --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-b border-emerald-100 text-emerald-600 px-6 py-4 text-xs font-bold uppercase tracking-widest">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full border-collapse">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <th class="px-6 py-5 text-center w-20 border-r">№</th>
                <th class="px-6 py-5 text-left border-r">Фамилия Имя Отчество полностью</th>
                <th class="px-6 py-5 text-left border-r">Должность</th>
                <th class="px-6 py-5 text-center border-r w-32">Статус</th>
                <th class="px-6 py-5 text-right px-8 w-80">Управление</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($employees as $emp)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">
                    {{ $loop->iteration }}
                </td>

                <td class="px-6 py-5 border-r">
                    <div class="font-black text-slate-700 uppercase tracking-tight">
                        {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold uppercase">ID: {{ str_pad($emp->id, 5, '0', STR_PAD_LEFT) }}</div>
                </td>

                <td class="px-6 py-5 text-slate-600 font-medium border-r">
                    {{ $emp->position->name ?? '---' }}
                </td>

                <td class="px-6 py-5 text-center border-r">
                    <span class="px-3 py-1 rounded text-[9px] font-black uppercase border bg-rose-50 text-rose-600 border-rose-200">
                        В архиве
                    </span>
                </td>

                <td class="px-6 py-5 text-right px-8">
                    <div class="flex justify-end items-center gap-3">
                        {{-- ТЕКСТОВАЯ КНОПКА ВОССТАНОВИТЬ --}}
                        <form action="{{ route('employees.restore', $emp->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-emerald-500 text-white px-4 py-2 rounded text-[9px] font-black uppercase tracking-wider hover:bg-emerald-600 transition shadow-sm">
                                Восстановить
                            </button>
                        </form>

                        {{-- ТЕКСТОВАЯ КНОПКА УДАЛИТЬ НАВСЕГДА --}}
                        <form action="{{ route('employees.forceDelete', $emp->id) }}" method="POST" onsubmit="return confirm('Внимание! Сотрудник будет полностью удален из базы данных. Продолжить?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded text-[9px] font-black uppercase tracking-wider hover:bg-rose-700 transition shadow-sm">
                                Удалить навсегда
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-20 text-center text-slate-300 font-black uppercase text-[10px] tracking-[0.2em]">
                    В архиве пока ничего нет
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($employees instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-8 px-4">
        {{ $employees->links() }}
    </div>
@endif

@include('layouts.footer')
@endsection
