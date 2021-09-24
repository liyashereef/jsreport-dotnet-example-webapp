<div class="col-md-12">
<table id="genreport" class="table table-bordered">
    <thead>
        <th>Project Number</th>
        <th>Project Name</th>
        <th>Contract Hours</th>
        <th>Average Hours per Week</th>
        <th>Variance</th>
        <th>Schedule Indicator</th>
        <th>Supervisor Notes</th>
        <th>Approver Notes</th>        
        <th>Date and Time</th>

        <th>Status</th>

    </thead>

<tbody>
@foreach ($reports as $report)
    <tr>
    <td>{{$report->customer->project_number}}</td>
        <td>{{$report->customer->client_name}}</td>
        <td>{{isset($report->cmuf_total_hours_perweek)?str_replace(".",":",$report->cmuf_total_hours_perweek):""}} </td>
        <td>{{isset($report->avgworkhours)?str_replace(".",":",$report->avgworkhours):""}}</td>
        <td @if($report->cmuf_total_hours_perweek>0)
                @if($report->variance!=0)
                    class="redbg"
                @else
                    class="greenbg"
                @endif
                @else
                class="redbg"
            @endif>
            @if( $report->cmuf_total_hours_perweek>0)
                {{str_replace(".",":",$report->variance)}}
            @else
                Invalid
            @endif
           

        </td>
        <td @if($report->cmuf_total_hours_perweek>0)
            @if($report->schedindicator==0)
                class="redbg"
            @else
                class="greenbg"
            @endif
            @else
            class="redbg"
        @endif>
            @if($report->schedindicator==1 && $report->cmuf_total_hours_perweek>0)
                True
            @else
                False
            @endif
        </td>
        <td>{{$report->supervisornotes}}</td> 
        <td>{{$report->status_notes}}</td>
        <td>{{date("Y-m-d h:i a",strtotime($report->created_at))}}</td>
       
       
        <td 
            @if($report->status==0)
                
                class="yellowbg" style="color:#000 !important"
            @elseif($report->status==1)
                class="greenbg" 
            @elseif($report->status==2)
            class="redbg"
            @endif
            >
            @if($report->status==0)
            @if(\Auth::user()->hasAnyPermission(['approve_all_employee_schedule_requests','approve_allocated_employee_schedule_requests']))
                <a target="_blank" href="{{route('scheduling.approval-grid-view', [ 'id' => $report->id])}}">Pending</a>
            @else
                Pending
            @endif


                
            @elseif($report->status==1)
                Approved
            @elseif($report->status==2)
                Rejected
            @endif
        </td>
    </tr>
@endforeach
</tbody>
</table>
</div>