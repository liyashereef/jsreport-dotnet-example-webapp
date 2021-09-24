@foreach($schedules as $schedule)
<tr>


    @foreach($schedule['schedule_data'] as $payperiod => $dataCollection)
    @foreach($dataCollection as $week => $dataArr)
    @foreach($dataArr as $data)
    @if($data['is_data'] == true)
    <td class=" value_card" style="width: 150px;z-index:-1">
        <div class="card-custom {{$data['day']}} text-white text-center {{($data['overlaps'] == '1')? 'overlaps-block':''}}" timing="{{$data['start_datetime']}} - {{$data['end_datetime']}}" date="{{$data['date_string']}}" username="{{$data['user_name']}}" title="{{$data['start_datetime'].'-'.$data['end_datetime'].' ('.$data['hours'].' hrs)'}}"  style="height: 100%;{{($data['overlaps'] == '1')? 'background:red':''}};">
            <span>{{$data['start_datetime']}} - {{$data['end_datetime']}}</span><br />
            <b>{{str_replace(".",":",$data['hours'])}} hrs</b>
        </div>
    </td>
    @else
    <td style="width: 150px;z-index:-1">
        <div class="card-custom {{$data['day']}}_unscheduled text-center"  style="height: 100%;">
            <span>&nbsp;</span><br />
            <b>&nbsp;</b>
        </div>
    </td>
    @endif
    @endforeach

    <td style="width: 150px;z-index:-1">
        <div class="card-custom text-center"  style="height: 100%;">
            <span>{{$schedule['week_'.$payperiod.'_'.$week]}} hrs</span><br />
            <b>&nbsp;</b>
        </div>
    </td>
    @endforeach

    <td style="width: 150px;z-index:-1">
        <div class="card-custom text-center"  style="height: 100%;">
            <span>{{array_key_exists($payperiod.'_display', $schedule) ? $schedule[$payperiod.'_display']:'00:00'}} hrs</span><br />
            <b>&nbsp;</b>
        </div>
    </td>
    @endforeach
    <td style="width: 150px;z-index:-1">
        <div class="card-custom text-center"  style="height: 100%;">
            <span>{{array_key_exists('total_hours_display', $schedule) ? $schedule['total_hours_display']: '00:00'}} hrs</span><br />
            <b>&nbsp;</b>
        </div>
    </td>
</tr>
@endforeach
