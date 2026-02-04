@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-archive"></i> Архив сотрудников</h1>
        <div>
            <a href="{{ route('employees.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> К активным сотрудникам
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($employees->isEmpty())
        <div class="alert alert-info">
            Архив пуст. Нет удаленных сотрудников.
        </div>
    @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ФИО</th>
                    <th>Должность</th>
                    <th>Дата удаления</th>
                    <th>Записей в табеле</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr class="table-secondary">
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->full_name }}</td>
                    <td>{{ $employee->position->name ?? '-' }}</td>
                    <td>
                        {{ $employee->deleted_at->format('d.m.Y H:i') }}
                        <br>
                        <small class="text-muted">
                            {{ $employee->deleted_at->diffForHumans() }}
                        </small>
                    </td>
                    <td>
                        @php($timesheetCount = $employee->timesheetItems()->count())
                        <span class="badge {{ $timesheetCount > 0 ? 'bg-info' : 'bg-secondary' }}">
                            {{ $timesheetCount }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <form action="{{ route('employees.restore', $employee->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-success"
                                        title="Восстановить">
                                    <i class="bi bi-arrow-counterclockwise"></i> Восстановить
                                </button>
                            </form>

                            <form action="{{ route('employees.force-delete', $employee->id) }}"
                                  method="POST" class="d-inline ms-1"
                                  onsubmit="return confirmDelete(this, '{{ $employee->full_name }}', {{ $timesheetCount }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"
                                        title="Удалить навсегда">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $employees->links() }}
    @endif
</div>

@push('scripts')
<script>
function confirmDelete(form, employeeName, timesheetCount) {
    if (timesheetCount > 0) {
        alert('Невозможно удалить сотрудника, так как имеются ' + timesheetCount + ' записей в табеле.\n\nСначала удалите или переназначьте записи в табеле.');
        return false;
    }

    return confirm('Вы уверены, что хотите полностью удалить сотрудника "' + employeeName + '"?\n\nЭто действие нельзя отменить!\nВсе данные будут безвозвратно удалены.');
}
</script>
@endpush
@endsection
