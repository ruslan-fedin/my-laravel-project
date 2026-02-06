<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление структурой ЗХ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Подключаем библиотеку для перетаскивания --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Ваши персональные отступы */
        .indent-area { padding-left: 120px; padding-right: 120px; }

        /* Стили ссылок для ФИО */
        .fio-brigadier-blue { color: #2563eb !important; font-weight: 800; text-decoration: none; }
        .fio-brigadier-blue:hover { text-decoration: underline; }

        .fio-standard-black { color: #0f172a !important; font-weight: 800; text-decoration: none; transition: color 0.2s; }
        .fio-standard-black:hover { color: #2563eb !important; text-decoration: underline; }

        .brigade-card { border: 1px solid #e2e8f0; background: white; border-radius: 24px; position: relative; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; overflow: hidden; cursor: grab; }
        .brigade-card:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0; }
        .sortable-chosen { border: 2px solid #2563eb; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
        .fio-row { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }
        .location-box { background-color: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 10px; margin-top: 10px; }
        .location-input { color: #dc2626; font-weight: 800; text-transform: uppercase; width: 100%; background: transparent; outline: none; text-align: center; font-size: 12px; }
        .vacation-overlay { position: absolute; inset: 0; display: flex; items: center; justify-content: center; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(4px); z-index: 50; }
        .stat-col { width: 100px; text-align: center; }
        .stat-col-wide { width: 140px; text-align: center; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

@include('layouts.navigation')

<main class="py-12 indent-area flex-grow">

    @foreach($masters as $master)
        @php
            $myBrigadiers = $master->subordinates;
            $countBrigadiers = $myBrigadiers->count();
            $countWorkers = 0;
            foreach($myBrigadiers as $br) {
                $countWorkers += $br->subordinates->count();
                foreach($br->subbingFor as $sub) { $countWorkers += $sub->subordinates->count(); }
            }
            $grandTotal = $countBrigadiers + $countWorkers;
        @endphp

        <div class="mb-20">
            {{-- ПАНЕЛЬ МАСТЕРА --}}
            <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white mb-8 flex justify-between items-center shadow-xl">
                <div class="flex items-center gap-6 min-w-0">
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-900/50"><i class="fas fa-user-shield text-2xl"></i></div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Мастер участка</span>
                        <h2 class="text-3xl font-black uppercase fio-row">
                            {{ $master->last_name }} {{ $master->first_name }} {{ $master->middle_name }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-l border-slate-700 pl-10 hidden md:flex">
                    <div class="stat-col"><p class="text-[9px] text-slate-500 uppercase font-black mb-1">Бригадиры</p><p class="text-3xl font-black">{{ $countBrigadiers }}</p></div>
                    <div class="stat-col"><p class="text-[9px] text-slate-500 uppercase font-black mb-1">Рабочие</p><p class="text-3xl font-black">{{ $countWorkers }}</p></div>
                    <div class="stat-col-wide bg-blue-600/10 px-4 py-3 rounded-2xl border border-blue-500/20 ml-2">
                        <p class="text-[9px] text-blue-400 uppercase font-black mb-1 text-center">Всего людей</p>
                        <p class="text-3xl font-black text-blue-500 text-center">{{ $grandTotal }}</p>
                    </div>
                </div>
            </div>

            {{-- ГРИД КАРТОЧЕК С СОРТИРОВКОЙ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 js-sortable-grid" data-master-id="{{ $master->id }}">
                @foreach($myBrigadiers as $brigadier)
                    @php
                        $isVacation = $brigadier->status === 'vacation';
                        $location = DB::table('brigade_locations')->where('brigadier_id', $brigadier->id)->first();
                        $workers = !$isVacation ? $brigadier->subordinates : collect();
                        foreach($brigadier->subbingFor as $absentee) { $workers = $workers->merge($absentee->subordinates); }
                        $totalBrigade = $workers->count() + 1;
                    @endphp

                    <div class="brigade-card" data-id="{{ $brigadier->id }}">
                        @if($isVacation)
                            <div class="vacation-overlay">
                                <form action="{{ route('brigades.return-vacation') }}" method="POST"> @csrf
                                    <input type="hidden" name="brigadier_id" value="{{ $brigadier->id }}">
                                    <button type="submit" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[11px] shadow-2xl">Вернуть из отпуска</button>
                                </form>
                            </div>
                        @endif

                        <div class="p-8 border-b">
                            <div class="flex justify-between items-start">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[9px] font-black text-slate-400 uppercase">{{ $brigadier->position->name ?? 'Бригадир' }}</span>
                                        <span class="bg-blue-600 text-white text-[10px] font-black px-2 py-0.5 rounded shadow-sm">{{ $totalBrigade }} ЧЕЛ.</span>
                                    </div>
                                    <h3 class="text-lg uppercase fio-row">
                                        <a href="{{ route('employees.show', $brigadier->id) }}" class="fio-brigadier-blue">
                                            {{ $brigadier->last_name }} {{ $brigadier->first_name }} {{ $brigadier->middle_name }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="flex items-center gap-2 text-slate-300">
                                    <i class="fas fa-grip-lines cursor-grab mr-2"></i>
                                    @if(!$isVacation)
                                        <button onclick="openSubModal({{ $brigadier->id }})" class="hover:text-rose-500"><i class="fas fa-plane-departure text-sm"></i></button>
                                    @endif
                                </div>
                            </div>
                            <div class="location-box">
                                <input type="text" value="{{ $location->location_name ?? '' }}" onblur="saveLocation({{ $brigadier->id }}, this.value)" class="location-input" placeholder="УЧАСТОК НЕ УКАЗАН">
                            </div>
                        </div>

                        <div class="p-6 flex-grow overflow-y-auto max-h-[350px]">
                            <ul class="space-y-1">
                                @forelse($workers->sortBy('last_name') as $worker)
                                    <li class="flex justify-between items-center group py-0.5 px-1 hover:bg-slate-50 rounded-lg js-worker-item" data-worker-id="{{ $worker->id }}">
                                        <div class="text-[11px] uppercase flex items-center min-w-0">
                                            <span class="text-blue-600 mr-2 font-black text-[10px]">{{ $loop->iteration }}.</span>
                                            {{-- ИСПРАВЛЕНО: Добавлена ссылка на карточку сотрудника --}}
                                            <a href="{{ route('employees.show', $worker->id) }}" class="fio-standard-black">
                                                {{ $worker->last_name }} {{ $worker->first_name }} {{ $worker->middle_name }}
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="openMoveModal({{ $worker->id }}, '{{ $worker->last_name }} {{ $worker->first_name }} {{ $worker->middle_name }}')" class="text-slate-400 hover:text-indigo-600"><i class="fas fa-exchange-alt text-[10px]"></i></button>
                                            <form action="{{ route('brigades.update-leader') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="employee_id" value="{{ $worker->id }}"><input type="hidden" name="parent_id" value="">
                                                <button type="submit" class="text-slate-200 hover:text-red-500"><i class="fas fa-times-circle text-[10px]"></i></button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-[10px] text-slate-400 uppercase font-bold text-center py-6">Нет людей</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="p-6 bg-slate-50 border-t mt-auto">
                            <button onclick="openAssignModal({{ $brigadier->id }})" @if($isVacation) disabled @endif class="w-full bg-slate-900 text-white text-[10px] font-black uppercase py-4 rounded-xl hover:bg-slate-800 shadow-lg">
                                + Добавить рабочего
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</main>

@include('brigades.modals')

<footer class="indent-area pb-8">
    @include('layouts.footer')
</footer>

<script>
    function saveLocation(id, val) {
        fetch('{{ route("brigades.update-location") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ brigadier_id: id, location_name: val })
        });
    }

    function restoreOrder() {
        document.querySelectorAll('.js-sortable-grid').forEach(container => {
            const masterId = container.getAttribute('data-master-id');
            const savedOrder = localStorage.getItem('order_master_' + masterId);
            if (savedOrder) {
                const orderArray = JSON.parse(savedOrder);
                const cards = Array.from(container.children);
                orderArray.forEach(id => {
                    const card = cards.find(c => c.getAttribute('data-id') == id);
                    if (card) container.appendChild(card);
                });
            }
        });
    }

    document.querySelectorAll('.js-sortable-grid').forEach(container => {
        new Sortable(container, {
            animation: 250,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            handle: '.p-8',
            onEnd: function () {
                const masterId = container.getAttribute('data-master-id');
                const currentOrder = Array.from(container.children).map(c => c.getAttribute('data-id'));
                localStorage.setItem('order_master_' + masterId, JSON.stringify(currentOrder));
            },
        });
    });

    document.addEventListener('DOMContentLoaded', restoreOrder);

    function openAssignModal(brigId) {
        document.getElementById('modalBrigId').value = brigId;
        const busyIds = Array.from(document.querySelectorAll('.js-worker-item')).map(el => el.getAttribute('data-worker-id'));
        const select = document.getElementById('employeeSelect');
        const options = select.querySelectorAll('.worker-option');
        options.forEach(opt => {
            if (busyIds.includes(opt.value)) { opt.style.display = 'none'; opt.disabled = true; }
            else { opt.style.display = 'block'; opt.disabled = false; }
        });
        select.value = "";
        document.getElementById('assignModal').classList.replace('hidden', 'flex');
    }
    function closeAssignModal() { document.getElementById('assignModal').classList.replace('flex', 'hidden'); }
    function openMoveModal(id, name) {
        document.getElementById('moveWorkerId').value = id;
        document.getElementById('moveWorkerName').innerText = name;
        document.getElementById('moveModal').classList.replace('hidden', 'flex');
    }
    function closeMoveModal() { document.getElementById('moveModal').classList.replace('flex', 'hidden'); }
    function openSubModal(id) { document.getElementById('subBrigId').value = id; document.getElementById('subModal').classList.replace('hidden', 'flex'); }
    function closeSubModal() { document.getElementById('subModal').classList.replace('flex', 'hidden'); }
</script>
</body>
</html>
