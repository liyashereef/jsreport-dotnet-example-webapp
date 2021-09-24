<table id="resulttable" class="table table-bordered dataTable no-footer dtr-inline" style="width:100%">
    <thead>
        <tr>
            <th>Client</th>
            <th>Project </th>
            <th>Employee Name</th>
            <th>Employee Number</th>
            <th>Payperiod Name</th>
            @foreach ($completeQuestionArray as $item)
                <th>{{ $item }}</th>
            @endforeach
            <th>Average Score</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($completeAnswerArray as $key=>$value)
            <tr>
            @foreach ($value as $item)
                    <td>{{ $item }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>