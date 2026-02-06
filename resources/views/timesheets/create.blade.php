@extends('layouts.app')

@section('content')
<div class="px-6"> {{-- Основной отступ страницы слева и справа --}}

    <div class="relative min-h-[80vh] flex flex-col items-center justify-center">

        {{-- Кнопка НАЗАД: Справа вверху --}}
        <div class="absolute top-0 right-0 mt-2">
            <a href="{{ route('timesheets.index') }}"
               class="bg-slate-900 text-white px-5 py-2 rounded text-[10px] font-black uppercase tracking-widest shadow-xl hover:bg-black transition inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Назад в архив
            </a>
        </div>

        {{-- Карточка создания табеля --}}
        <div class="w-full max-w-md bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden mt-12">
            {{-- Заголовок с серым фоном --}}
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-[10px] font-black uppercase text-slate-700 tracking-widest text-center">
                    Параметры нового отчетного периода
                </h2>
            </div>

            <form action="{{ route('timesheets.store') }}" method="POST" class="p-8">
                @csrf

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                            Дата начала:
                        </label>
                        <input type="date" name="start_date" required
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                               class="w-full border border-slate-200 rounded-lg px-3 py-3 text-xs font-bold focus:outline-none focus:border-slate-900 bg-white uppercase">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">
                            Дата окончания:
                        </label>
                        <input type="date" name="end_date" required
                               value="{{ now()->endOfMonth()->format('Y-m-d') }}"
                               class="w-full border border-slate-200 rounded-lg px-3 py-3 text-xs font-bold focus:outline-none focus:border-slate-900 bg-white uppercase">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-lg text-[10px] font-black uppercase hover:bg-black transition shadow-lg tracking-widest">
                        СФОРМИРОВАТЬ ТАБЕЛЬ НА ПЕРИОД
                    </button>
                </div>
            </form>
        </div>

        {{-- Футер внутри общего контейнера --}}
        <div class="w-full mt-auto pt-12">
            @include('layouts.footer')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Здесь можно добавить валидацию дат, если потребуется
</script>
@endsection
