@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-md rounded-lg border">
    <div class="bg-gray-100 px-6 py-4 border-b">
        <h2 class="text-sm font-bold uppercase text-gray-700">Редактировать статус</h2>
    </div>

    <form action="{{ route('statuses.update', $status) }}" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Название</label>
                <input type="text" name="name" value="{{ $status->name }}" required
                       class="w-full border rounded p-2 text-sm font-bold outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Код</label>
                <input type="text" name="short_name" value="{{ $status->short_name }}" required maxlength="3"
                       class="w-full border rounded p-2 text-sm font-bold text-center outline-none border-blue-300">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Цвет в табеле</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" value="{{ $status->color }}"
                       class="h-10 w-20 cursor-pointer border-none bg-transparent">
                <span class="text-[10px] text-gray-400 font-medium">Текущий цвет отображения</span>
            </div>
        </div>

        <div class="pt-4 flex justify-between items-center border-t">
            <a href="{{ route('statuses.index') }}" class="text-gray-500 text-[10px] font-bold uppercase hover:underline">Отмена</a>
            <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded text-xs font-bold uppercase hover:bg-orange-600 transition">
                Обновить данные <i class="fas fa-sync-alt ml-2"></i>
            </button>
        </div>
    </form>
        </div>

{{-- ПОДКЛЮЧЕНИЕ ПОДВАЛА ЧЕРЕЗ BLADE ШАБЛОН --}}
    <div class="mt-8">
@include('layouts.footer')
    </div>

</div> {{-- ЗАКРЫВАЮЩИЙ ТЕГ ОСНОВНОГО КОНТЕЙНЕРА (если он был открыт в начале страницы или в макете) --}}
@endsection
