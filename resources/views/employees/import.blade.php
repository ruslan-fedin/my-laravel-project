@extends('layouts.app')

@section('content')
<div class="flex justify-center py-12 px-4">
    <div class="w-full max-w-2xl bg-white shadow-sm rounded-lg border border-slate-200 p-10">
        <h1 class="text-2xl font-black uppercase text-slate-900 mb-6">Импорт сотрудников из табеля</h1>

        <form action="{{ route('employees.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-slate-400 transition">
<input type="file" name="file" class="hidden" id="fileInput" accept=".xlsx, .xls, .csv">                <label for="fileInput" class="cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-4"></i>
                    <p class="text-sm font-bold text-slate-500 uppercase">Нажмите, чтобы выбрать файл </p>
                </label>
            </div>

            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('employees.index') }}" class="text-[10px] font-black uppercase text-slate-400">Отмена</a>
                <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded text-[10px] font-black uppercase shadow-xl hover:bg-black">
                    Начать импорт
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
