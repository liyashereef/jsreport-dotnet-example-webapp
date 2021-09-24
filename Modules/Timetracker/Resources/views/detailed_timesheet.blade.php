@extends('layouts.app')
@section('title', 'Detailed Timesheet')
@section('content_header')
<h1 class="ts-approve">Timesheet Approval</h1>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
@stop
@section('content')
<style>
    .popover {
    z-index: 1010; /* A value higher than 1010 that solves the problem */
}
.time-container{
    padding: 1px 1px 0px 0px !important;
}

.time-container label{
    padding-bottom: 1px;
    float: left;
    clear: both;
}

.position-name-label{
	font-size: 13px;
}
.total-hours-span{
    color: red;

}
#total-row{
    font-weight: bold;
    height: 50px;
}
</style>
<span class="ts-pending pull-right">Approved <strong> {{$approved_count['approved']}} </strong> of <strong>{{$approved_count['total']}}</strong></span>
<div class="table_title">
<h4>Employee Information</h4></div>

<div class="timesheet-approval"></div>
@foreach($shift_details as $shift_details)
<?php
    $customerAllocationRepo = app()->make(\Modules\Admin\Repositories\CpidCustomerAllocationRepository::class);
    $empShiftCpidRepo = app()->make(\Modules\TimeTracker\Repositories\EmployeeShiftCpidRepository::class);
?>
<script>
 //get customer allocated cpids list.
function cpidOptionsList(){
   <?php $options =$empShiftCpidRepo->getCpidSelectOptions($shift_details->trashed_customer->id,$shift_details->id); ?>
    return '<?php echo $options ?>';
}

function cpidWorkTypeOptionsList($customer_id){
    <?php
    $workHourRepo = app()->make(\Modules\Timetracker\Repositories\EmployeeShiftWorkHourTypeRepository::class);?>
    return '<?php echo $workHourRepo->generateCustomerOptionsList($customer_id) ?>';
}
</script>
<div class="table-responsive full-width tsa-emp-info">
    <table width="100%" >
        <tr>
            <th>Employee Id</th>
            <th>Employee Name</th>
            <th>Project Number</th>
            <th>Client</th>
            <th>Period Name</th>
            <th>Period Start</th>
            <th>Period End</th>
        </tr>
        <tr>
            <td>{{$shift_details->trashed_user->trashed_employee_profile->employee_no}}</td>
            <td>{{$shift_details->trashed_user->first_name or ''}} {{$shift_details->trashed_user->last_name or ''}}</td>
            <td>{{$shift_details->trashed_customer->project_number}}</td>
            <td>{{$shift_details->trashed_customer->client_name}}</td>
            <td>{{$shift_details->trashed_payperiod->pay_period_name}}&nbsp;{{$shift_details->payperiod_week? 'W'.$shift_details->payperiod_week:''}}</td>
            <td>{{$shift_details->trashed_payperiod->start_date}}</td>
            <td>{{$shift_details->trashed_payperiod->end_date}}</td>
        </tr>
    </table>
</div>

<div id="tab-content2">
     <div class="table-fixedth full-width" style="margin-bottom: 5px;">
                Employee Log Details
    </div>
    <div class="table-responsive m-2 p-2">
        <table class="table table-bordered" id="employee-log-details-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actual  Hours</th>
                    <th>Scheduled Hours</th>
                    <th>Notes</th>
                    <th>View Schedule</th>
                </tr>
            </thead>
            <tbody>
            @php
                 $totalHours = 0;
                 $totalScheduledHours = 0;
            @endphp
                @foreach($shift_details->shifts as $eachshift)
                    <tr>
                        <td>{{date('l', strtotime($eachshift->start))}}</td>
                        <td>{{date("M d, Y",strtotime($eachshift->start))}}</td>
                        <td>{{date("h:i a", strtotime($eachshift->start))}}</td>
                        @if (!empty($eachshift->end))
                        <td>{{date("h:i a", strtotime($eachshift->end))}}</td>
                        @else
                        <td>--</td>
                        @endif
                        @php
                        if(!empty($eachshift->end) && !empty($eachshift->start)){
                            $actualHours = (new Carbon($eachshift->end))->diff(new Carbon($eachshift->start))->format('%h:%I');
                            $time = explode(':', $actualHours);
                            $minutes = ($time[0]*60) + ($time[1]);
                            $totalHours += $minutes;
                        }else{
                            $actualHours = "--";
                        }
                        @endphp
                        <td>{{ $actualHours }}</td>
                        @if(!empty($eachshift->employeeScheduleTimeLog->hours))
                        <td>{{empty($eachshift->employeeScheduleTimeLog->hours)?'-':str_replace('.', ':', $eachshift->employeeScheduleTimeLog->hours)}}</td>
                        @elseif (isset($dateWiseScheduleArray[date("Y-m-d", strtotime($eachshift->start))]))
                        <td>
                            {{$dateWiseScheduleArray[date("Y-m-d", strtotime($eachshift->start))]["expected_hours"]}}
                        </td>
                        @else
                        <td>-</td>
                        @endif
                        <td>{{empty($eachshift->notes)?'-':$eachshift->notes}}</td>
                        @if (!empty($eachshift->employeeScheduleTimeLog->employee_schedule_id))
                        <td><a target="_blank" title="View"  href="{{route('scheduling.approval-grid-view', [ 'id' => $eachshift->employeeScheduleTimeLog->employee_schedule_id])}}" class="view btn fa fa-eye"></a></td>
                        @elseif (isset($dateWiseScheduleArray[date("Y-m-d", strtotime($eachshift->start))]))
                        <td><a target="_blank" title="View"  href="{{route('scheduling.approval-grid-view', [ 'id' => $dateWiseScheduleArray[date("Y-m-d", strtotime($eachshift->start))]["id"]])}}" class="view btn fa fa-eye"></a></td>                        @else
                        <td><span style="margin-left: 14px;">--</span></td>
                        @endif
                    </tr>
                    @php
                        if(!empty($eachshift->employeeScheduleTimeLog->hours))
                        {
                        $ScheduledParsedHours = new Carbon($eachshift->employeeScheduleTimeLog->hours);
                        $ScheduledFormattedHours  = $ScheduledParsedHours->format('H:i');
                        $scheduledTime = explode(':', $ScheduledFormattedHours);
                        $scheduledMinutes = ($scheduledTime[0]*60) + ($scheduledTime[1]);
                        $schedule_date=date("Y-m-d", strtotime($eachshift->start));
                        if($eachshift->employeeScheduleTimeLog!=null){
                            if(!in_array($schedule_date,$scheduleDates)){
                                $totalScheduledHours += $scheduledMinutes;
                                $scheduleDates[]=$eachshift->employeeScheduleTimeLog->schedule_date;
                            }else{

                            }
                        }
                        }else if(isset($dateWiseScheduleArray[date("Y-m-d", strtotime($eachshift->start))])){
                            $ScheduledParsedHours = new Carbon($dateWiseScheduleArray[date("Y-m-d", strtotime($eachshift->start))]["expected_hours"]);
                            $ScheduledFormattedHours  = $ScheduledParsedHours->format('H:i');
                            $scheduledTime = explode(':', $ScheduledFormattedHours);
                            $scheduledMinutes = ($scheduledTime[0]*60) + ($scheduledTime[1]);
                            $schedule_date=date("Y-m-d", strtotime($eachshift->start));
                            if(!in_array($schedule_date,$scheduleDates)){
                                $totalScheduledHours += $scheduledMinutes;
                                $scheduleDates[]=date("Y-m-d", strtotime($eachshift->start));

                            }else{
                            }
                        }
                    @endphp
                @endforeach
            </tbody>
            <tbody>
            @php
            //totalHours
                $totalHoursLength = strlen((string)$totalHours % 60);
                if($totalHoursLength > 1){
                    $totalMinutes = $totalHours % 60;
                }else{
                    $totalMinutes = '0'.$totalHours % 60;
                }
            //Scheduled Hours
                $totalScheduledHoursLength = strlen((string)$totalScheduledHours % 60);
                $scheduleHours="";
                if($totalScheduledHours>0){
                    $scheduleHours=intval($totalScheduledHours/60);
                }
                if($totalScheduledHoursLength > 1){
                    $scheduledMinutes = $totalScheduledHours % 60;
                }else{
                    $scheduledMinutes = '0'.$totalScheduledHours % 60;
                }
            @endphp
                <tr id="total-row">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{$hours = intdiv($totalHours, 60).':'. ($totalMinutes)}}</td>
                    <td>{{$scheduledhours = intdiv($totalScheduledHours, 60).':'. ($scheduledMinutes)}}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


{{ Form::open(array('url'=>'#','id'=>'approvalform', 'method'=> 'POST')) }}
{{csrf_field()}}
<!--<form id="approvalform" role="form" method="POST" action="">-->
{{ csrf_field() }}
<div class="inline-block full-width tsa-approval">
<h5>Timesheet Approval</h4>
<div class="timesheet-approval"></div>
</div>
<input type="hidden" name="assigned" class="form-control" id="assigned" value="{{$shift_details->assigned}}">
<input type="hidden" name="employee_shift_payperiod_id"
class="form-control" id="assigned" value="{{$shift_details->id}}">
<!-- Original Time Sheet Section -->
<div class="row">
<div class="form-inline form-group col-md-6">
    <div class="col-md-2 col-xs-12"><label>Original Time Sheet</label></div>
    <div class="form-group col-md-3 col-xs-12">
        <input type="hidden" name="employeeId" value="{{$shift_details->employee_id}}">
        <input type="hidden" name="payPeriodId" value="{{$shift_details->trashed_payperiod->id}}">
        <input type="hidden" name="customerId" value="{{$shift_details->customer_id}}">
        <input type="hidden" id="weeklyPerformance" name="weeklyPerformance" value="">
        <label  id="exampleInputEmail2">Total Regular Hours</label>
        <input type="text" name="totalRegularHours" class="form-control size-alignment" id="txt1" value="{{ $shift_details->total_regular_hours }}" readonly="readonly"  >
        <input type="hidden" name="originalRegHours" class="form-control" id="txt1org" value="">
    </div>
    <div class="form-group col-md-3 col-xs-12">
        <label  for="exampleInputEmail2"> Total Overtime Hours</label>
        <input type="text" name="totalOvertimeHours" class="form-control size-alignment" id="txt2" value="{{ $shift_details->total_overtime_hours }}" readonly="readonly">
        <input type="hidden" name="originalOvrHours" class="form-control" id="txt2org" value="">
        <input type="hidden" name="clientApprovedBillableOvertime" class="form-control" id="clientApprovedBillableOvertime" value="">
    </div>
    <div class="form-group col-md-3 col-xs-12">
        <label  for="exampleInputEmail2">  Total Stat Hours</label>
        <input type="text" name="totalStatutoryHours" class="form-control size-alignment" id="txt3" value="{{ $shift_details->total_statutory_hours }}" readonly="readonly">
        <input type="hidden" name="originalStatHours" class="form-control" id="txt3org" value="">
        <input type="hidden" name="clientApprovedBillableStatutory" class="form-control" id="clientApprovedBillableStatutory" value="">
    </div>
</div>
<!--php start-->
@php
            $totalRegularHours = 0;
            $totalRegularMinutes = 0;
            $totalRegulartimeHours = $shift_details->approved_total_regular_hours;
            if($totalRegulartimeHours != null){
                $timesplit = explode(':',$totalRegulartimeHours);
                $totalRegularHours = $timesplit[0];
                $totalRegularMinutes = $timesplit[1];
            }
        @endphp
<!--php end-->
<div class="form-inline form-group col-md-6">
<div class="col-md-6"> <label style="margin-left: 27px;"><span style="padding-right: 10px;">Supervisor Time Sheet <br/><p class="total-hours-span">(Enter Total Hours of the Week including Regular, Overtime and Stat)</p></span></label></div>
    <div class="form-group col-md-4 col-xs-12">
        <div class="col-md-5 time-container">
            <label  for="approvedTotalRegularHoursSelect"> Hours</label>
            <select name="approvedTotalRegularHoursSelect"  id="approvedTotalRegularHoursSelect" class="form-control size-alignment supervisor-time-fields">
                    <option value="00" selected>00</option>
                    @for ($i=1;$i<=200;$i++)
                    @if (strlen((string)$i) == 1)
                    <option value={{"0".$i}} @if("0".$i == $totalRegularHours || ($i == $total_hours && $totalRegularHours == 0)) selected @endif>{{"0".$i}}</option>
                    @else
                    <option value={{$i}} @if($i == $totalRegularHours || ($i == $total_hours && $totalRegularHours == 0)) selected @endif>{{$i}}</option>
                    @endif
                    @endfor
            </select>
            <input type="hidden"
name="approvedTotalRegularHours"
class="form-control size-alignment supervisor-time-fields"
id="approvedTotalRegularHours"
data-original-time="{{ $shift_details->total_regular_hours }}"
value="{{ $shift_details->fallbackApprovedRegularHours() }}"
{{$shift_details->canEdit()?:'readonly'}}
placeholder="00:00"
>
        </div>
        <div style="padding-right: 15px;padding-right:15px;"></div>
        <div class="col-md-5 time-container">
            <label  for="approvedTotalOvertimeHours"> Minutes</label>
            <select name="approvedTotalRegularMinutesSelect"  id="approvedTotalRegularMinutesSelect" class="form-control size-alignment supervisor-time-fields">
                    <option value="00" selected>00</option>
                    <option value="15" @if("15" == $totalRegularMinutes) selected @endif>15</option>
                    <option value="30" @if("30" == $totalRegularMinutes) selected @endif>30</option>
                    <option value="45" @if("45" == $totalRegularMinutes) selected @endif>45</option>
            </select>
        </div>
    </div>
</div>
</div>
<!-- Supervisor Time Sheet Editable -->
<div class="form-inline form-group col-md-12" style="display: none;">
    <!-- Supervisor Time Sheet Editable End-->
    <div class="form-group col-md-3 col-xs-12">
        <div class="col-md-6 time-container">
        <label  for="approvedTotalOvertimeHours"> Total Overtime Hours</label>
        <input type="text"
               name="approvedTotalOvertimeHours"
               class="form-control size-alignment supervisor-time-fields"
               data-original-time="{{$shift_details->total_overtime_hours }}"
               id="approvedTotalOvertimeHours"
               value="{{$shift_details->fallbackApprovedOvertimeHours() }}"
               {{$shift_details->canEdit()?:'readonly'}}
               placeholder="00:00"
        >
        </div>
        <div class="col-md-6 time-container">
            <label  for="overtimeBillableHours">Billable OT Hours</label>
            <input type="text"
                   name="overtimeBillableHours"
                   class="form-control size-alignment supervisor-time-fields"
                   data-original-time="{{$shift_details->billable_overtime_hours }}"
                   id="overtimeBillableHours"
                   value={{($shift_details->billable_overtime_hours)?$shift_details->billable_overtime_hours:"00:00" }}
                   {{$shift_details->canEdit()?:'readonly'}}
                   placeholder="00:00"
            >
        </div>
    </div>






    <div class="form-group col-md-3 col-xs-12" style="display: none;">
    <div class="col-md-6 time-container">
        <label  for="approvedTotalStatutoryHours" class="pr-33"> Total Stat Hours</label>
        <input type="text"
        name="approvedTotalStatutoryHours"
        class="form-control size-alignment supervisor-time-fields"
        id="approvedTotalStatutoryHours"
        data-original-time="{{ $shift_details->total_statutory_hours }}"
        value="{{$shift_details->fallbackApprovedStatutoryHours()}}"
        {{$shift_details->canEdit()?:'readonly'}}
        placeholder="00:00"
        >
    </div>
        <div class="col-md-6 time-container">
            <label  for="statBillableHours" class="pr-14">Billable Stat Hours</label>
            <input type="text"
                   name="statBillableHours"
                   class="form-control size-alignment supervisor-time-fields"
                   id="statBillableHours"
                   data-original-time="{{ $shift_details->billable_statutory_hours }}"
                   value={{ ($shift_details->billable_statutory_hours)?$shift_details->billable_statutory_hours:"00:00" }}
                   {{$shift_details->canEdit()?:'readonly'}}
                   placeholder="00:00"
            >
        </div>
    </div>


</div>

<?php  $cpidCustomerAllocations = $customerAllocationRepo->getByCustomerId($shift_details->trashed_customer->id);
       $cpidCustomerAllocatonsActive = $customerAllocationRepo->getByCustomerIdWithActive($shift_details->trashed_customer->id);
       $empShiftCpids = $empShiftCpidRepo->getAllBy([
                        'employee_shift_payperiod_id' => $shift_details->id
                    ]);?>

<div class="row">
    <!-- Cpid Asssign Section -->
    <div class="col-md-8 p-4">
         @if($cpidCustomerAllocatonsActive->isEmpty())
            <div class="alert alert-info" role="alert">
                No CPID allocated!
            </div>
         @endif
         <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>CPID</th>
                        <th style="width:15%">CPID Function</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th style="width:15%">Code</th>
                        <th>Hours</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cpidInjectionContainer">
                    @foreach($empShiftCpids as $index => $empShiftCpid)
                        <tr id="94a6eb07-{{$index}}" class="cpid-fields-row">
                            <td>
                                <input type="hidden" name="es_cpid[]" value="{{$empShiftCpid->id}}"/>
                                <select name="cpids[]"
                                data-target="94a6eb07-{{$index}}"
                                class="form-control cpid-dropdown"
                                {{$shift_details->canEdit()?:'disabled'}}
                                >
                                <option value="">Select any</option>
                                <?php
                                 $options = $empShiftCpidRepo->getCpidSelectOptions(
                                     $shift_details->trashed_customer->id,
                                     $shift_details->id,
                                     $empShiftCpid->cpid_lookup_with_trash->id
                                    );
                                  echo $options;
                                ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control  cpfunctions"
                                value="{{($empShiftCpid->cpid_lookup_with_trash->cpidFunction->name) ?? ''}}"  name="cpidFunction[]" required readonly >
                            </td>
                            <td><label class="cpidRoleLabel">-</label></td>
                            <td>
                                <select  style="display: inline-block;width:75%" name="cpidWorkType[]" required
                                class="form-control cpid-work-type"
                                {{$shift_details->canEdit()?:'disabled'}}
                                >
                                <option value="">Select any</option>
                                    <?php echo $workHourRepo->generateCustomerOptionsList($customer_id,$empShiftCpid->work_hour_type_id); ?>
                                </select>
                                <i  style="display: inline-block;padding-left:5px"
                                class="fa fa-info-circle typedesc" data-toggle="popover" data-trigger="manual" data-content=""></i>
                            </td>
                            <td>
                                <select style="display: inline-block;width:75%" name="activCode[]"
                                class="form-control active-code-field"
                                 id="activCode[]">
                                    @if(isset($dataMappingTable[$empShiftCpid->work_hour_type_id]))
                                        @foreach($dataMappingTable[$empShiftCpid->work_hour_type_id] as $arrayValue)
                                            <option
                                            @if($empShiftCpid->activity_code_id==$arrayValue["id"])
                                                selected
                                            @endif
                                             value="{{$arrayValue["id"]}}">{{$arrayValue["code"]}}</option>
                                        @endforeach
                                    @else


                                    @endif
                                </select>
                                <i  style="display: inline-block;padding-left:5px"
                                class="fa fa-info-circle activcodedesc" data-toggle="popover" data-trigger="manual" data-content=""></i>

                            </td>
                            <td><input name="cpidTimes[]"
                            type="text"
                            placeholder="00:00"
                            value="{{$empShiftCpid->formatted_time}}"
                            class="form-control cpid-time-field"
                            {{$shift_details->canEdit()?:'readonly'}}
                            ></td>
                            <td>
                            @if($shift_details->canEdit())
                                <button type="button"
                                class="btn btn-custom approval-button removeCpidEntry">
                                <i style="color:white !important;" class="fa fa-trash"></i></button>
                            @endif
                            @can('view_cpid_lookups')
                                <a href="javascript:void(0)"
                                    style="margin-left:5px;"
                                    role="button"
                                    data-toggle="popover"
                                    data-trigger="focus"
                                    class="originalpopover"
                                    title="Rate Info"
                                    data-placement="right"
                                    data-html="true"
                                    data-content="Applied rate: <strong style='color:green;'>{{$empShiftCpid->rate_string_with_currency}}</strong>
                                    </br>
                                    Effective From: <strong>{{$empShiftCpid->cpid_rates_with_trash->effective_from}}</strong>
                                    </br>
                                    Created at: <strong>{{$empShiftCpid->created_at}}</strong>
                                    ">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                </a>
                            @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">
                        @if($shift_details->canEdit() && !$cpidCustomerAllocatonsActive->isEmpty())
                            <button id="newCpidEntry"
                            type="button"
                            class="btn btn-custom approval-button"
                            data-toggle="tooltip"
                            data-original-title="Add more controls">
                            <i style="color:white !important" class="fa fa-plus"></i>&nbsp; Add&nbsp;</button>
                        @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- Approval Status Section -->
    <div class="col-md-4 p-4 ">
         @if($shift_details->isApproved())
         <?php $approversRepo = app()->make(\Modules\Timetracker\Repositories\EmployeeShiftApprovalLogRepository::class);
         $approversList = $approversRepo->getAllBy([
             'employee_shift_payperiod_id' => $shift_details->id
         ]);
         ?>
            <div class="approvers-list">
                 <div class="row m-1">
                        <div class="col-md-6">
                            <p class="approved-by-label font-weight-bold mb-1">Approved By</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mr-2 font-weight-bold mb-1">Date</p>
                        </div>
                </div>
                @foreach($approversList as $approvedObj)
                <div class="row m-1">
                        <div class="col-md-6">
                            <!-- <p class="approved-by-label font-weight-bold mb-1">Approved By</p> -->
                            <p class="name-label m-0 p-0">
                                {{$approvedObj->approved_user->first_name}} {{$approvedObj->approved_user->last_name}}/
                                {{$approvedObj->approved_user->trashed_employee_profile->employee_no}}</p>
                            <span class="position-name-label m-0 p-0">{{$approvedObj->approved_user->formated_role_name}}</span>
                        </div>
                        <div class="col-md-6">
                            <!-- <p class="mr-2 font-weight-bold mb-1">Date</p> -->
                            <p>{{$approvedObj->created_at->format('M d, Y')}} at {{$approvedObj->created_at->format('g:i A')}}</p>
                        </div>
                </div>
                @endforeach
            </div>
            <div class="col-md-12">
            <img class="img img-responsive approve-stamp"src="{{asset('/images/approved_stamp.png')}}"/>
            </div>
        @endif
    </div>
</div>

<!-- Delta -->
<div class="form-inline form-group col-md-12">
    <div class="col-md-2 col-xs-12"><label>Delta (Original-Supervisor)</label></div>
    <div class="form-group col-md-3 col-xs-12">
        <label  for="deltaRegularHours">Total Regular Hours</label>
        <input type="text"
        name="deltaRegularHours"
        class="form-control size-alignment"
        id="deltaRegularHours"
        value=""
        readonly="readonly"  >
    </div>
</div>
<!-- Pagination Section -->
<div class="form-group footer-pagination-control row">
    <div class="col-sm-4 col-md-4 col-xs-12 prev-next-control-place">
        <input class="btn btn-custom approval-button"
        id="prev_btn"
        data-dismiss="modal"
        type="button"
        value="Previous"
        <?php echo (!$shift_details->isApproved()) ? 'style="display:none;"' : '' ?>
        disabled="disabled">
    </div>
    <div class="col-sm-4 col-md-4 text-center col-xs-12 prev-next-control-place">

        @if($shift_details->canEdit())
            @can('approve_timesheet')
            {{ Form::submit('Approve', array('class'=>'btn btn-custom approval-button','id'=>'submit','data-dismiss' =>'modal'))}}
            @endcan
        @endif
    </div>
    <div class="col-sm-4 col-md-4 text-right col-xs-12 prev-next-control-place">
        <input class="btn btn-custom approval-button"
        id="nxt_btn"
        data-dismiss="modal"
        type="button"
        value="Next"
        <?php echo (!$shift_details->isApproved()) ? 'style="display:none;"' : '' ?>
        disabled="disabled">
    </div>
</div>
{{ Form::close() }}
<!-- CPID details -->
@can('view_cpid_lookups')
<div class="table-responsive mt-5">
    <table id="cpid-list-table">
        <thead>
            <tr>
                <th>CPID</th>
                <th>Position</th>
                <th>Effective From</th>
                <th>Pay Standard</th>
                <th>Pay Overtime</th>
                <th>Pay Stat</th>
                <th>Bill Standard</th>
                <th>Bill Overtime</th>
                <th>Bill Stat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cpidCustomerAllocations as $cpidEntry)
                <?php if(!isset($cpidEntry->cpid_lookup)
                || !isset($cpidEntry->cpid_lookup->effectiveDate)){continue;}  ?>

                <tr>
                    <td>{{$cpidEntry->cpid_lookup->cpid}}</td>
                    <td>{{isset($cpidEntry->cpid_lookup->position->position)?
                        $cpidEntry->cpid_lookup->position->position:''}}</td>
                    <td>{{$cpidEntry->cpid_lookup->effectiveDate->effective_from}}</td>
                    <td>{{ number_format($cpidEntry->cpid_lookup->effectiveDate->p_standard,2,'.','') }}</td>
                    <td>${{ number_format($cpidEntry->cpid_lookup->effectiveDate->p_overtime,2,'.','')}}</td>
                    <td>${{number_format($cpidEntry->cpid_lookup->effectiveDate->p_holiday,2,'.','')}}</td>
                    <td>${{number_format($cpidEntry->cpid_lookup->effectiveDate->b_standard,2,'.','')}}</td>
                    <td>${{number_format($cpidEntry->cpid_lookup->effectiveDate->b_overtime,2,'.','')}}</td>
                    <td>${{number_format($cpidEntry->cpid_lookup->effectiveDate->b_holiday,2,'.','')}}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
@endcan

@endforeach


<script>
 function showhide() {
    var x = document.getElementById("tab-content2");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}
    // Shorthand for $( document ).ready()
$(function() {
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
    $('.popover-dismiss').popover({
         trigger: 'focus'
    })
    $('#approvedTotalRegularHoursSelect').select2();//Added Select2 to project listing
    $('#approvedTotalRegularMinutesSelect').select2();//Added Select2 to project listing
    // $('#approvedTotalRegularHoursSelect').change(function(){
    //     var hourval = $(this).val();
    //     $('#approvedTotalRegularHours').val(hourval);

    // });
    // $('#approvedTotalRegularMinutesSelect').change(function(){
    //     var minutesval = $(this).val();
    //     $('#approvedTotalRegularMinutes').val(minutesval);

    // });
    //init data tables
    if($('#cpid-list-table').length > 0){
        $('#cpid-list-table').DataTable({
   "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 50, "All"] ]
         });
    }

    if($('#employee-log-details-table').length > 0){
        $('#employee-log-details-table').DataTable();
    }

    //new cpid entry in table
    $("#newCpidEntry").bind("click", function () {
        let rowContent=generateCpidRow({});
        $("#cpidInjectionContainer").append(rowContent);
        $('body').find('.cpid-dropdown').trigger('change'); //trigger dropdown
        var table=document.getElementById("cpidInjectionContainer");
        let lastRow = table.rows[table.rows.length-1];
        let lastrowid =$('#cpidInjectionContainer tr:last').attr('id');
        populateActivityTypeDropdown(lastrowid);
    });

    $(document).on("change",".cpid-work-type",function(e){
        e.preventDefault();
        let id= ((this).closest("tr").id);
        populateActivityTypeDropdown(id)
    })

    $(document).on("click",".originalpopover",function(e){
        e.preventDefault()
        setTimeout(() => {
            $(this).popover("show")
        }, 100);
    })
    $(document).on("click",".activcodedesc",function(e){
        e.preventDefault();
        let id= ((this).closest("tr").id);
        $(".popover").remove()
        if($('.popover').hasClass('show')){
                $(this).attr('data-content','Cannot proceed with Save while Editing a row.');
                $(this).popover('hide');
            }
        let controlVal=$("#"+id+" .active-code-field").val();
        let customer_type_activity_code = {!! json_encode($dataDescArray) !!};
        if(controlVal!=null){
            setTimeout(() => {
                if($('.popover').hasClass('show')){
                $(this).attr('data-content','No description added.');
                $(this).popover('hide');
            }
            else
            {
                if(customer_type_activity_code[controlVal]!=null){
                    $(this).attr('data-content',customer_type_activity_code[controlVal]);
                    $(this).popover('show');

                }else{

                    $(this).attr('data-content',"No description added");
                    $(this).popover('show');

                }

            }
            }, 200);

        }
    })

    $(document).on('click','body *',function(){
        if($('.popover').hasClass('show')){

            $(".popover").remove()
        }else{
        }
    });

    $(document).on("click",".typedesc",function(e){
        e.preventDefault();
        e.stopPropagation()
        $(".popover").remove()
        let id= (this).closest("tr").id;
        let controlVal=$("#"+id+" .cpid-work-type").val();
        let work_hour_desc = {!! json_encode($workHourArray) !!};
        let popupmesage=work_hour_desc[controlVal]
        if($('.popover').hasClass('show')){
                $(this).attr('data-content','Cannot proceed with Save while Editing a row.');
                $(this).popover('hide');
            }
        if(controlVal!=null){
            if($('.popover').hasClass('show')){
                $(this).attr('data-content','No description added.');
                $(this).popover('hide');
            }
            else
            {
                if(popupmesage!=null){
                    $(this).attr('data-content',popupmesage);
                }else{
                    $(this).attr('data-content',"No description added");
                }
                $(this).popover('show');
            }


            //$(this).next(".popover").find(".popover-content").html("Content");

        }
    })

    var populateActivityTypeDropdown=function(rowId){
        let customer_type_activity_code = {!! json_encode($dataMappingTable) !!};
        let activitySelection=$("#"+rowId+" .cpid-work-type").val()
        let dropDownValue=[];
        if(customer_type_activity_code[activitySelection]!=null){
            dropDownValue=customer_type_activity_code[activitySelection];
        }
        let dropDownText="";
        dropDownValue.forEach((value,key) => {
            dropDownText+=`<option value="${value["id"]}">${value["code"]}</option>`;
        });
        $("#"+rowId+" .active-code-field").html(dropDownText)
    }

    //remove cpid entry form table
    $("body").on("click", ".removeCpidEntry", function () {
        $(this).closest("tr").remove();
    });

    //handle supervisor time input field changes.
    $('.supervisor-time-fields').on('input',function(e){
        //update delta fields
        initiateDeltaCalculation();
    });
    //handle cpid item dropdown change
    $('body').on('change','.cpid-dropdown',function(e){
        let function_code = {!! json_encode($allocatedCpidFunctionCodes) !!};
        var target = ('#'+$(this).data('target'));
        var optionSelected = $("option:selected", this);
        $(target).find('.cpidRoleLabel').text(optionSelected.data('role-name'));
        let id= ((this).closest("tr").id);
        let dropDownValue=$(this).val();
        if(function_code[dropDownValue]!=null){
            $("#"+id+" .cpfunctions").val(function_code[dropDownValue])
        }else{
            $("#"+id+" .cpfunctions").val("")
        }
    });
    //on body click refresh sidemenu
    $('body').on('click',function(){
        refreshSideMenu();
    });
    //refresh the sidebar
    refreshSideMenu();

//is valid time string function
function isValidTimeString(timeString){
   var reg =  /^([0-9]{1,2}:[0-9]{1,2})$/g
   return reg.test(timeString);
}

//convert time to seconge eg: 00:20 -> 1200
function parsetTimeStringToSeconds(time){
    if(!time || !isValidTimeString(time)){
       console.log('Parse: Invalid time');
    }
    var seconds = 0;
    var timeArray = time.split(':');

    seconds += Number(timeArray[1] * 60); //convert minuts
    seconds += Number(timeArray[0] * 60 *60); //convert hours
    return seconds;
}

//convert seconds to time string eg: 1200 -> 00:20
function secondsToTimeString(seconds){
    var seconds = parseInt(seconds, 10); // don't forget the second param
    var hours   = Math.floor(seconds / 3600);
    var minutes = Math.floor((seconds - (hours * 3600)) / 60);
    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    return hours+':'+minutes;
}

// if a > b +ve | b > a -ve | a=b +ve
function computeTimeDifference(a,b){
    var _as = parsetTimeStringToSeconds(a);
    var _bs = parsetTimeStringToSeconds(b);
    var out = _as - _bs;
    return secondsToTimeString(Math.abs(out));
}

function initiateDeltaCalculation(){
    var originalTime = 'original-time';
    //delta for regular hours
    var deltaHours = $('#approvedTotalRegularHoursSelect').val();
    var deltaMinutes = $('#approvedTotalRegularMinutesSelect').val();
    var deltaTimehoursminutes = deltaHours+':'+deltaMinutes;
    $('#approvedTotalRegularHours').val(deltaTimehoursminutes);
    var _atrHours = $('#approvedTotalRegularHours');
    if(isValidTimeString(_atrHours.val())){
        $('#deltaRegularHours').val(computeTimeDifference($(_atrHours).data(originalTime),_atrHours.val()))
    }
    //delta for overtime hours
    var _atoHours = $('#approvedTotalOvertimeHours');
    if(isValidTimeString(_atoHours.val())){
        $('#deltaOvertimeHours').val(computeTimeDifference($(_atoHours).data(originalTime),_atoHours.val()))
    }
    //delta for statutory hours
    var _atsHours = $('#approvedTotalStatutoryHours');
    if(isValidTimeString(_atsHours.val())){
        $('#deltaStatutoryHours').val(computeTimeDifference($(_atsHours).data(originalTime),_atsHours.val()))
    }
}
function uuidv4() {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
    return v.toString(16);
  });
}

//insert new cpid row to the container.
function generateCpidRow(options) {
    var value = '00:00';
    var uuid = uuidv4();
    var optionsList = cpidOptionsList();
    let customer_id = {!! json_encode($customer_id) !!};
    var workTypeOptionsList = cpidWorkTypeOptionsList(customer_id);

    return '<tr id="'+uuid+'" class="cpid-fields-row"><td>'
    +'<input type="hidden" name="es_cpid[]" value="0"/>'
    +'<select name="cpids[]" data-target="'+uuid+'"class="form-control cpid-dropdown"><option value="">Select any</option>' + optionsList + '</select></td>'
    +'<td><input type="text"  name="cpidFunction[]" class="form-control cpfunctions" required readonly ></td>'
    + '<td><label class="cpidRoleLabel">-</label>'
    + '<td><select  style="display: inline-block;width:75%" name="cpidWorkType[]" required class="form-control cpid-work-type">'+workTypeOptionsList+'</select><i style="display: inline-block;padding-left:5px" class="fa fa-info-circle typedesc" data-toggle="popover" data-trigger="manual" data-content=""></i></td>'
    + '<td><select name="activCode[]" style="display:inline-block;width:75%" class="form-control active-code-field" id="activCode[]"></select><i  style="display: inline-block;padding-left:5px" class="fa fa-info-circle activcodedesc" data-toggle="popover" data-trigger="manual" data-content=""></i></td>'
    + '<td><input name="cpidTimes[]" type="text" placeholder="00:00" value = "' + value + '" class="form-control cpid-time-field" /></td>'
    + '<td><button type="button" class="btn btn-custom approval-button removeCpidEntry"><i style="color:white !important;" class="fa fa-trash"></i></button></td></tr>'
}

//trigger section
 $('.supervisor-time-fields').trigger('input'); //update deta feld on init
 $('body').find('.cpid-dropdown').trigger('change'); //trigger dropdown

    function prepareSubmitForm() {
        var weekPerArr = []
        var jsonContainer = document.getElementById("weeklyPerformance");
        var lookUpObj = document.getElementsByName("lookup[]");
        var lookUpObjLen = document.getElementsByName("lookup[]").length;
        var notesObj = document.getElementsByName("notes[]");
        var notesObjLen = document.getElementsByName("notes[]").length;
        for (var i = 0; i < lookUpObjLen; i++) {
            var obj = {
                lookup_id: lookUpObj[i].value,
                notes: notesObj[i].value
            }
            weekPerArr.push(obj);
        }
        $("#weeklyPerformance").val(JSON.stringify(weekPerArr));
    }

    function checkCpidFieldsConstrains(){
        //TODO
        var totalTime = 0;
        var totalCpidTime = 0;
        var totalCpidRegularTime = 0;
        var totalCpidOvertimeTime = 0;
        var totalCpidStatutoryTime = 0;
        var actvCodeCount=0;
        var validCpidFormat = true;
        //get total supervisor total times
        var totalRegularTime = parsetTimeStringToSeconds($('#approvedTotalRegularHours').val());
        var totalOvertime = parsetTimeStringToSeconds($('#approvedTotalOvertimeHours').val());
        //console.log($('#approvedTotalOvertimeHours').val());
        var totalStatutoryTime = parsetTimeStringToSeconds($('#approvedTotalStatutoryHours').val());
        //billable
        var billableOvertime = parsetTimeStringToSeconds($('#overtimeBillableHours').val());
        var billableStatutoryTime = parsetTimeStringToSeconds($('#statBillableHours').val());
        //get total cpid times
        var sumofApprovedHours=totalRegularTime;
        var sumOfBiferecatedHours=0;
        var emptyHours=0;
        let cpidRowCount = 0;
        var fieldRows = $('#cpidInjectionContainer').find('.cpid-fields-row');
            fieldRows.each(function(index,item){
                //check the time fields are valid
                cpidRowCount++;
                var actcodeval = $(this).find('.active-code-field').val();
                var acthoursval = $(this).find('.cpid-time-field').val();
                if(actcodeval==null){
                    actvCodeCount++;
                }
                var time = $(this).find('.cpid-time-field').val();
                if(time=="00:00"){
                    emptyHours++;
                }
                if (!isValidTime(time) ) {
                    validCpidFormat = false;
                    emptyHours++;
                    return false; //break the loop.
                    emptyHours++;
                }
                var workType = Number($(item).find('.cpid-work-type').val());
                sumOfBiferecatedHours+=parsetTimeStringToSeconds(time);
                if(workType === 1){
                    totalCpidRegularTime += parsetTimeStringToSeconds(time);
                }
                if(workType === 2){
                    totalCpidOvertimeTime += parsetTimeStringToSeconds(time);
                }
                if(workType === 3){
                    totalCpidStatutoryTime += parsetTimeStringToSeconds(time);
                }
                if(emptyHours>0){
                    swal("Warning", "CPID Hours should be greater than zero", "warning");
                    return false;
                }
            });
            // debugger
        //if cpid format are not valid
        if(!validCpidFormat){
            swal("Invalid Format", "Please enter hours in hh:mm format", "warning");
            return false;
        }
        if(sumofApprovedHours!=sumOfBiferecatedHours){
            swal("Invalid Format", "There is a mismatch in hours", "warning");
            return false;
        }
        if(cpidRowCount === 0)
        {
            swal("Invalid Format", "There is no CPID allocated", "warning");
            return false;
        }
        if(emptyHours>0){
            swal("Warning", "CPID Hours should be greater than zero", "warning");
            return false;
        }
        // //check cpid regular time filled
        // if(!(totalCpidRegularTime === totalRegularTime)){
        //     swal({
        //         title: 'Time Mismatch',
        //         text:  'Please check CPID regular time.',
        //         icon: "warning",
        //         type: 'warning',
        //         button: "OK",
        //     }, function () {
        //         return false;
        //     });
        //     return false;
        // }

        //  //check overtime is filled
        //  if(!(totalCpidOvertimeTime === totalOvertime)){
        //     swal({
        //         title: 'Time Mismatch',
        //         text:  'Please check CPID overtime.',
        //         icon: "warning",
        //         type: 'warning',
        //         button: "OK",
        //     }, function () {
        //         return false;
        //     });
        //     return false;
        // }

        //  //check overtime and billable is filled
        //  if(!(totalOvertime >= billableOvertime)){
        //     swal({
        //         title: 'Time Mismatch',
        //         text:  'Please check total overtime and billable over time.',
        //         icon: "warning",
        //         type: 'warning',
        //         button: "OK",
        //     }, function () {
        //         return false;
        //     });
        //     return false;
        // }
        // //check overtime and billable is filled
        // if(!(totalStatutoryTime >= billableStatutoryTime)){
        //     swal({
        //         title: 'Time Mismatch',
        //         text:  'Please check total statutory and billable statutory time.',
        //         icon: "warning",
        //         type: 'warning',
        //         button: "OK",
        //     }, function () {
        //         return false;
        //     });
        //     return false;
        // }
        // //check Statutory is filled
        // if(!(totalCpidStatutoryTime === totalStatutoryTime)){
        //     swal({
        //         title: 'Time Mismatch',
        //         text:  'Please check CPID statutory time.',
        //         icon: "warning",
        //         type: 'warning',
        //         button: "OK",
        //     }, function () {
        //         return false;
        //     });
        //     return false;
        // }
        if(actvCodeCount>0){
            swal({
                title: 'Active Code',
                text:  'Active code cannot be empty',
                icon: "warning",
                type: 'warning',
                button: "OK",
            }, function () {
                return false;
            });
            return false;
        }
        return true;
    }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if ("{{$prev}}" != "") {
            $('#prev_btn').removeAttr('disabled');
            $('#prev_btn').click(function () {
                window.location = "{{$prev}}";
            });
        }
        if ("{{$next}}" != "") {
            $('#nxt_btn').removeAttr('disabled');
            $('#nxt_btn').click(function () {
                window.location = "{{$next}}";
            });
        }

        var approval = {
            overtime: 0,
            statutory: 0
        }

        function approveAlert(overtimehours, statutoryhours) {

            var strMsg = "";
            var overtime = false;
            var statutory = false;
            let functionCodes=0;
            $(".cpfunctions").each( function (i, obj) {
                 if(obj.value==""){
                    functionCodes++;
                 }
            });
            if(functionCodes>0){
                swal("Warning","CPID Function Codes cannot be empty","warning")
            }
            else if (overtimehours == "00:00" && statutoryhours == "00:00") {
                formSubmit()
            } else {
                if (overtimehours != "00:00") {
                    overtime = true;
                }
                if (statutoryhours != "00:00") {
                    statutory = true;
                }
                if (overtime) {
                    formSubmit();
                } else if (statutory) {
                    statutoryPopUp();
                }
            }
        }

        function statutoryPopUp(){

            swal({
                title: "Alert",
                text: "Has the client approved billable stat time?" ,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnClickOutside: false,

            },function(statApprove){
                //console.log('inside');
                if (statApprove) {
                    approval.statutory = 1;
                    $('#clientApprovedBillableStatutory').val("1");
                }
                if(statApprove !== null){
                    formSubmit();
                }

            });
        }


        /* Posting data to TimesheetApprovalController - Start*/
        $('#approvalform').submit(function (e) {
            e.preventDefault();
            var hours = $('#approvedTotalRegularHoursSelect').val();
            var minutes = $('#approvedTotalRegularMinutesSelect').val();
            var timehoursminutes = hours+':'+minutes;
            $('#approvedTotalRegularHours').val(timehoursminutes);
            var regulartimehours =   $('#approvedTotalRegularHours').val();
            var overtimehours = $('#approvedTotalOvertimeHours').val();
            var statutoryhours = $('#approvedTotalStatutoryHours').val();
            var billableovertimehours = $('#overtimeBillableHours').val();
            var billablestathours = $('#statBillableHours').val();
            approveAlert(overtimehours, statutoryhours);
            // if (isValidTime(regulartimehours) && isValidTime(overtimehours) && isValidTime(statutoryhours)&& isValidTime(billableovertimehours) && isValidTime(billablestathours)) {
            //     approveAlert(overtimehours, statutoryhours);
            // } else {
            //     swal("Invalid Format", "Please enter hours in hh:mm format", "warning")
            // }

        });

        function formSubmit() {
            prepareSubmitForm();
            var $form = $(this);
            var formData = new FormData($('#approvalform')[0]);
            if(!checkCpidFieldsConstrains()){

                return;
            }
            $.ajax({
                url: '{{ route('approval.store')}}',
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: 'Approved',
                            text: 'The report approved successfully',
                            icon: "success",
                            type: 'success',
                            button: "OK",
                        }, function () {
                            window.location.reload();
                        });
                    } else {
                        console.log(data);
                        swal("Oops", "The report approval was unsuccessful", "warning");
                    }
                },
                fail: function (response) {
                    //alert('here');
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");

                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr);
                    //associate_errors(xhr.responseJSON.errors, $form);
                },
                contentType: false,
                processData: false,
            });
        }

        function isValidTime(time) {
            var patt = /^\d{2,}:\d{2}$/;
            var res = patt.test(time);
            return res;
        }

    });
</script>

<!-- Style -->
<style>
    .copy{
        margin-top:28% !important;
    }
    .approve-stamp{
        height: 120px;
        width: auto;
        object-fit: contain;
    }
    .approvers-list{
        max-height: 218px;
        overflow-y: scroll;
        overflow-x:hidden;
    }
    .cpid-dropdown{
        min-width: 170px;
    }
    .cpid-time-field{
        max-width: 84px;
    }
    .pr-33{
        padding-right: 33px;
    }
    .pr-14{
        padding-right: 14px;
    }
</style>

@stop
