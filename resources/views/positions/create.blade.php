@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-md rounded-lg border">
    <div class="bg-gray-100 px-6 py-4 border-b">
        <h2 class="text-sm font-bold uppercase text-gray-700">Новая должность</h2>
    </div>

    <form action="{{ route('positions.store') }}" method="POST" class="p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Наименование должности</label>
            <input type="text" name="name" required placeholder="Например: Менеджер"
                   class="w-full border rounded p-2 text-sm font-medium outline-none focus:border-blue-500">
            <p class="text-[9px] text-gray-400 mt-1 italic">Система автоматически сделает первую букву заглавной</p>
        </div>

        <div class="pt-4 flex justify-between items-center border-t">
            <a href="{{ route('positions.index') }}" class="text-gray-500 text-[10px] font-bold uppercase hover:underline">Отмена</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-xs font-bold uppercase hover:bg-blue-700 transition">
                Сохранить <i class="fas fa-save ml-2"></i>
            </button>
        </div>
    </form>
        </div>
{{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА ЧЕРЕЗ BLADE ШАБЛОН --}}
    <div class="mt-8">
        @include('partials.footer')
    </div>

</div> {{-- ЗАКРЫВАЮЩИЙ ТЕГ ОСНОВНОГО КОНТЕЙНЕРА (если он был открыт в начале страницы или в макете) --}}
@endsection
