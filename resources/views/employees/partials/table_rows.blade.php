@forelse($employees as $emp)
<tr class="hover:bg-slate-50/50 transition-colors group border-b border-slate-100 last:border-0">
    {{-- № --}}
    <td class="px-4 py-1 text-center text-slate-300 font-bold text-[10px]">
        {{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}
    </td>

    {{-- Фото --}}
    <td class="px-2 py-1 text-center w-12">
        <div class="w-9 h-9 rounded-full border border-slate-200 overflow-hidden bg-white mx-auto shadow-sm">
            @if($emp->photo && file_exists(public_path($emp->photo)))
                <img src="{{ asset($emp->photo) }}" class="w-full h-full object-cover">
            @else
                <div class="flex items-center justify-center h-full text-slate-200 bg-slate-50">
                    <i class="fas fa-user text-[10px]"></i>
                </div>
            @endif
        </div>
    </td>

    {{-- Личные данные --}}
    <td class="px-4 py-1 border-r" style="min-width: 480px;">
        <div class="font-black text-slate-800 uppercase tracking-tighter text-[14px] leading-tight">
            {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
        </div>

        <div class="flex items-center gap-3 mt-0.5">
            {{-- Телефон --}}
            <span class="text-[10px] font-black text-indigo-600 uppercase flex items-center bg-indigo-50/50 px-1.5 rounded">
                <i class="fas fa-phone mr-1 text-[8px] text-indigo-400"></i>
                {{ $emp->phone ?? 'нет связи' }}
            </span>

            {{-- Расчет Стажа --}}
            @if($emp->hire_date)
                @php
                    $hireDate = \Carbon\Carbon::parse($emp->hire_date);
                    $diff = $hireDate->diff(\Carbon\Carbon::now());
                @endphp
                <span class="text-[10px] font-black text-slate-500 uppercase flex items-center border-l pl-3 border-slate-200">
                    <i class="fas fa-briefcase mr-1 text-[8px] text-amber-500"></i>
                    Стаж: <span class="text-slate-800 ml-1">@if($diff->y > 0){{ $diff->y }}г @endif{{ $diff->m }}м</span>
                </span>
            @endif

            {{-- ИСПРАВЛЕННЫЙ РАСЧЕТ ДНЕЙ ДО ДР --}}
            @if($emp->birth_date)
                @php
                    $birthDate = \Carbon\Carbon::parse($emp->birth_date);
                    $today = \Carbon\Carbon::now()->startOfDay();

                    // Устанавливаем ДР на текущий год
                    $nextBirthday = $birthDate->copy()->year($today->year)->startOfDay();

                    // Если ДР уже прошел в этом году, переносим на следующий
                    if ($nextBirthday->isBefore($today)) {
                        $nextBirthday->addYear();
                    }

                    // Считаем разницу в днях
                    $daysUntil = $today->diffInDays($nextBirthday);
                @endphp
                <span class="text-[10px] font-black text-slate-500 uppercase border-l pl-3 border-slate-200 flex items-center">
                    <i class="fas fa-cake-candles mr-1 text-[8px] text-rose-500"></i>
                    {{ $birthDate->age }} лет
                    <span class="text-rose-600 ml-1 font-extrabold lowercase italic">
                        @if($daysUntil == 0)
                            (Сегодня день рождения!)
                        @else
                            (через {{ $daysUntil }} дн.)
                        @endif
                    </span>
                </span>
            @endif
        </div>
    </td>

    {{-- Должность --}}
    <td class="px-6 py-1 text-slate-700 font-black border-r uppercase text-[11px] bg-slate-50/30" style="min-width: 320px;">
        {{ $emp->position->name ?? '---' }}
    </td>

    {{-- Статус --}}
    <td class="px-4 py-1 text-center border-r w-28">
        @if($emp->trashed())
            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-rose-50 text-rose-600 border border-rose-100">Архив</span>
        @else
            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase border {{ $emp->is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-200' }}">
                {{ $emp->is_active ? 'Актив' : 'Выкл' }}
            </span>
        @endif
    </td>

    {{-- Управление --}}
    <td class="px-6 py-1 text-right">
        <div class="flex justify-end items-center gap-4">
            @if($emp->trashed())
                <form action="{{ route('employees.restore', $emp->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-500 text-white px-3 py-1 rounded text-[9px] font-black uppercase tracking-wider shadow-sm">Восстановить</button>
                </form>
            @else
                <a href="{{ route('employees.edit', $emp->id) }}" class="text-slate-300 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-edit text-sm"></i>
                </a>
                <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('В архив?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-slate-300 hover:text-rose-500 transition-colors">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="6" class="py-16 text-center text-slate-300 font-black uppercase text-[10px] tracking-widest">Список пуст</td></tr>
@endforelse
