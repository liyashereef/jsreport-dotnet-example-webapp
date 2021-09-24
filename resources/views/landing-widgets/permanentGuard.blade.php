<table class="table">
    <thead>
        <tr>
            <th nowrap class="heading">Mandatory Courses</th>
            <th class="guardHeading">Deadline</th>
            @foreach ($names as $key => $value)
                <th class="guardHeading">{{ $value }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $course => $detail)
        <tr>
            <td nowrap class="heading">{{$detail['course']}}</td>
            <td nowrap>{{$detail['course_due_date']}}</td>
            @foreach ($detail['allocation_data'] as $emp => $empDetail)
                <td nowrap style="background-color: {{ $empDetail['color_code'] }}">{{ $empDetail['completed_date'] }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

<style>
    .table {
        border: 5px solid white !important;
    }
    .heading {
        background-color: #003b63;
        color: white;
    }
    .guardHeading {
        background-color: #F17437;
        color: white;
    }
</style>
