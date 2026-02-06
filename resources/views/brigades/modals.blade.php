{{-- 1. МОДАЛКА: ДОБАВИТЬ РАБОЧЕГО --}}
<div id="assignModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-lg shadow-2xl border-t-8 border-blue-600">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center"><i class="fas fa-user-plus text-xl"></i></div>
            <h3 class="text-2xl font-black uppercase tracking-tighter text-slate-900">Добавить в состав</h3>
        </div>
        <form action="{{ route('brigades.update-leader') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" id="modalBrigId">
            <div class="mb-8">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-2 tracking-widest">Сотрудники без назначенной бригады</label>
                <select name="employee_id" id="employeeSelect" required class="w-full bg-slate-100 border-none rounded-xl p-4 text-xs font-black uppercase outline-none focus:ring-2 focus:ring-slate-900">
                    <option value="">— ВЫБРАТЬ ИЗ СПИСКА —</option>
                    @foreach($freeWorkers as $worker)
                        <option value="{{ $worker->id }}" class="worker-option">
                            {{ $worker->last_name }} {{ $worker->first_name }} {{ $worker->middle_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeAssignModal()" class="flex-1 py-4 font-black uppercase text-[10px] bg-slate-100 text-slate-500 rounded-xl">Отмена</button>
                <button type="submit" class="flex-1 py-4 font-black uppercase text-[10px] bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 transition-all">Подтвердить</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. МОДАЛКА: ПЕРЕНОС (ВЕРНУЛАСЬ) --}}
<div id="moveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[110] p-4">
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-lg shadow-2xl border-t-8 border-indigo-600">
        <div class="flex items-center gap-4 mb-4 text-slate-900">
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center"><i class="fas fa-exchange-alt text-xl"></i></div>
            <div>
                <h3 class="text-2xl font-black uppercase tracking-tighter">Перенос сотрудника</h3>
                <p id="moveWorkerName" class="text-indigo-600 font-black text-[11px] uppercase mt-1 italic"></p>
            </div>
        </div>
        <form action="{{ route('brigades.update-leader') }}" method="POST">
            @csrf
            <input type="hidden" name="employee_id" id="moveWorkerId">
            <div class="mb-8">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-2">Назначить новому руководителю</label>
                <select name="parent_id" required class="w-full p-5 bg-slate-50 border-2 border-slate-100 rounded-2xl font-black uppercase text-xs focus:border-indigo-500 outline-none">
                    <option value="">— ВЫБЕРИТЕ ПРИНИМАЮЩЕГО —</option>
                    @foreach($allLeaders as $leader)
                        <option value="{{ $leader->id }}">
                            {{ $leader->last_name }} {{ $leader->first_name }} {{ $leader->middle_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeMoveModal()" class="flex-1 py-4 font-black uppercase text-[10px] bg-slate-100 text-slate-500 rounded-xl">Отмена</button>
                <button type="submit" class="flex-1 py-4 font-black uppercase text-[10px] bg-indigo-600 text-white rounded-xl shadow-lg hover:bg-indigo-700">Перенести</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. МОДАЛКА: ОТПУСК --}}
<div id="subModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-md border-t-8 border-rose-500 shadow-2xl">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-black text-slate-900 uppercase">Уход в отпуск</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase mt-2">Кому передать людей?</p>
        </div>
        <form action="{{ route('brigades.start-vacation') }}" method="POST">
            @csrf
            <input type="hidden" name="absentee_id" id="subBrigId">
            <div class="mb-8">
                <select name="substitute_id" required class="w-full p-5 bg-slate-50 border-2 border-slate-100 rounded-2xl font-black uppercase text-xs focus:border-rose-500 outline-none">
                    <option value="">— ВЫБЕРИТЕ ПРИНИМАЮЩЕГО —</option>
                    @foreach($allLeaders as $leader)
                        <option value="{{ $leader->id }}">{{ $leader->last_name }} {{ $leader->first_name }} {{ $leader->middle_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeSubModal()" class="flex-1 py-4 font-black uppercase text-[10px] bg-slate-100 text-slate-500 rounded-xl">Отмена</button>
                <button type="submit" class="flex-1 py-4 font-black uppercase text-[10px] bg-rose-500 text-white rounded-xl shadow-lg">Отправить</button>
            </div>
        </form>
    </div>
</div>
