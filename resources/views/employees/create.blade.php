@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden border">
    <div class="bg-gray-100 px-6 py-4 border-b">
        <h2 class="text-sm font-bold uppercase text-gray-700">Регистрация нового сотрудника</h2>
    </div>

    <form action="{{ route('employees.store') }}" method="POST" class="p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Фамилия</label>
                <input type="text" name="last_name" required placeholder="Иванов" class="w-full border rounded p-2 text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Имя</label>
                <input type="text" name="first_name" required placeholder="Иван" class="w-full border rounded p-2 text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Отчество</label>
                <input type="text" name="middle_name" placeholder="Иванович" class="w-full border rounded p-2 text-sm font-medium">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Дата рождения</label>
                <input type="date" name="birth_date" class="w-full border rounded p-2 text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Дата приема</label>
                <input type="date" name="hire_date" class="w-full border rounded p-2 text-sm font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Телефон</label>
                <input type="text" name="phone" placeholder="+7..." class="w-full border rounded p-2 text-sm font-medium">
            </div>
        </div>

        <div class="border-t pt-4">
            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Должность</label>
            <select name="position_id" required class="w-full border rounded p-2 text-sm font-medium bg-white">
                <option value="">-- Выберите должность --</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="is_active" value="1">
        <div class="pt-6 flex justify-between items-center border-t">
            <a href="{{ route('employees.index') }}" class="text-gray-500 text-[10px] font-bold uppercase">Отмена</a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded text-xs font-bold uppercase hover:bg-blue-700 transition">
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
