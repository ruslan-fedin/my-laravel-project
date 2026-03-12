@extends('layouts.app')

@section('title', 'Редактировать табель')

@section('content')
<style>
    .edit-page-wrapper {
        padding: 30px 40px;
        background-color: #f8fafc;
        min-height: calc(100vh - 64px);
        font-family: 'Inter', sans-serif;
    }

    @media (max-width: 768px) { .edit-page-wrapper { padding: 20px 15px; } }

    .form-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        max-width: 600px;
        margin: 0 auto;
        overflow: hidden;
    }

    .input-label {
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        display: block;
    }

    .input-field {
        height: 52px;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 0 16px;
        font-size: 15px;
        font-weight: 700;
        width: 100%;
        outline: none;
        transition: all 0.2s;
        color: #1e293b;
    }

    .input-field:focus {
        border-color: #3b82f6;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .btn-update {
        height: 52px;
        background: #0f172a;
        color: white;
        width: 100%;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 900;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
</style>

<div class="edit-page-wrapper">
    <div class="max-w-[600px] mx-auto">
        <a href="{{ route('travel-timesheets.index') }}" class="text-slate-400 text-xs font-black uppercase mb-6 inline-flex items-center gap-2 hover:text-slate-900 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Назад к списку
        </a>

        <div class="form-card">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Редактирование</h1>
                <p class="text-slate-400 text-[11px] font-bold uppercase mt-1">ID табеля: #{{ $timesheet->id }}</p>
            </div>

            <form action="{{ route('travel-timesheets.update', $timesheet) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="input-label">Дата начала</label>
                        <input type="date" name="start_date" value="{{ \Carbon\Carbon::parse($timesheet->start_date)->format('Y-m-d') }}" required class="input-field">
                    </div>

                    <div>
                        <label class="input-label">Дата окончания</label>
                        <input type="date" name="end_date" value="{{ \Carbon\Carbon::parse($timesheet->end_date)->format('Y-m-d') }}" required class="input-field">
                    </div>

                    <div>
                        <label class="input-label">Примечание</label>
                        <input type="text" name="note" value="{{ $timesheet->note }}" placeholder="Напр: Объект Север" class="input-field">
                    </div>
                </div>

                <div class="mt-10">
                    <button type="submit" class="btn-update shadow-lg">
                        <i class="fa-solid fa-floppy-disk"></i> Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
