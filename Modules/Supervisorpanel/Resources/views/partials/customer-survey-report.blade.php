<table id="resulttable" class="display" style="width:100%">
    <thead>
        <tr>
            <th nowrap>Client</th>
            <th nowrap>Project </th>
            <th nowrap>Employee Name</th>
            <th nowrap>Employee Number</th>
            <th nowrap>Payperiod Name</th>
            @foreach ($completeQuestionArray as $item)
                <th nowrap>{{ $item }}</th>
            @endforeach
            <th nowrap>Average Score</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($completeAnswerArray as $key=>$value)
            <tr>
            @foreach ($value as $item)
                    <td nowrap>{{ $item }}</td>
                
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
