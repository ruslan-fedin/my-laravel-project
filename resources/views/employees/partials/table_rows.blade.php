@forelse($employees as $emp)
<tr class="hover:bg-slate-50/50 transition-colors">
    <td class="px-6 py-5 text-center text-slate-400 border-r font-medium text-xs">
        {{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}
    </td>

    <td class="px-6 py-5 border-r text-center">
        <div class="w-10 h-10 rounded-full border border-slate-100 overflow-hidden bg-slate-50 mx-auto shadow-sm">
            @if($emp->photo && file_exists(public_path($emp->photo)))
                <img src="{{ asset($emp->photo) }}" class="w-full h-full object-cover">
            @else
                <div class="flex items-center justify-center h-full text-slate-200">
                    <i class="fas fa-user text-xs"></i>
                </div>
            @endif
        </div>
    </td>

    <td class="px-6 py-5 border-r">
        <div class="font-black text-slate-700 uppercase tracking-tight">
            {{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}
        </div>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
            {{-- Телефон --}}
            <div class="text-[10px] text-slate-600 font-black uppercase">
                <i class="fas fa-phone mr-1 text-[9px] text-slate-400"></i> {{ $emp->phone ?? 'нет телефона' }}
            </div>

            {{-- Возраст и Дни до ДР --}}
            @if($emp->birth_date)
                @php
                    $birthDate = \Carbon\Carbon::parse($emp->birth_date);
                    $now = \Carbon\Carbon::now()->startOfDay();
                    $nextBirthday = $birthDate->copy()->year($now->year);
                    if ($nextBirthday->isPast()) { $nextBirthday->addYear(); }
                    $daysUntil = (int)$now->diffInDays($nextBirthday);
                @endphp
                <div class="text-[10px] text-slate-600 font-black uppercase border-l pl-3 border-slate-200">
                    <i class="fas fa-cake-candles mr-1 text-[9px] text-slate-400"></i>
                    {{ $birthDate->age }} лет
                    <span class="text-rose-500 ml-1">(до ДР: {{ $daysUntil }} дн.)</span>
                </div>
            @endif

            {{-- Стаж (Годы и Месяцы без запятых) --}}
            @if($emp->hire_date)
                @php
                    $hireDate = \Carbon\Carbon::parse($emp->hire_date);
                    $diff = $hireDate->diff(\Carbon\Carbon::now());
                    $years = $diff->y;
                    $months = $diff->m;
                @endphp
                <div class="text-[10px] text-slate-600 font-black uppercase border-l pl-3 border-slate-200">
                    <i class="fas fa-briefcase mr-1 text-[9px] text-slate-400"></i>
                    Стаж:
                    @if($years > 0) {{ $years }} г. @endif
                    {{ $months }} мес.
                </div>
            @endif
        </div>
    </td>

    <td class="px-6 py-5 text-slate-600 font-black border-r uppercase text-[11px]">
        {{ $emp->position->name ?? '---' }}
    </td>

    <td class="px-6 py-5 text-center border-r">
        @if($emp->trashed())
            <span class="px-3 py-1 rounded text-[9px] font-black uppercase border bg-rose-50 text-rose-600 border-rose-200">В архиве</span>
        @else
            <span class="px-3 py-1 rounded text-[9px] font-black uppercase border {{ $emp->is_active ? 'bg-green-50 text-green-600 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                {{ $emp->is_active ? 'Активен' : 'Неактивен' }}
            </span>
        @endif
    </td>

    <td class="px-6 py-5 text-right px-8">
        <div class="flex justify-end items-center gap-5">
            @if($emp->trashed())
                <form action="{{ route('employees.restore', $emp->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-500 text-white px-3 py-1.5 rounded text-[9px] font-black uppercase tracking-wider hover:bg-emerald-600 transition shadow-sm">Восстановить</button>
                </form>
            @else
                <a href="{{ route('employees.edit', $emp->id) }}" class="text-slate-400 hover:text-slate-900 transition">
                    <i class="fas fa-edit fa-lg"></i>
                </a>
                <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Переместить в архив?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-slate-300 hover:text-red-600 transition">
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
        <div class="text-slate-300 font-black uppercase text-[10px] tracking-[0.2em]">Сотрудники не найдены</div>
    </td>
</tr>
@endforelse
