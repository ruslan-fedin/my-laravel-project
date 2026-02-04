@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white shadow-md rounded-lg border">
    <div class="bg-gray-100 px-6 py-4 border-b">
        <h2 class="text-sm font-bold uppercase text-gray-700">Новый статус</h2>
    </div>

    <form action="{{ route('statuses.store') }}" method="POST" class="p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Название статуса</label>
                {{-- Убрали класс uppercase, чтобы пользователь видел реальный ввод --}}
                <input type="text" name="name" required placeholder="Например: Больничный"
                       class="w-full border rounded p-2 text-sm font-medium outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Код (1-2 буквы)</label>
                <input type="text" name="short_name" required maxlength="3" placeholder="Б"
                       class="w-full border rounded p-2 text-sm font-bold text-center outline-none border-blue-300 uppercase">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Цвет в табеле</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" value="#3b82f6"
                       class="h-10 w-20 cursor-pointer border-none bg-transparent">
                <span class="text-[10px] text-gray-400 font-medium italic">Выберите цвет отображения в сетке</span>
            </div>
        </div>

        <div class="pt-4 flex justify-between items-center border-t">
            <a href="{{ route('statuses.index') }}" class="text-gray-500 text-[10px] font-bold uppercase hover:underline">Отмена</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-xs font-bold uppercase hover:bg-blue-700 transition">
                Сохранить <i class="fas fa-check ml-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection
