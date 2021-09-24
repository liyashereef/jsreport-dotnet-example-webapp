@if(!empty($tableHeaderRow))
<tr>
    
    
    @php($payPeriodCount = 1)
    @foreach($tableHeaderRow as $ky1 => $headerRowArr)
        @foreach($headerRowArr as $ky2 => $headerRows)
        @foreach($headerRows as $ky3 => $headerRow)
        <th class="" style="width: 150px;">
            <div class="card-custom card-header-bg text-white text-center" title="{{$headerRow['value']. ' ('. $headerRow['display'].')'}}" style="height: 100%;">
                <span>{{$headerRow['value']}}</span><br />
                <b>{{$headerRow['display']}}</b>
            </div>
        </th>
        @endforeach
        
        <th class="" style="width: 150px;">
            <div class="card-custom card-header-bg text-white text-center" title="Week Total" style="height: 100%;">
                <span>{{'Week '.$ky2}}</span><br />
                <b>Hours</b>
            </div>
        </th>
        
        @endforeach
        
        <th class="" style="width: 150px;">
            <div class="card-custom card-header-bg text-white text-center" title="Week Total" style="height: 100%;">
                <span>Payperiod</span><br />
                <b>Hours</b>
            </div>
        </th>
        @php($payPeriodCount += 1)
    @endforeach
    
    @if($payPeriodCount > 1)
    <th class="" style="width: 150px;">
        <div class="card-custom card-header-bg text-white text-center" title="Week Total" style="height: 100%;">
            <span>Total</span><br />
            <b>Hours</b>
        </div>
    </th>
    @endif
</tr>
@endif