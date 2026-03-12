<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление структурой ЗХ</title>

    <script src="{{ asset('vendor/tailwind.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/inter.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Инденты по 120px */
        .indent-area { padding-left: 120px; padding-right: 120px; }
        @media (max-width: 1024px) { .indent-area { padding-left: 20px; padding-right: 20px; } }

        /* Настройка шрифтов (сделано мельче) */
        .master-title-text { font-size: 18px !important; }

        .fio-brigadier-blue {
            color: #2563eb !important;
            font-weight: 800;
            text-decoration: none;
            font-size: 13px !important;
            line-height: 1.2;
        }
        .fio-brigadier-blue:hover { text-decoration: underline !important; }

        .fio-standard-black {
            color: #0f172a !important;
            font-weight: 800;
            text-decoration: none;
            font-size: 10px !important;
            line-height: 1.1;
            display: inline; /* Возвращаем стандартное поведение */
        }
        .fio-standard-black:hover { color: #2563eb !important; text-decoration: underline !important; }

        .brigade-card { border: 1px solid #e2e8f0; background: white; border-radius: 20px; position: relative; height: 100%; display: flex; flex-direction: column; overflow: hidden; }

        .drag-handle-zone { cursor: grab; }
        .drag-handle-zone:active { cursor: grabbing; }
        .sortable-ghost { opacity: 0.1 !important; background: #2563eb !important; }

        .location-box { background-color: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 6px; margin-top: 8px; }
        .location-input { color: #dc2626; font-weight: 800; text-transform: uppercase; width: 100%; background: transparent; outline: none; text-align: center; font-size: 10px; }
        .vacation-overlay { position: absolute; inset: 0; display: flex; items: center; justify-content: center; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(4px); z-index: 50; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

@include('layouts.navigation')

<main class="py-12 indent-area flex-grow">
    @foreach($masters as $master)
        <div class="mb-16">
            <div class="bg-slate-900 rounded-[2rem] p-7 text-white mb-8 flex justify-between items-center shadow-xl">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg"><i class="fas fa-user-shield text-lg"></i></div>
                    <div>
                        <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Мастер участка</span>
                        <h2 class="master-title-text font-black uppercase">
                            {{ $master->last_name }} {{ $master->first_name }} {{ $master->middle_name }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 js-sortable-grid" data-master-id="{{ $master->id }}">
                @foreach($master->subordinates as $brigadier)
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
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black uppercase text-[9px]">Вернуть</button>
                                </form>
                            </div>
                        @endif

                        <div class="p-6 border-b drag-handle-zone">
                            <div class="flex justify-between items-start">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[8px] font-black text-slate-400 uppercase">{{ $brigadier->position->name ?? 'Бригадир' }}</span>
                                        <span class="bg-blue-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded">{{ $totalBrigade }} ЧЕЛ.</span>
                                    </div>
                                    {{-- Ссылка бригадира --}}
 {{-- Ссылка бригадира: ведем сразу на EDIT --}}
<a href="{{ url('/employees/' . $brigadier->id . '/edit') }}" data-turbo="false" class="fio-brigadier-blue uppercase">
    {{ $brigadier->last_name }} {{ $brigadier->first_name }} {{ $brigadier->middle_name }}
</a>
                               </div>
                                <div class="flex items-center gap-2 text-slate-300">
                                    @if(!$isVacation)
                                        <button onclick="openSubModal({{ $brigadier->id }})" class="hover:text-rose-500 transition-colors">
                                            <i class="fas fa-plane-departure text-xs"></i>
                                        </button>
                                    @endif
                                    <i class="fas fa-grip-lines text-lg"></i>
                                </div>
                            </div>
                            <div class="location-box">
                                <input type="text" value="{{ $location->location_name ?? '' }}" onblur="saveLocation({{ $brigadier->id }}, this.value)" class="location-input" placeholder="УЧАСТОК НЕ УКАЗАН">
                            </div>
                        </div>

                        <div class="p-4 flex-grow overflow-y-auto max-h-[300px]">
                            <ul class="space-y-1">
                                @forelse($workers->sortBy('last_name') as $worker)
                                    <li class="flex justify-between items-center group py-0.5 px-1 hover:bg-slate-50 rounded js-worker-item" data-worker-id="{{ $worker->id }}">
                                        <div class="min-w-0 flex-1">
                                            <span class="text-blue-600 mr-1 font-black text-[9px]">{{ $loop->iteration }}.</span>
                                            {{-- Ссылка рабочего: используем url() для надежности --}}
{{-- Ссылка рабочего: ведем сразу на EDIT --}}
<a href="{{ url('/employees/' . $worker->id . '/edit') }}" data-turbo="false" class="fio-standard-black uppercase">
    {{ $worker->last_name }} {{ $worker->first_name }} {{ $worker->middle_name }}
</a>
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 ml-2">
                                            <button onclick="openMoveModal({{ $worker->id }}, '{{ $worker->last_name }} {{ $worker->first_name }} {{ $worker->middle_name }}')" class="text-slate-400 hover:text-indigo-600">
                                                <i class="fas fa-exchange-alt text-[9px]"></i>
                                            </button>
                                            <form action="{{ route('brigades.update-leader') }}" method="POST" onsubmit="return confirm('Убрать?')">
                                                @csrf
                                                <input type="hidden" name="employee_id" value="{{ $worker->id }}">
                                                <input type="hidden" name="parent_id" value="">
                                                <button type="submit" class="text-slate-300 hover:text-rose-500">
                                                    <i class="fas fa-times-circle text-[9px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-[9px] text-slate-400 uppercase font-bold text-center py-4">Пусто</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="p-4 bg-slate-50 border-t mt-auto">
                            <button onclick="openAssignModal({{ $brigadier->id }})" @if($isVacation) disabled @endif class="w-full bg-slate-900 text-white text-[9px] font-black uppercase py-3 rounded-lg hover:bg-slate-800 shadow-md">
                                + Добавить
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</main>

@include('brigades.modals')

<footer class="indent-area pb-8">@include('layouts.footer')</footer>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    function openAssignModal(brigId) {
        document.getElementById('modalBrigId').value = brigId;
        const busyIds = Array.from(document.querySelectorAll('.js-worker-item')).map(el => el.getAttribute('data-worker-id'));
        const select = document.getElementById('employeeSelect');
        const options = select.querySelectorAll('.worker-option');
        options.forEach(opt => {
            if (busyIds.includes(opt.value)) { opt.style.display = 'none'; opt.disabled = true; }
            else { opt.style.display = 'block'; opt.disabled = false; }
        });
        document.getElementById('assignModal').classList.replace('hidden', 'flex');
    }
    function closeAssignModal() { document.getElementById('assignModal').classList.replace('flex', 'hidden'); }

    function openMoveModal(id, name) {
        document.getElementById('moveWorkerId').value = id;
        document.getElementById('moveWorkerName').innerText = name;
        document.getElementById('moveModal').classList.replace('hidden', 'flex');
    }
    function closeMoveModal() { document.getElementById('moveModal').classList.replace('flex', 'hidden'); }

    function openSubModal(id) {
        document.getElementById('subBrigId').value = id;
        document.getElementById('subModal').classList.replace('hidden', 'flex');
    }
    function closeSubModal() { document.getElementById('subModal').classList.replace('flex', 'hidden'); }

    function saveLocation(id, val) {
        fetch('{{ route("brigades.update-location") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ brigadier_id: id, location_name: val })
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
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

            new Sortable(container, {
                animation: 250,
                handle: '.drag-handle-zone',
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    const currentOrder = Array.from(container.children).map(c => c.getAttribute('data-id'));
                    localStorage.setItem('order_master_' + masterId, JSON.stringify(currentOrder));
                }
            });
        });
    });
</script>
</body>
</html>
