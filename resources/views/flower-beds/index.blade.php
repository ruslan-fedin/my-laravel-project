@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    {{-- ЗАГОЛОВОК --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">🌸 Клумбы</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">Управление объектами озеленения</p>
        </div>
        <a href="{{ route('flower-beds.create') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg shadow-emerald-200">
            <i class="fas fa-plus"></i>
            <span>Добавить клумбу</span>
        </a>
    </div>

    {{-- УВЕДОМЛЕНИЯ --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

    {{-- 🔥 ЖИВОЙ ПОИСК --}}
    <div class="bg-white rounded-2xl p-6 shadow-lg mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="liveSearch"
                   placeholder="🔍 Живой поиск: название, район, адрес..."
                   class="w-full pl-12 pr-4 py-4 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-lg"
                   autocomplete="off">
            <span id="searchCount" class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></span>
        </div>
        <div id="searchResults" class="hidden absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 max-h-96 overflow-y-auto"></div>
    </div>

    {{-- 🔥 ФИЛЬТРЫ --}}
    <div class="bg-white rounded-2xl p-4 shadow-lg mb-6 border border-slate-100">
        <form action="{{ route('flower-beds.index') }}" method="GET" class="flex flex-wrap items-center gap-3" id="filterForm">

            {{-- Район --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Район:</span>
                <select name="district" class="px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm min-w-[140px]">
                    <option value="">Все</option>
                    @foreach($districts ?? [] as $district)
                    <option value="{{ $district }}" {{ request('district') === $district ? 'selected' : '' }}>
                        {{ $district }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Тип --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Тип:</span>
                <select name="perennial" class="px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm min-w-[160px]">
                    <option value="">Все</option>
                    <option value="1" {{ request('perennial') === '1' ? 'selected' : '' }}>🌿 Многолетка</option>
                    <option value="0" {{ request('perennial') === '0' ? 'selected' : '' }}>🌸 Однолетка</option>
                </select>
            </div>

            {{-- Статус --}}
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Статус:</span>
                <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-sm min-w-[120px]">
                    <option value="">Все</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>✅ Активные</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>❌ Неактивные</option>
                </select>
            </div>

            {{-- Кнопки --}}
            <div class="flex items-center gap-2 ml-auto">
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-emerald-700 transition">
                    Применить
                </button>
                <a href="{{ route('flower-beds.index') }}" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-slate-200 transition" title="Сбросить фильтры" id="resetFilters">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>

        {{-- Активные фильтры --}}
        @if(request('district') || request('perennial') === '0' || request('perennial') === '1' || request('status') === '0' || request('status') === '1')
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap gap-2">
            <span class="text-xs text-slate-500">Фильтры:</span>
            @if(request('district'))
            <span class="inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg border border-indigo-200">
                📍 {{ request('district') }}
                <a href="{{ request()->fullUrlWithQuery(['district' => null]) }}" class="text-indigo-400 hover:text-indigo-600 font-bold">×</a>
            </span>
            @endif
            @if(request('perennial') === '0' || request('perennial') === '1')
            <span class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg border border-amber-200">
                {{ request('perennial') === '1' ? '🌿 Многолетка' : '🌸 Однолетка' }}
                <a href="{{ request()->fullUrlWithQuery(['perennial' => null]) }}" class="text-amber-400 hover:text-amber-600 font-bold">×</a>
            </span>
            @endif
            @if(request('status') === '0' || request('status') === '1')
            <span class="inline-flex items-center gap-1 text-xs bg-slate-50 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200">
                {{ request('status') === '1' ? '✅ Активные' : '❌ Неактивные' }}
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-slate-400 hover:text-slate-600 font-bold">×</a>
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- 🔥 ТАБЛИЦА --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-x-auto">
        <table class="w-full" id="flowerBedsTable">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-12">№</th>
                    <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Название</th>
                    <th class="text-left py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Район</th>
                    <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-40">Тип</th>
                    <th class="text-right py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest w-32">Площадь</th>
                    <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">Статус</th>
                    <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">Файлов</th>
                    <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-28">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="tableBody">
                @forelse($flowerBeds as $index => $bed)
                <tr class="hover:bg-slate-50 transition-colors">
                    {{-- НОМЕР --}}
                    <td class="py-4 px-4 text-center">
                        <span class="text-sm font-bold text-slate-500">
                            {{ $flowerBeds->firstItem() + $index }}
                        </span>
                    </td>

                    {{-- НАЗВАНИЕ (БЕЗ ОБРЕЗКИ) --}}
                    <td class="py-4 px-6">
                        <div class="font-black text-slate-900">{{ $bed->short_name }}</div>
                        <div class="text-xs font-bold text-slate-600">{{ $bed->full_name }}</div>
                    </td>

                    {{-- РАЙОН (Жирный) --}}
                    <td class="py-4 px-6">
                        <span class="text-sm font-bold text-slate-700">{{ $bed->district ?? '—' }}</span>
                    </td>

                    {{-- ТИП (Прописью) --}}
                    <td class="py-4 px-4 text-center">
                        @if($bed->is_perennial)
                        <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">🌿 Многолетка</span>
                        @else
                        <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">🌸 Однолетка</span>
                        @endif
                    </td>

                    {{-- ПЛОЩАДЬ --}}
                    <td class="py-4 px-6 text-right">
                        <span class="text-sm font-bold text-slate-700">{{ number_format($bed->area, 2) }}</span>
                        <span class="text-xs text-slate-400 ml-1">м²</span>
                    </td>

                    {{-- СТАТУС --}}
                    <td class="py-4 px-4 text-center">
                        @if($bed->is_active)
                        <span class="text-xs font-bold px-2 py-1 rounded bg-emerald-100 text-emerald-700">✅</span>
                        @else
                        <span class="text-xs font-bold px-2 py-1 rounded bg-slate-100 text-slate-400">❌</span>
                        @endif
                    </td>

                    {{-- ФАЙЛЫ --}}
                    <td class="py-4 px-4 text-center">
                        <span class="text-sm font-bold text-slate-700">{{ $bed->files->count() }}</span>
                    </td>

                    {{-- ДЕЙСТВИЯ --}}
                    <td class="py-4 px-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('flower-beds.show', $bed->id) }}" class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('flower-beds.edit', $bed->id) }}" class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center hover:bg-indigo-200 transition">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <form action="{{ route('flower-beds.destroy', $bed->id) }}" method="POST" onsubmit="return confirm('Удалить клумбу?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center text-slate-400">
                        <i class="fas fa-seedling text-5xl mb-4"></i>
                        <p class="font-bold text-lg">Нет клумб по выбранным фильтрам</p>
                        <p class="text-sm mt-2">Попробуйте изменить параметры поиска</p>
                        <a href="{{ route('flower-beds.index') }}" class="inline-block mt-4 bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-emerald-700 transition">
                            Сбросить фильтры
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>

            {{-- 🔥 ИТОГИ --}}
            @if($flowerBeds->total() > 0)
            <tfoot class="border-t-2 border-slate-300 bg-slate-50">
                <tr>
                    <td colspan="3" class="py-3 px-6">
                        <span class="text-xs font-black text-slate-500 uppercase tracking-wider">
                            Всего: <span class="text-slate-900 text-sm">{{ $flowerBeds->total() }}</span>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">
                                🌿 {{ $flowerBeds->where('is_perennial', true)->count() }}
                            </span>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100">
                                🌸 {{ $flowerBeds->where('is_perennial', false)->count() }}
                            </span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-right">
                        <span class="text-base font-black text-slate-900">
                            {{ number_format($flowerBeds->sum('area'), 2) }}
                            <span class="text-xs font-bold text-slate-500">м²</span>
                        </span>
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- ПАГИНАЦИЯ --}}
    @if($flowerBeds->hasPages())
    <div class="mt-6 flex justify-center" id="paginationSection">
        {{ $flowerBeds->links() }}
    </div>
    @endif

</div>

{{-- 🔥 СКРИПТ --}}
<script>
let searchTimeout = null;

document.getElementById('liveSearch').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    document.getElementById('searchCount').textContent = query ? '🔍' : '';
    if (searchTimeout) clearTimeout(searchTimeout);
    if (!query) {
        document.getElementById('searchResults').classList.add('hidden');
        document.getElementById('paginationSection').style.display = 'block';
        location.reload();
        return;
    }
    searchTimeout = setTimeout(() => { fetchLiveSearch(query); }, 300);
});

async function fetchLiveSearch(query) {
    try {
        const response = await fetch(`/flower-beds/search?query=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await response.json();
        renderSearchResults(data.results, query);
    } catch (error) {
        console.error('Ошибка поиска:', error);
        document.getElementById('searchResults').innerHTML = '<div class="p-4 text-rose-600">❌ Ошибка поиска</div>';
        document.getElementById('searchResults').classList.remove('hidden');
    }
}

function renderSearchResults(results, query) {
    const container = document.getElementById('searchResults');
    const tableBody = document.getElementById('tableBody');
    const pagination = document.getElementById('paginationSection');

    if (pagination) pagination.style.display = 'none';

    if (results.length === 0) {
        container.innerHTML = `<div class="p-4 text-slate-500">Не найдено по запросу "${query}"</div>`;
        container.classList.remove('hidden');
        tableBody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400">Ничего не найдено</td></tr>`;
        return;
    }

    container.innerHTML = results.map(bed => `
        <a href="${bed.url_show}" class="block p-4 hover:bg-emerald-50 border-b border-slate-100 last:border-0 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold text-slate-900">${escapeHtml(bed.short_name)}</div>
                    <div class="text-xs font-bold text-slate-600">${escapeHtml(bed.full_name)}</div>
                    <div class="text-xs text-slate-400 mt-1">
                        ${bed.district ? '📍 ' + escapeHtml(bed.district) + ' • ' : ''}
                        ${bed.is_perennial ? '🌿 Многолетка' : '🌸 Однолетка'} • ${bed.area} м²
                    </div>
                </div>
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
        </a>
    `).join('');
    container.classList.remove('hidden');

    tableBody.innerHTML = results.map((bed, idx) => `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-4 px-4 text-center"><span class="text-sm font-bold text-slate-500">${idx + 1}</span></td>
            <td class="py-4 px-6">
                <div class="font-black text-slate-900">${escapeHtml(bed.short_name)}</div>
                <div class="text-xs font-bold text-slate-600">${escapeHtml(bed.full_name)}</div>
            </td>
            <td class="py-4 px-6"><span class="text-sm font-bold text-slate-700">${bed.district ? escapeHtml(bed.district) : '—'}</span></td>
            <td class="py-4 px-4 text-center">
                ${bed.is_perennial
                    ? '<span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">🌿 Многолетка</span>'
                    : '<span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">🌸 Однолетка</span>'}
            </td>
            <td class="py-4 px-6 text-right"><span class="text-sm font-bold text-slate-700">${bed.area} м²</span></td>
            <td class="py-4 px-4 text-center">
                ${bed.is_active
                    ? '<span class="text-xs font-bold px-2 py-1 rounded bg-emerald-100 text-emerald-700">✅</span>'
                    : '<span class="text-xs font-bold px-2 py-1 rounded bg-slate-100 text-slate-400">❌</span>'}
            </td>
            <td class="py-4 px-4 text-center"><span class="text-sm font-bold text-slate-700">${bed.files_count}</span></td>
            <td class="py-4 px-4">
                <div class="flex items-center justify-center gap-1">
                    <a href="${bed.url_show}" class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition"><i class="fas fa-eye text-xs"></i></a>
                    <a href="${bed.url_edit}" class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center hover:bg-indigo-200 transition"><i class="fas fa-pen text-xs"></i></a>
                </div>
            </td>
        </tr>
    `).join('');

    const totalArea = results.reduce((sum, b) => sum + parseFloat(b.area), 0);
    const perennialCount = results.filter(b => b.is_perennial).length;
    const annualCount = results.length - perennialCount;

    document.getElementById('searchCount').textContent = results.length;

    const tfoot = document.querySelector('#flowerBedsTable tfoot');
    if (tfoot) {
        const cells = tfoot.querySelectorAll('td');
        cells[0].innerHTML = `<span class="text-xs font-black text-slate-500 uppercase tracking-wider">Найдено: <span class="text-slate-900 text-sm">${results.length}</span></span>`;
        cells[1].innerHTML = `<div class="flex items-center justify-center gap-3"><span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">🌿 ${perennialCount}</span><span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100">🌸 ${annualCount}</span></div>`;
        cells[2].innerHTML = `<span class="text-base font-black text-slate-900">${totalArea.toFixed(2)} <span class="text-xs font-bold text-slate-500">м²</span></span>`;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('click', function(e) {
    const searchBox = document.getElementById('liveSearch');
    const results = document.getElementById('searchResults');
    if (!searchBox.contains(e.target) && !results.contains(e.target)) {
        results.classList.add('hidden');
    }
});

document.getElementById('resetFilters').addEventListener('click', function() {
    document.getElementById('liveSearch').value = '';
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('paginationSection').style.display = 'block';
});

document.getElementById('liveSearch').addEventListener('keydown', function(e) {
    const results = document.getElementById('searchResults');
    const links = results.querySelectorAll('a');
    if (e.key === 'ArrowDown' && links.length > 0) { e.preventDefault(); links[0].focus(); }
});
</script>
@endsection
