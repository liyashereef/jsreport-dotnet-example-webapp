<style>
    .tableHeaderStyle {
        background-color: #003b63;
        color: white;
    }
</style>
@if(!empty($average_report))
<table class="table table-bordered" style="border: 5px solid white;">
    <thead>
        <tr style="border: 5px solid white;">
            <th class="tableHeaderStyle">Average Trend</th>
            <th class="tableHeaderStyle" style="text-align: center">Current</th>
            <th class="tableHeaderStyle" style="text-align: center">Average</th>
            <th class="tableHeaderStyle" style="text-align: center">Trend</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report_keys as $eachkey)
            @if ($eachkey == 'total')
            @else
                <tr>
                    <td class="tableHeaderStyle">{{ $eachkey }}</td>
                    <td style="background-color: {{ $current_report['color_class'][$eachkey] }}; text-align: center;">{{ number_format($current_report['score'][$eachkey],2) }}</td>
                    <td style="background-color: {{ $average_report['color_class'][$eachkey] }}; text-align: center;">{{ number_format($average_report['score'][$eachkey],2) }}</td>
                    @if (isset($current_report) && empty(!$current_report['score']))
                        @if (is_numeric($average_report['score'][$eachkey]) && is_numeric($current_report['score'][$eachkey]))
                            @if (number_format($current_report['score'][$eachkey] - $average_report['score'][$eachkey], 2) == 0)
                                <td style="text-align: center; color: black; background-color: yellow;">{{ number_format($current_report['score'][$eachkey] - $average_report['score'][$eachkey], 2) }}</td>
                            @elseif (number_format($current_report['score'][$eachkey] - $average_report['score'][$eachkey], 2) > 0)
                                <td style="text-align: center; color: black; background-color: green;">{{ number_format($current_report['score'][$eachkey] - $average_report['score'][$eachkey], 2) }}</td>
                            @else
                                <td style="text-align: center; color: white; background-color: red;">{{ number_format($current_report['score'][$eachkey] - $average_report['score'][$eachkey], 2) }}</td>
                            @endif
                        @endif
                    @else
                        <td style="text-align: center;">{{ number_format($average_report['score'][$eachkey], 2) }}</td>
                    @endif
                </tr>
            @endif
        @endforeach
        <tr style="border: 5px solid white;">
            <td class="tableHeaderStyle">Current Site Metric</td>
            <td style="text-align: center;">{{ number_format($current_report['score']['total'],2) }}</td>
            <td class="tableHeaderStyle" style="text-align: center;">Post Orders</td>
            <td style="padding: 5px !important;"><button type="button" class="btn btn-secondary btn-block">Download</button></td>
        </tr>
    </tbody>
</table>
@else
<p>Survey not submitted by the supervisor</p>
@endif
