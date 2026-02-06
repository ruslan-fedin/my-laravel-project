@extends('layouts.app')

@section('content')
<script src="https://unpkg.com/imask"></script>

<div class="max-w-4xl mx-auto px-6 py-4">
    <div class="bg-white shadow-md rounded-lg overflow-hidden border">
        <div class="bg-gray-100 px-6 py-4 border-b">
            <h2 class="text-sm font-bold uppercase tracking-tight text-gray-700">Редактировать данные сотрудника</h2>
        </div>

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row gap-8">
                {{-- ФОТОГРАФИЯ --}}
                <div class="w-full md:w-1/3 flex flex-col items-center border-r pr-8 border-gray-100">
                    <label class="block text-[10px] font-bold uppercase text-gray-500 mb-3 text-center w-full">Фотография</label>
                    <div class="w-48 h-56 rounded-lg border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center bg-gray-50 mb-4 relative">

                        @if($employee->photo && file_exists(public_path($employee->photo)))
                            {{-- ПРЯМОЙ ПУТЬ БЕЗ STORAGE --}}
                            <img src="{{ asset($employee->photo) }}" id="preview" class="w-full h-full object-cover">
                        @else
                            <div id="placeholder" class="text-center p-4">
                                <i class="fas fa-user-tie text-4xl text-gray-200 mb-2"></i>
                                <p class="text-[9px] text-gray-400 uppercase font-bold">Фото отсутствует</p>
                            </div>
                            <img src="" id="preview" class="hidden w-full h-full object-cover">
                        @endif

                    </div>
                    <label class="cursor-pointer bg-slate-800 text-white px-4 py-2 rounded text-[10px] font-bold uppercase hover:bg-black transition w-48 text-center shadow-sm">
                        <i class="fas fa-camera mr-2"></i> Обновить фото
                        <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*">
                    </label>
                </div>

                {{-- ДАННЫЕ --}}
                <div class="w-full md:w-2/3 space-y-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Фамилия</label>
                            <input type="text" name="last_name" value="{{ $employee->last_name }}" required class="w-full border rounded p-2 text-sm uppercase font-bold outline-none focus:border-blue-500 shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Имя</label>
                                <input type="text" name="first_name" value="{{ $employee->first_name }}" required class="w-full border rounded p-2 text-sm uppercase font-bold outline-none shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Отчество</label>
                                <input type="text" name="middle_name" value="{{ $employee->middle_name }}" class="w-full border rounded p-2 text-sm uppercase font-bold outline-none shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 border-t pt-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Дата рождения</label>
                            <input type="date" name="birth_date" value="{{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('Y-m-d') : '' }}" class="w-full border rounded p-2 text-sm font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Дата приема</label>
                            <input type="date" name="hire_date" value="{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '' }}" class="w-full border rounded p-2 text-sm font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Телефон</label>
                            <input type="text" id="phone-mask" name="phone" value="{{ $employee->phone }}" class="w-full border rounded p-2 text-sm font-bold shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t pt-4">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Должность</label>
                            <select name="position_id" class="w-full border rounded p-2 text-sm uppercase font-bold bg-white">
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-gray-500 mb-1">Статус</label>
                            <div class="flex items-center mt-2 gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }} class="mr-2">
                                    <span class="text-xs font-bold uppercase text-green-600">Активен</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="is_active" value="0" {{ !$employee->is_active ? 'checked' : '' }} class="mr-2">
                                    <span class="text-xs font-bold uppercase text-red-600">Архив</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex justify-between items-center border-t">
                <a href="{{ route('employees.index') }}" class="text-gray-500 text-[10px] font-bold uppercase hover:underline">Отмена</a>
                <button type="submit" class="bg-orange-500 text-white px-10 py-3 rounded text-xs font-bold uppercase hover:bg-orange-600 shadow-md transition-all active:scale-95">
                    Сохранить изменения <i class="fas fa-save ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone-mask');
        if (phoneInput) { IMask(phoneInput, { mask: '{+7} (000) 000-00-00' }); }

        const photoInput = document.getElementById('photo-input');
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');

        if (photoInput) {
            photoInput.onchange = function() {
                const [file] = this.files;
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                }
            };
        }
    });
</script>
@endsection
