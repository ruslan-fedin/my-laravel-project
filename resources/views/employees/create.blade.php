@extends('layouts.app')

@section('content')
<script src="https://unpkg.com/imask"></script>

<div class="max-w-[1440px] mx-auto px-[120px] py-10">
    {{-- Навигация назад --}}
    <div class="mb-6">
        <a href="{{ route('brigades.index') }}" class="text-[10px] font-black uppercase text-slate-400 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Вернуться к структуре
        </a>
    </div>

    <div class="bg-white shadow-xl rounded-[2rem] overflow-hidden border border-slate-200">
        {{-- Заголовок --}}
        <div class="bg-slate-900 px-8 py-6">
            <h2 class="text-xl font-black uppercase tracking-tight text-white">
                Регистрация нового сотрудника
            </h2>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            <div class="flex flex-col md:flex-row gap-10">
                {{-- БЛОК ФОТО --}}
                <div class="w-full md:w-1/3 flex flex-col items-center border-r border-slate-100 pr-10">
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-4 text-center w-full tracking-widest">Фотография сотрудника</label>
                    <div class="w-52 h-64 rounded-2xl border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50 mb-6 relative shadow-inner">
                        <div id="placeholder" class="text-center p-4">
                            <i class="fas fa-user-plus text-5xl text-slate-200 mb-3"></i>
                            <p class="text-[9px] text-slate-400 uppercase font-black">Нажмите для выбора</p>
                        </div>
                        <img src="" id="preview" class="hidden w-full h-full object-cover">
                    </div>
                    <label class="cursor-pointer bg-blue-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-blue-700 transition-all w-full text-center shadow-lg shadow-blue-100">
                        <i class="fas fa-camera mr-2"></i> Выбрать фото
                        <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*">
                    </label>
                </div>

                {{-- БЛОК ДАННЫХ --}}
                <div class="w-full md:w-2/3 space-y-8">
                    {{-- ФИО ПОЛНОСТЬЮ --}}
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Фамилия</label>
                            <input type="text" name="last_name" required placeholder="ИВАНОВ"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase focus:border-blue-500 outline-none transition-all shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Имя</label>
                                <input type="text" name="first_name" required placeholder="ИВАН"
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase focus:border-blue-500 outline-none transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Отчество</label>
                                <input type="text" name="middle_name" placeholder="ИВАНОВИЧ"
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase focus:border-blue-500 outline-none transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 border-t border-slate-100 pt-8">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Дата рождения</label>
                            <input type="date" name="birth_date"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Дата приема</label>
                            <input type="date" name="hire_date" value="{{ date('Y-m-d') }}"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Телефон</label>
                            <input type="text" id="phone-mask" name="phone" placeholder="+7 (___) ___-__-__"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 border-t border-slate-100 pt-8">
                        <div>
                            <label class="block text-[9px] font-black uppercase text-slate-400 mb-2 ml-1">Должность</label>
                            <select name="position_id" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-xs font-bold uppercase outline-none appearance-none">
                                <option value="" disabled selected>Выберите должность</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- НИЖНЯЯ ПАНЕЛЬ --}}
            <div class="pt-8 flex justify-between items-center border-t border-slate-100">
                <a href="{{ route('brigades.index') }}" class="text-slate-400 text-[10px] font-black uppercase hover:text-slate-600 transition-colors">Отмена</a>
                <button type="submit" class="bg-blue-600 text-white px-12 py-4 rounded-xl text-xs font-black uppercase hover:bg-blue-700 shadow-xl shadow-blue-100 transition-all active:scale-95">
                    Создать сотрудника <i class="fas fa-plus-circle ml-2"></i>
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
