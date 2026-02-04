<table>
    <thead>
        <tr>
            {{-- +3 это №, ФИО, Должность и +1 Примечание --}}
            <th colspan="{{ count($dates) + 4 }}" style="text-align: center; font-weight: bold;">ТАБЕЛЬ УЧЕТА РАБОЧЕГО ВРЕМЕНИ</th>
        </tr>
        <tr>
            <th colspan="{{ count($dates) + 4 }}" style="text-align: center;">Период: {{ $timesheet->start_date }} — {{ $timesheet->end_date }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="border: 1px solid #000000; text-align: center;">№</th>
            <th style="border: 1px solid #000000; text-align: center;">ФАМИЛИЯ ИМЯ ОТЧЕСТВО</th>
            <th style="border: 1px solid #000000; text-align: center;">ДОЛЖНОСТЬ</th>
            @foreach($dates as $date)
                <th style="border: 1px solid #000000; text-align: center;">{{ $date->format('d') }}</th>
            @endforeach
            <th style="border: 1px solid #000000; text-align: center;">ПРИМЕЧАНИЕ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $emp)
            @php $empItems = $items->get($emp->id); @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000000; font-weight: bold;">{{ $emp->last_name }} {{ $emp->first_name }} {{ $emp->middle_name }}</td>
                <td style="border: 1px solid #000000; font-size: 10px;">{{ $emp->position->name }}</td>
                @foreach($dates as $date)
                    @php
                        $item = $empItems?->where('date', $date->format('Y-m-d'))->first();
                        $st = $item ? $statuses->where('id', $item->status_id)->first() : null;
                    @endphp
                    <td style="text-align: center; border: 1px solid #000000; background-color: {{ $st ? $st->color : '#ffffff' }}; color: {{ $st ? '#ffffff' : '#000000' }}; font-weight: bold;">
                        {{ $st ? $st->short_name : '' }}
                    </td>
                @endforeach
                <td style="border: 1px solid #000000; font-size: 9px;">
                    {{ $empItems?->first()?->comment ?? '' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
