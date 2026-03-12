@extends('layouts.app')

@section('content')
<script src="https://unpkg.com/imask"></script>

<div class="max-w-5xl mx-auto py-6">
    {{-- Навигация назад --}}
    <div class="mb-6">
        <a href="{{ route('brigades.index') }}" class="text-[10px] font-black uppercase text-slate-400 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Вернуться к структуре
        </a>
    </div>

    <div class="bg-white shadow-xl rounded-[2rem] overflow-hidden border border-slate-200">
        {{-- Заголовок с полным ФИО --}}
        <div class="bg-slate-900 px-8 py-6">
            <h2 class="text-xl font-black uppercase tracking-tight text-white">
                Редактировать: {{ $employee->last_name }} {{ $employee->first_name }} {{ $employee->middle_name }}
            </h2>
        </div>

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row gap-10">
                {{-- БЛОК ФОТО --}}
                <div class="w-full md:w-1/3 flex flex-col items-center border-r border-slate-100 pr-10">
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-4 text-center w-full tracking-widest">Фотография сотрудника</label>
                    <div class="w-52 h-64 rounded-2xl border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50 mb-6 relative shadow-inner">
                        @if($employee->photo && file_exists(public_path($employee->photo)))
                            <img src="{{ asset($employee->photo) }}" id="preview" class="w-full h-full object-cover">
                        @else
                            <div id="placeholder" class="text-center p-4">
                                <i class="fas fa-user-tie text-5xl text-slate-200 mb-3"></i>
                                <p class="text-[9px] text-slate-400 uppercase font-black">Нет фото</p>
                            </div>
                            <img src="" id="preview" class="hidden w-full h-full object-cover">
                        @endif
                    </div>
                    <label class="cursor-pointer bg-blue-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-blue-700 transition-all w-full text-center shadow-lg shadow-blue-100">
                        <i class="fas fa-camera mr-2"></i> Загрузить новое
                        <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*">
                    </label>
                </div>

                {{-- БЛОК ДАННЫХ --}}
                <div class="w-full md:w-2/3 space-y-8">
                    {{-- ФИО ПОЛНОСТЬЮ --}}
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Фамилия</label>
                            <input type="text" name="last_name" value="{{ $employee->last_name }}" required
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase focus:border-blue-500 outline-none transition-all shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Имя</label>
                                <input type="text" name="first_name" value="{{ $employee->first_name }}" required
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase focus:border-blue-500 outline-none transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Отчество</label>
                                <input type="text" name="middle_name" value="{{ $employee->middle_name }}"
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase focus:border-blue-500 outline-none transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 border-t border-slate-100 pt-8">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Дата рождения</label>
                            <input type="date" name="birth_date" value="{{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('Y-m-d') : '' }}"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Дата приема</label>
                            <input type="date" name="hire_date" value="{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('Y-m-d') : '' }}"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Телефон</label>
                            <input type="text" id="phone-mask" name="phone" value="{{ $employee->phone }}"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 border-t border-slate-100 pt-8">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Должность</label>
                            <select name="position_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase outline-none appearance-none">
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Статус сотрудника</label>
                            <div class="flex items-center gap-6 mt-3 ml-1">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                    <span class="ml-2 text-[10px] font-black uppercase text-green-600 group-hover:text-green-700">Активен</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="is_active" value="0" {{ !$employee->is_active ? 'checked' : '' }} class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                                    <span class="ml-2 text-[10px] font-black uppercase text-rose-600 group-hover:text-rose-700">Архив</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- НИЖНЯЯ ПАНЕЛЬ --}}
            <div class="pt-8 flex justify-between items-center border-t border-slate-100">
                <a href="{{ route('brigades.index') }}" class="text-slate-400 text-[10px] font-black uppercase hover:text-slate-600 transition-colors">Отмена</a>
                <button type="submit" class="bg-blue-600 text-white px-12 py-4 rounded-xl text-xs font-black uppercase hover:bg-blue-700 shadow-xl shadow-blue-100 transition-all active:scale-95">
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
