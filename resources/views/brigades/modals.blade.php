{{-- 1. МОДАЛКА: ДОБАВИТЬ РАБОЧЕГО --}}
<div id="assignModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[1.5rem] p-8 w-full max-w-md shadow-2xl border-t-4 border-blue-600">
        <div class="flex items-center gap-3 mb-5 text-slate-900">
            <i class="fas fa-user-plus text-lg"></i>
            <h3 class="text-lg font-black uppercase">В состав бригады</h3>
        </div>
        <form action="{{ route('brigades.update-leader') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" id="modalBrigId">
            <div class="mb-6">
                <select name="employee_id" id="employeeSelect" required class="w-full bg-slate-100 border-none rounded-lg p-3 text-[10px] font-bold uppercase outline-none">
                    <option value="">— ВЫБРАТЬ —</option>
                    @foreach($freeWorkers as $worker)
                        <option value="{{ $worker->id }}" class="worker-option">
                            {{ $worker->last_name }} {{ $worker->first_name }} {{ $worker->middle_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeAssignModal()" class="flex-1 py-3 font-black uppercase text-[9px] bg-slate-100 text-slate-500 rounded-lg">Отмена</button>
                <button type="submit" class="flex-1 py-3 font-black uppercase text-[9px] bg-blue-600 text-white rounded-lg shadow-md">Добавить</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. МОДАЛКА: ПЕРЕНОС --}}
<div id="moveModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[110] p-4">
    <div class="bg-white rounded-[1.5rem] p-8 w-full max-w-md shadow-2xl border-t-4 border-indigo-600">
        <div class="mb-5">
            <h3 class="text-lg font-black uppercase text-slate-900">Перенос</h3>
            <p id="moveWorkerName" class="text-indigo-600 font-bold text-[9px] uppercase mt-1"></p>
        </div>
        <form action="{{ route('brigades.update-leader') }}" method="POST">
            @csrf
            <input type="hidden" name="employee_id" id="moveWorkerId">
            <div class="mb-6">
                <select name="parent_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg font-bold uppercase text-[10px] outline-none">
                    <option value="">— ПЕРЕДАТЬ —</option>
                    @foreach($allLeaders as $leader)
                        <option value="{{ $leader->id }}">
                            {{ $leader->last_name }} {{ $leader->first_name }} {{ $leader->middle_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeMoveModal()" class="flex-1 py-3 font-black uppercase text-[9px] bg-slate-100 text-slate-500 rounded-lg">Отмена</button>
                <button type="submit" class="flex-1 py-3 font-black uppercase text-[9px] bg-indigo-600 text-white rounded-lg shadow-md">Перенести</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. МОДАЛКА: ОТПУСК --}}
<div id="subModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
    <div class="bg-white rounded-[1.5rem] p-8 w-full max-w-sm border-t-4 border-rose-500 shadow-2xl">
        <div class="text-center mb-6">
            <h3 class="text-lg font-black text-slate-900 uppercase">Уход в отпуск</h3>
        </div>
        <form action="{{ route('brigades.start-vacation') }}" method="POST">
            @csrf
            <input type="hidden" name="absentee_id" id="subBrigId">
            <div class="mb-6">
                <select name="substitute_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg font-bold uppercase text-[10px] outline-none">
                    <option value="">— КОМУ ПЕРЕДАТЬ —</option>
                    @foreach($allLeaders as $leader)
                        <option value="{{ $leader->id }}">{{ $leader->last_name }} {{ $leader->first_name }} {{ $leader->middle_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeSubModal()" class="flex-1 py-3 font-black uppercase text-[9px] bg-slate-100 text-slate-500 rounded-lg">Отмена</button>
                <button type="submit" class="flex-1 py-3 font-black uppercase text-[9px] bg-rose-500 text-white rounded-lg shadow-md">Отправить</button>
            </div>
        </form>
    </div>
</div>
