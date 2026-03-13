@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-emerald-50 p-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900">📊 План посадок</h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">Планирование посадок цветов по районам</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('planting-plans.create') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-emerald-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Добавить план</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
        <span class="font-bold text-emerald-700">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-rose-600 text-xl"></i>
        <span class="font-bold text-rose-700">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form action="{{ route('planting-plans.update-rate') }}" method="POST" class="flex items-center gap-4">
            @csrf
            <label class="text-sm font-bold text-slate-700">🌱 Норма посадки:</label>
            <input type="number" name="planting_rate" value="{{ $defaultRate }}" min="1"
                   class="w-24 px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-emerald-500 text-center font-bold">
            <span class="text-sm text-slate-500">шт/м²</span>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition text-sm">
                💾 Применить ко всем
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">№</th>
                        <th class="text-left py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Наименование</th>
                        <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Клумб</th>
                        <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Площадь (м²)</th>
                        <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Норма</th>
                        <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Кол-во</th>
                        <th class="text-center py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($plans as $index => $plan)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-4 px-4 text-sm font-bold text-slate-700">{{ $index + 1 }}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="togglePlanDetails({{ $plan->id }})"
                                        class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition">
                                    <i class="fas fa-chevron-right text-xs" id="chevron-{{ $plan->id }}"></i>
                                </button>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $plan->name }}</div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        👤 {{ $plan->createdBy->name ?? '—' }} |
                                        📅 {{ $plan->created_at->format('d.m.Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ $plan->flowerBeds->count() }}</span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ number_format($plan->area, 2, ',', ' ') }}</span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ $plan->planting_rate }}</span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-bold text-emerald-600">{{ number_format($plan->total_quantity, 0, ',', ' ') }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('planting-plans.edit', $plan->id) }}"
                                   class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center hover:bg-indigo-200 transition">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('planting-plans.destroy', $plan->id) }}" method="POST"
                                      onsubmit="return confirm('Удалить план посадок?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center hover:bg-rose-200 transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- ДЕТАЛИ ПЛАНА (скрыто по умолчанию) --}}
                    <tr id="plan-details-{{ $plan->id }}" class="hidden bg-slate-50">
                        <td colspan="7" class="py-4 px-4">
                            <div class="border-2 border-slate-200 rounded-xl p-4 bg-white">
                                <h4 class="font-bold text-slate-700 mb-4">🌸 Детали по клумбам</h4>
                                <div class="space-y-3">
                                    @foreach($plan->flowerBeds as $bed)
                                    <div class="border border-slate-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-bold text-slate-900">{{ $bed->full_name }}</div>
                                            <div class="text-sm text-slate-500">{{ number_format($bed->area, 2, ',', ' ') }} м²</div>
                                        </div>

                                        @php
                                            $bedColors = $plan->bedColors->where('flower_bed_id', $bed->id);
                                            $bedTotal = $bedColors->sum('quantity');
                                        @endphp

                                        <div class="flex flex-wrap gap-2">
                                            @foreach($bedColors as $color)
                                            @if($color->quantity > 0)
                                            @php $colorData = \App\Models\PlantingPlanBedColor::$colorTypes[$color->color_type] ?? null; @endphp
                                            @if($colorData)
                                            @if($color->color_type === 'white')
                                            <span class="text-xs px-2 py-1 rounded font-bold border-2 border-slate-300 bg-slate-100 text-slate-700 shadow-sm">
                                                {{ $colorData['icon'] }} {{ number_format($color->quantity, 0, ',', ' ') }}
                                            </span>
                                            @else
                                            <span class="text-xs px-2 py-1 rounded font-bold" style="background: {{ $colorData['hex'] }}20; color: {{ $colorData['hex'] }}">
                                                {{ $colorData['icon'] }} {{ number_format($color->quantity, 0, ',', ' ') }}
                                            </span>
                                            @endif
                                            @endif
                                            @endif
                                            @endforeach
                                        </div>

                                        <div class="mt-2 text-xs text-slate-500 text-right">
                                            Итого: <span class="font-bold text-emerald-600">{{ number_format($bedTotal, 0, ',', ' ') }} шт.</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-700">📊 Итого по плану:</span>
                                        <div class="flex flex-wrap gap-2">
                                            @php
                                                $planColors = [];
                                                foreach(\App\Models\PlantingPlanBedColor::$colorTypes as $type => $data) {
                                                    $qty = $plan->bedColors()->where('color_type', $type)->sum('quantity');
                                                    if($qty > 0) {
                                                        $planColors[] = ['data' => $data, 'qty' => $qty, 'type' => $type];
                                                    }
                                                }
                                            @endphp
                                            @foreach($planColors as $color)
                                            @if($color['type'] === 'white')
                                            <span class="text-xs px-2 py-1 rounded font-bold border-2 border-slate-300 bg-slate-100 text-slate-700 shadow-sm">
                                                {{ $color['data']['icon'] }} {{ number_format($color['qty'], 0, ',', ' ') }}
                                            </span>
                                            @else
                                            <span class="text-xs px-2 py-1 rounded font-bold" style="background: {{ $color['data']['hex'] }}20; color: {{ $color['data']['hex'] }}">
                                                {{ $color['data']['icon'] }} {{ number_format($color['qty'], 0, ',', ' ') }}
                                            </span>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-emerald-50 border-t-2 border-emerald-200">
                    <tr>
                        <td colspan="2" class="py-4 px-4 text-right font-black text-slate-700">ИТОГО:</td>
                        <td class="py-4 px-4 text-center font-black text-slate-700">—</td>
                        <td class="py-4 px-4 text-center font-black text-emerald-700">{{ number_format($totals['area'], 2, ',', ' ') }}</td>
                        <td class="py-4 px-4 text-center font-black text-slate-700">—</td>
                        <td class="py-4 px-4 text-center font-black text-emerald-700">{{ number_format($totals['quantity'], 0, ',', ' ') }}</td>
                        <td class="py-4 px-4 text-center font-black text-slate-700">—</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<script>
function togglePlanDetails(planId) {
    const detailsRow = document.getElementById('plan-details-' + planId);
    const chevron = document.getElementById('chevron-' + planId);

    if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
        chevron.classList.remove('fa-chevron-right');
        chevron.classList.add('fa-chevron-down');
    } else {
        detailsRow.classList.add('hidden');
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-right');
    }
}
</script>

@endsection
