@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden border">
    <div class="bg-gray-100 px-6 py-4 border-b text-gray-700">
        <h2 class="text-sm font-bold uppercase tracking-tight">Редактировать данные сотрудника</h2>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Фамилия</label>
                <input type="text" name="last_name" value="{{ $employee->last_name }}" required
                       class="w-full border rounded p-2 text-sm uppercase font-bold focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Имя</label>
                <input type="text" name="first_name" value="{{ $employee->first_name }}" required
                       class="w-full border rounded p-2 text-sm uppercase font-bold focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Отчество</label>
                <input type="text" name="middle_name" value="{{ $employee->middle_name }}"
                       class="w-full border rounded p-2 text-sm uppercase font-bold focus:border-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Дата рождения</label>
                <input type="date" name="birth_date" value="{{ $employee->birth_date }}"
                       class="w-full border rounded p-2 text-sm font-bold outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Дата приема</label>
                <input type="date" name="hire_date" value="{{ $employee->hire_date }}"
                       class="w-full border rounded p-2 text-sm font-bold outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Телефон</label>
                <input type="text" name="phone" value="{{ $employee->phone }}" placeholder="+7 (___) ___-__-__"
                       class="w-full border rounded p-2 text-sm font-bold outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Должность</label>
                <select name="position_id" required class="w-full border rounded p-2 text-sm uppercase font-bold bg-white">
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>
                            {{ $pos->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Статус сотрудника</label>
                <div class="flex items-center mt-2 gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }} class="mr-2">
                        <span class="text-xs font-bold uppercase text-green-600">Активен</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="is_active" value="0" {{ !$employee->is_active ? 'checked' : '' }} class="mr-2">
                        <span class="text-xs font-bold uppercase text-red-600">Неактивен</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="pt-6 flex justify-between items-center border-t">
            <a href="{{ route('employees.index') }}" class="text-gray-500 text-[10px] font-bold uppercase hover:underline">Отмена</a>
            <button type="submit" class="bg-orange-500 text-white px-8 py-2 rounded text-xs font-bold uppercase hover:bg-orange-600 transition shadow-md">
                Сохранить изменения <i class="fas fa-save ml-2"></i>
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
