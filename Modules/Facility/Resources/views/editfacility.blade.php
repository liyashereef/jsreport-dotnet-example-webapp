@extends('layouts.app')
@section('css')
<style>

    .addnew{
        margin-top:20px;
        margin-bottom:20px;
    }
    /* Style the tab */
.tab {
  overflow: hidden;

  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

.red{
    color:red;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}
.stop-scrolling {
  height: 100%;
  overflow: hidden;
}
/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  width: 100% !important;
  border-top: none;
}
.gj-modal .col-md-4{
    color: #38393a !important;
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" type="text/css" />
@endsection
@section('content')
<div class="container-fluid" style="margin-top:-7px;padding: 3px !important">
<div class="row">
    <div class="col-md-6">   <form action='{{route("cbs.editfacilitysignout")}}' id="editform" method="post">
        <div class="container-fluid" style="padding: 0px !important">

                <input type="hidden" name="errormessage" id="errormessage" value="@if($error){{json_encode($error,true)}}@endif" />


                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-12 table_title"><h4 style="margin:0px !important">Facility Signout</h4></div>

                </div>
                @csrf
                <input type="hidden" name="id" id="id" value="{{$id}}" />


                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-6">
                        Facility&nbsp;&nbsp;<i class="fa fa-question-circle" title="Facility Name" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="text-align: right" id="facility">
                        <input type="text" name="facility" class="form-control" value="{{$data->facility}}"  />
                        {!! $errors->first('facility', '<p class="help-block">:message</p>') !!}
                    </div>


                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px;display: none">
                    <div class="col-md-6">
                        Customer
                    </div>
                    <div class="col-md-6"  id="customer_id" style="">
                        <select name="customer_id" class="form-control"  >
                            <option value="0">Select Any</option>
                            @foreach ($customersarray as $key=>$value)
                                <option {{ old('customer_id') == 1 ? 'selected' : ''}} value="{{$key}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                            @endforeach
                        </select>
                        <label class="errorhtml" for="customer_id"></label>
                    </div>


                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-6">
                            Description
                    </div>
                    <div class="col-md-6" style="text-align: right" id="description">
                    <textarea name="description" class="form-control" rows="5"  >{{$data->description}}</textarea>
                    <label class="errorhtml" for="description">{!! $errors->first('description', '<p class="help-block">:message</p>') !!}</label>
                    </div>

                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                    <div class="col-md-6">
                        Maximum Booking Per Day (Hours)&nbsp;&nbsp;<i class="fa fa-question-circle" title="Maximum Booking Hours allowed for an individual , Eg :  0.5 = 30 minutes,1= hour etc" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="text-align: right" id="maxbooking_perday">
                        <input type="text" maxlength="5" class="form-control number" name="maxbooking_perday" value="{{$facilitydata->maxbooking_perday}}" />
                        <label class="errorhtml" for="maxbooking_perday">{!! $errors->first('maxbooking_perday', '<p class="help-block">:message</p>') !!}</label>
                    </div>

                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                    <div class="col-md-6">
                        Single Service Facility&nbsp;&nbsp;<i class="fa fa-question-circle" title="Facility provides multiple service or single like swimming pool,rooms etc." style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="text-align: right" id="single_service_facility">

                        <select class="form-control" name="single_service_facility">
                            <option value="">Select Any</option>
                            <option value="yes" @if ($data->single_service_facility == 1) {{ 'selected' }}@endif>Yes</option>
                            <option value="no" @if ($data->single_service_facility != 1) {{ 'selected' }} @endif>No</option>

                        </select>
                        <label class="errorhtml" for="single_service_facility">{!! $errors->first('single_service_facility', '<p class="help-block">:message</p>') !!}</label>
                    </div>
                </div>
                <div class="row ssf" style="margin-top: 20px;margin-bottom: 20px;
                @if ($data->single_service_facility == 1)

                @else
                    display:none
                @endif">

                    <div class="col-md-6" style="display: inline-block;vertical-align: top !important">
                        Maximum Occupancy Per Slot (Count)&nbsp;&nbsp;<i class="fa fa-question-circle" title="How many people allowed during an interval" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="display: inline-block;vertical-align: top !important;text-align: right" id="tolerance_perslot">
                        <input type="text" maxlength="3" class="form-control notdecimal" name="tolerance_perslot" value="{{$facilitydata->tolerance_perslot}}" />
                        <label class="errorhtml" for="tolerance_perslot">{!! $errors->first('tolerance_perslot', '<p class="help-block">:message</p>') !!}</label>
                    </div>
                </div>
                <div class="row ssf" style="margin-top: 20px;margin-bottom: 20px;
                @if ($data->single_service_facility == 1)

                @else
                    display:none
                @endif">

                    <div class="col-md-6">
                        Booking Interval&nbsp;(Hours)&nbsp;<i class="fa fa-question-circle" title="Interval allowed per slot" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="text-align: right" id="slot_interval">
                        <input type="text" maxlength="5"  class="form-control number" name="slot_interval" value="{{$slot_interval}}" />
                        <label class="errorhtml" for="slot_interval">{!! $errors->first('slot_interval', '<p class="help-block red">:message</p>') !!}</label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                    <div class="col-md-6">
                        Booking Window&nbsp;&nbsp;&nbsp;<i class="fa fa-question-circle" title="Maximum open days allowed for booking Eg : 5 means you can book for next 5 days" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="text-align: right" id="booking_window">
                        <input type="text" maxlength="2"  class="form-control notdecimal" value="{{$booking_window}}" name="booking_window" />
                        <label class="errorhtml" for="booking_window"></label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                    <div class="col-md-6">
                        Weekend Booking&nbsp;&nbsp;&nbsp;<i class="fa fa-question-circle" title="Weekend booking allowed or not" style="cursor:pointer" aria-hidden="true"></i>
                    </div>
                    <div class="col-md-6" style="text-align: right" id="weekend_booking">
                        <select class="form-control" name="weekend_booking">
                            <option value="">Select Any</option>
                            <option value="yes" @if ($facilitydata->weekend_booking == 1) {{ 'selected' }}@endif>Yes</option>
                            <option value="no" @if ($facilitydata->weekend_booking != 1) {{ 'selected' }} @endif>No</option>

                        </select>
                        <label  class="errorhtml" for="weekend_booking">{!! $errors->first('weekend_booking', '<p class="help-block">:message</p>') !!}</label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">

                    <div class="col-md-6">
                        Restrict Booking
                    </div>
                    <div class="col-md-6" style="text-align: right" id="restrict_booking">

                        <select class="form-control" name="restrict_booking">
                            <option value="">Select Any</option>
                            <option value="yes" @if ($data->restrict_booking == 1) {{ 'selected' }}@endif>Yes</option>
                            <option value="no" @if ($data->restrict_booking != 1) {{ 'selected' }} @endif>No</option>

                        </select>
                        <label class="errorhtml" for="restrict_booking"></label>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                    <div class="col-md-6">
                        Active
                    </div>
                    <div class="col-md-6" style="text-align: right" id="active">

                        <select class="form-control" name="active">
                            <option value="">Select Any</option>
                            <option value="yes" @if ($data->active == 1) {{ 'selected' }}@endif>Yes</option>
                            <option value="no" @if ($data->active != 1) {{ 'selected' }} @endif>No</option>

                        </select>
                        <label class="errorhtml" for="active"></label>
                    </div>
                </div>


                <div class="row" style="margin-top: 20px;margin-bottom: 20px">


                    <div class="col-md-12" style="text-align: right">
                                 <button class="btn btn-primary" id="savebutton">Save</button>
                                 <button class="btn btn-primary" id="cancelbutton">Cancel</button>
                    </div>
                </div>
            </form>
        </div></form>
    </div>
    <div class="col-md-6" style="">

        <div class="container-fluid" style="margin-top: 65px;background: #f1f1f1 " >
            <div class="row">
                <div class="tab">
                    <button class="tablinks" id="tmbutton" onclick="openCity(event, 'timing')">Timings</button>
                    <button class="tablinks" onclick="openCity(event, 'prereq')">Prerequisites</button>
                    <button class="tablinks" onclick="openCity(event, 'policy')">Facility Policy
                    </button>
                    <button class="tablinks" onclick="openCity(event, 'lockdown')">Lockdown
                    </button>
                  </div>
            </div>
            <div class="row">

                  <div id="timing" class="tabcontent container_fluid">
                    <div class="row">

                        <div class="col-md-3 addnew">
                        <button type="button" style="" class="btn btn-primary addnewtiming ">Add New</button>
                        &nbsp;&nbsp;&nbsp;
                        <i data-placement="bottom" class="fa fa-question-circle" title="Working Time of Facility ,
                        Start time and End time should be provided.
                        Should be greater than or equal to Maximum Booking Hours,Slot Interval etc,
                        Week day timing and weekend service timing can be defined seperately" style="cursor:pointer" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="timingtable">
                                <thead>
                                    <th>#</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Start Date</th>
                                    <th>Expiry Date</th>
                                    <th>Day Type</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @foreach ($facilitytiming as $timings)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>
                                                {{date("h:i A",strtotime(date("Y-m-d").$timings->start_time))}}
                                            </td>
                                            <td>
                                                {{date("h:i A",strtotime(date("Y-m-d").$timings->end_time))}}
                                            </td>
                                            <td>{{date("d M, Y",strtotime($timings->start_date))}}</td>
                                            <td>
                                                @if ($timings->expiry_date!="")
                                                    {{date("d M, Y",strtotime($timings->expiry_date))}}
                                                @endif

                                            </td>
                                            <td>
                                                @if ($timings->weekend_timing==0)
                                                    Weekday
                                                    @else
                                                    Weekend
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($timings->expiry_date))
                                                <button attr-id="{{$timings->id}}"
                                                    attr-start_time="{{date('h:i A', strtotime(date("Y-m-d")." ".$timings->start_time))}}"
                                                    attr-end_time="{{date('h:i A', strtotime(date("Y-m-d")." ".$timings->end_time))}}"
                                                    attr-expiry_time="{{$timings->expiry_date}}"
                                                    attr-weekend_timing="{{$timings->weekend_timing}}" class="btn btn-primary edittiming">Edit</button>
                                                <button attr-id="{{$timings->id}}" class="btn btn-primary removetiming">Remove</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>

                  <div id="prereq" class="tabcontent container_fluid">
                    <div class="row">

                        <div class="col-md-3 addnew">
                        <button type="button" style="" class="btn btn-primary addrequisite ">Add New</button>
                        &nbsp;&nbsp;&nbsp;
                        <i data-placement="bottom" class="fa fa-question-circle" title="Prerequisite of facility with the User is defined here."
                        style="cursor:pointer" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="requisitetable">
                                <thead>
                                    <th>Prerequisite</th>

                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @foreach ($facilityprequisite as $requisite)
                                        <tr>
                                            <td id="req-{{$requisite->id}}">{{$requisite->requisite}}</td>

                                            <td>
                                            <button attr-id="{{$requisite->id}}"

                                                     class="btn btn-primary editrequisite">Edit</button>
                                                <button attr-id="{{$requisite->id}}" class="btn btn-primary removerequisite">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>

                  <div id="policy" class="tabcontent container_fluid">
                        <div class="row">

                            <div class="col-md-3 addnew">
                            <button type="button" style="" class="btn btn-primary addpolicy">Add New</button>
                            &nbsp;&nbsp;&nbsp;
                            <i data-placement="bottom" class="fa fa-question-circle" title="Working Time of Facility ,
                            Facility Policy and rules defined here." style="cursor:pointer" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered" id="policytable">
                                    <thead>
                                        <th>Policy</th>

                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($facilitypolicy as $policy)
                                            <tr>
                                                <td id="pol-{{$policy->id}}">{{$policy->policy}}</td>

                                                <td>
                                                <button attr-id="{{$policy->id}}"

                                                        class="btn btn-primary editpolicy">Edit</button>
                                                    <button attr-id="{{$policy->id}}" class="btn btn-primary removepolicy">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                  </div>

                  <div id="lockdown" class="tabcontent container_fluid">
                    <div class="row">

                        <div class="col-md-3 addnew">
                        <button type="button" style="" class="btn btn-primary addnewlockdown ">Add New</button>
                        &nbsp;&nbsp;<i data-placement="bottom" class="fa fa-question-circle" title="Lockdown of facility .
                        Time only means that specific will not be allowed till its deleted.
                        "
                        style="cursor:pointer" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="lockdowntable">
                                <thead>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @foreach ($facilityservicelockdown as $lockdown)
                                        <tr>
                                            <td>
                                                @if($lockdown->start_date!="")
                                            {{date("d M, Y",strtotime($lockdown->start_date))}}
                                            @endif
                                            </td>
                                            <td>
                                            @if($lockdown->end_date!="")
                                            {{date("d M, Y",strtotime($lockdown->end_date))}}
                                            @endif
                                            </td>
                                            <td>
                                            @if($lockdown->start_time!=""  && $lockdown->start_time!="00:00:00")

                                            {{date("h:i A",strtotime(date("Y-m-d").$lockdown->start_time))}}
                                            @endif
                                            </td>
                                            <td>
                                               @if($lockdown->end_time!="" && $lockdown->end_time!="00:00:00")
                                               {{date("h:i A",strtotime(date("Y-m-d").$lockdown->end_time))}}
                                            @endif
                                            </td>
                                            <td>
                                                <button attr-id="{{$lockdown->id}}"
                                                    @if ($lockdown->start_date!="")
                                                       attr-start_date="{{$lockdown->start_date}}"
                                                       attr-end_date="{{$lockdown->end_date}}"
                                                    @else
                                                        attr-start_date=""
                                                        attr-end_date=""
                                                    @endif
                                                    @if ($lockdown->start_time!="")
                                                        attr-start_time="{{date('h:i A', strtotime(date("Y-m-d")." ".$lockdown->start_time))}}"
                                                        attr-end_time="{{date('h:i A', strtotime(date("Y-m-d")." ".$lockdown->end_time))}}"
                                                    @else
                                                        attr-start_time=""
                                                        attr-end_time=""
                                                    @endif

                                                    class="btn btn-primary editlockdown">Edit</button>
                                                <button attr-id="{{$lockdown->id}}" class="btn btn-primary removelockdown">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>
            </div>




        </div>
    </div>
</div>

</div>

<form name="addnewtiming"  id="addnewtiming" action="" method="POST">
    <div class="container_fluid managetiming" id="managetiming">
        <input type="hidden" id="service_id" name="service_id" value="{{$id}}" />
        <input type="hidden" name="booking_window"  id="booking_window" value="{{$booking_window}}" />
        <div class="row form-group">
            <div class="col-md-4">
                Start Time
            </div>
            <div class="col-md-8">
                <input type="text" id="st_time" name="st_time" class="form-control timepick" re value="" />
                <input type="hidden" id="edit_facility_id" name="edit_facility_id" class="form-control" value="" />
                <input type="hidden" id="edit_timing_id" name="edit_timing_id" class="form-control" value="" />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-4">
                End Time
            </div>
            <div class="col-md-8">
                <input type="text" id="en_time" name="en_time" class="form-control timepick" value="" />
            </div>
        </div>
        <div class="row form-group" id="dtype">
            <div class="col-md-4 ">
                Day Type
            </div>
            <div class="col-md-8">
                <select name="weekend_timing" id="weekend_timing" class="form-control">

                    <option value="false">Weekday</option>
                    <option value="true">Weekend</option>
                </select>
            </div>
        </div>
        <div class="row form-group" id="">

            <div class="col-md-12" style="text-align: center">
                <button type="button" class="btn btn-primary timingbuttons" id="savetiming">Save</button>

            </div>
        </div>
    </div>
    </form>

    <form name="addnewlockdown"  id="addnewlockdown" action="" method="POST">
        <div class="container_fluid managelockdown" id="managelockdown">
            <input type="hidden" id="service_id" name="service_id" value="{{$id}}" />
            <input type="hidden" name="booking_window"  id="booking_window" value="{{$booking_window}}" />
            <div class="row form-group">
                <div class="col-md-4">
                    Start Date
                </div>
                <div class="col-md-8">
                    <input type="text" required id="st_date" name="st_date" class="form-control datepicker" value="" data-date-format="yyyy-mm-dd H:i" />
                    <input type="hidden" id="edit_lockdownfacility_id" name="edit_lockdownfacility_id" class="form-control" value="" data-date-format="yyyy-mm-dd H:i" />
                    <span for="st_date"></span>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4">
                    Start Time
                </div>
                <div class="col-md-8">
                    <input type="text" required id="st_time" name="st_time" class="form-control timepick" value=""  />
                    <span class="" for="st_time"></span>

                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4">
                    End Date
                </div>
                <div class="col-md-8">
                    <input type="text" required id="en_date" name="en_date" class="form-control datepicker" value="" />
                    <span for="en_date"></span>

                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4">
                    End Time
                </div>
                <div class="col-md-8">
                    <input type="text" required id="en_time" name="en_time" class="form-control timepick" value="" />
                    <span for="en_time"></span>

                </div>
            </div>
            <div class="row form-group" id="">

                <div class="col-md-12" style="text-align: center">
                    <button type="button" class="btn btn-primary timingbuttons" id="savelockdown">Save</button>

                </div>
            </div>
        </div>
        </form>

    <form name="addnewrequisite"  id="addnewrequisite" action="" method="POST">
        <div class="container_fluid managerequisite" id="managerequisite">
            <input type="hidden" id="service_id" name="service_id" value="{{$id}}" />


                <div class="row form-group">

                <div class="col-md-12">
                    <textarea id="requisite" name="requisite" class="form-control " placeholder="Prerequisite"></textarea>
                    <input type="hidden" id="edit_requisite_id" name="edit_requisite_id" class="form-control" value="" />
                </div>
            </div>


            <div class="row form-group" id="">

                <div class="col-md-12" style="text-align: center">
                    <button type="button" class="btn btn-primary timingbuttons" id="saverequisite">Save</button>

                </div>
            </div>
        </div>
        </form>

        <form name="addnewpolicy"  id="addnewpolicy" action="" method="POST">
            <div class="container_fluid managepolicy" id="managepolicy">
                <input type="hidden" id="service_id" name="service_id" value="{{$id}}" />

                    <div class="row form-group">

                    <div class="col-md-12">
                        <textarea id="policytext" name="policytext" class="form-control " placeholder="Corporate Policy" maxlength="150"></textarea>
                        <input type="hidden" id="edit_policy_id" name="edit_policy_id" class="form-control" value="" />
                    </div>
                </div>


                <div class="row form-group" id="">

                    <div class="col-md-12" style="text-align: center">
                        <button type="button" class="btn btn-primary timingbuttons" id="savepolicy">Save</button>

                    </div>
                </div>
            </div>
            </form>

@stop
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
<script src="{{ asset('js/timepicki.js') }}"></script>
@include('facility::partials.common')
<script>
    $(document).ready(function () {

        let successmessage =  {!! json_encode($error) !!};


        $( ".fa-question-circle" ).tooltip();
        $("#timingtable").dataTable({
            "order": [[ 0, "asc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
        });
        $("#requisitetable").dataTable({
            autoWidth: false,
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columnDefs: [
                { width: "70%", targets: 0 },
                { width: "30%", targets: 1 },
            ],
            sorting:false,
        });

        $("#policytable").dataTable({
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columnDefs: [
                { width: "70%", targets: 0 },
                { width: "30%", targets: 1 },
            ],
            sorting:false,
            autoWidth: false
        });

        var customer_id = {{$data->customer_id}};
        if(customer_id>0){
            $('select[name="customer_id"]').val(customer_id).select2();
        }else{
            $('select[name="customer_id"]').select2();
        }

        $("#tmbutton").addClass("active");
        $("#timing").css("display","block")

        if(successmessage!=null){
            swal(successmessage["success"],successmessage["message"],successmessage["success"]);
        }

    });

    $(document).on("click",".gj-dialog-md-close",function(e){
        $('body').removeClass('stop-scrolling')
    })

    $(document).on("change","select[name=single_service_facility]",function(e){
        if($(this).val()=="yes"){
            $(".ssf").show();
        }else{
            $(".ssf").hide();
        }
    })

    const convertTime12to24 = (time12h) => {
    const [time, modifier] = time12h.split(' ');

    let [hours, minutes] = time.split(':');

    if (hours === '12') {
        hours = '00';
    }

    if (modifier === 'PM') {
        hours = parseInt(hours, 10) + 12;
    }

    return `${hours}:${minutes}`;
    }
    $("#managerequisite").dialog({
                autoOpen: false,
                width:"500",
                title: "<h4>Manage Prerequisite</h4>",
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                draggable: false,
                close: function() {
                    alert('close');
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').addClass('custom-overlay');

                }
            });

            $("#managetiming").dialog({
                autoOpen: false,
                width:"650",
                title: "<h4>Manage Timings</h4>",
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },

                draggable: false,
                close: function() {
                    alert('close');
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').addClass('custom-overlay');

                }
            })
            $("#managelockdown").dialog({
                autoOpen: false,
                width:"650",
                title: "<h4>Manage Lockdown</h4>",
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                draggable: false,
                close: function() {
                    alert('close');
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').addClass('custom-overlay');

                }
            });


            $("#managepolicy").dialog({
                autoOpen: false,
                width:"500",
                title: "<h4>Manage Policy</h4>",
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                draggable: false,
                close: function() {
                    alert('close');
                },
                modal: true,
                open: function() {
                    $('.ui-widget-overlay').addClass('custom-overlay');

                }
            });
    $(document).on("click",".addnewtiming",function(e){
        e.preventDefault();
        $("#addnewtiming")[0].reset();
        $("#dtype").show();
        var dialog = $('#managetiming');

        $("#edit_facility_id").val("");
        $("#edit_timing_id").val("");
        dialog.dialog('open').after(function(e){
            $('body').addClass('stop-scrolling')
        });
    })

    $(document).on("click",".addnewlockdown",function(e){
        e.preventDefault();
        $("#addnewlockdown")[0].reset()
        var dialog = $('#managelockdown');

        $("#edit_lockdownfacility_id").val("");
        dialog.dialog('open').after(function(e){
            $('body').addClass('stop-scrolling')
        });
    });

    $(document).on("click",".editlockdown",function(e){
        e.preventDefault();
        $("#addnewlockdown")[0].reset()
        var dialog = $('#managelockdown');
        var st_date = $(this).attr("attr-start_date");
        var en_date = $(this).attr("attr-end_date");
        var st_time = $(this).attr("attr-start_time");
        var en_time = $(this).attr("attr-end_time");
        $("#addnewlockdown [name=st_date]").val(st_date);
        $("#addnewlockdown [name=en_date]").val(en_date);
        $("#addnewlockdown [name=st_time]").val(st_time);
        $("#addnewlockdown [name=en_time]").val(en_time);
        $("#edit_lockdownfacility_id").val($(this).attr("attr-id"));
        dialog.dialog('open').after(function(e){
            $('body').addClass('stop-scrolling')
        });
    });

    $("#savelockdown").click(function (e) {
        e.preventDefault();

        var formdata = $("#addnewlockdown").serialize();
        var stdate = ($("#addnewlockdown [name=st_date]").val());
        var endate = $("#addnewlockdown [name=en_date]").val();
        var sttime = convertTime12to24($("#addnewlockdown [name=st_time]").val());
        var entime = convertTime12to24($("#addnewlockdown [name=en_time]").val());
        var timing_id = $("#edit_lockdown_facility_id").val();
        var service_id = $("#service_id").val();
        if((sttime>entime && sttime!="" && sttime!="") ) {
            swal("Warning","Start time should be less than end time ","warning");
        }
        else if(stdate=="" && $("#addnewlockdown[name=st_time]").val()=="" &&
         $("#addnewlockdown[name=st_time]").val()=="" &&
         $("#addnewlockdown [name=en_time]").val()==""){
            swal({
            title: "Warning",
            text: "Please fill in any of date/time combination",
            type: "warning",
            confirmButtonText: "Ok"
        });
        }
        else if(stdate!="" && (endate<stdate)){
            swal({
            title: "Warning",
            text: "End date should be greater than or equal to start date ",
            type: "warning",
            confirmButtonText: "Ok"
        });
        }
        else{
            $.ajax({
            type: "post",
            url: "{{route('cbs.savefacilityservicelockdown')}}",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code=="200"){
                    $("#managetiming").dialog("close");
                    swal({
                    title: "Success",
                    text: data.message,
                    type: "success"
                    }, function() {
                        location.reload();
                    });
                }else{
                    swal("Warning",data.message,data.success);
                }
            }
        }).fail(function(data){

            var response = JSON.parse(data.responseText);
            $(".error").html("");
            $.each( response.errors, function( key, value) {

            var errorString = '<ul>';
            errorString += '<li>' + value + '</li>';
            var labelfor = $('span[for="' + $("#"+key).attr('id') + '"]');
            $(labelfor).html(errorString).addClass("red");
            });

            });
        }

    });

    $(document).on("click",".addrequisite",function(e){
        e.preventDefault();
        $("#addnewrequisite")[0].reset()
        var dialog = $('#managerequisite');

        $("#service_id").val("");
        dialog.dialog('open').after(function(e){

        });
    })

    $(document).on("click",".addpolicy",function(e){
        e.preventDefault();
        $("#addnewpolicy")[0].reset()
        var dialog = $('#managepolicy');

        $("#edit_requisite_id").val("");
        dialog.dialog('open').after(function(e){

        });
    })

    $(document).on("click",".edittiming",function(e){
        e.preventDefault();
        $("#addnewtiming")[0].reset()
        $("#dtype").hide();
        var managedialog = $('#managetiming');
        var st_time = $(this).attr("attr-start_time");
        var en_time = $(this).attr("attr-end_time");
        var expiry = $(this).attr("attr-expiry_time");
        $("#st_time").val(st_time);
        $("#en_time").val(en_time);
        $("#edit_facility_id").val($(this).attr("attr-id"));
        $("#edit_timing_id").val($(this).attr("attr-id"));
        var weektime = $(this).attr("attr-weekend_timing")
        if(weektime==1)
        {
            $("#weekend_timing").val("true");
        }else{
            $("#weekend_timing").val("false");
        }

        managedialog.dialog('open').after(function(e){
            $('body').addClass('stop-scrolling')
        })
    })
    $(document).on('keydown', function(event) {
       if (event.key == "Escape") {
            $('body').removeClass('stop-scrolling')
       }
   });
    $(document).on("click",".editrequisite",function(e){
        e.preventDefault();
        $("#addnewrequisite")[0].reset()
        var dialog = $('#managerequisite');
        var requisite = $("#req-"+$(this).attr("attr-id")).html() ;

        $("#requisite").val(requisite);

        $("#edit_requisite_id").val($(this).attr("attr-id"));
        dialog.dialog('open').after(function(e){

        })
    })

    $(".removerequisite").click(function (e) {
        e.preventDefault();
        var self = this;
        swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },function(unalloc) {
                if (unalloc){
                    $.ajax({
                    type: "post",
                    url: "{{route('cbs.removefacilityprerequisite')}}",
                    data: {model_id:$(self).attr("attr-id")},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var data = jQuery.parseJSON(response);
                        if(data.code=="200"){

                                swal({
                                title: "Deleted",
                                text: "Prerequisite removed successfully",
                                type: "success"
                                }, function() {
                                    location.reload();
                                });

                            }else{
                                swal("Warning",data.message,"warning")
                            }
                    }
                });
                }})

    });


    $(".removetiming").click(function (e) {
        e.preventDefault();
        var self = this;
        swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },function(unalloc) {
                if (unalloc){
                    $.ajax({
            type: "post",
            url: "{{route('cbs.removeservicetiming')}}",
            data: {model_id:$(self).attr("attr-id"),model_type:"facility",facility:$("#id").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code==200){
                        swal({
                        title: "Deleted",
                        text: data.message,
                        type: "success"
                        }, function() {
                            location.reload();
                        });

                    }else{
                        swal({
                        title: "Warning",
                        text: data.message,
                        type: "warning"
                        }, function() {

                        });
                    }
            }
        }).fail(function(res){
            alert("fail")
        });
                }})

    });

    $(".removelockdown").click(function (e) {
        e.preventDefault();
        var self = this;
        swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },function(unalloc) {
                if (unalloc){
                    $.ajax({
                        type: "post",
                        url: "{{route('cbs.removefacilityservicelockdown')}}",
                        data: {model_id:$(self).attr("attr-id"),model_type:"facility"},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            var data = jQuery.parseJSON(response);
                            if(data.code=="200"){

                                    swal({
                                    title: "Deleted",
                                    text: "Lockdown removed successfully",
                                    type: "success"
                                    }, function() {
                                        location.reload();
                                    });

                                }else{
                                    swal("Warning",data.message,data.success)
                                }
                        }
                    });
                }})

    });


    $("#saverequisite").click(function (e) {
        e.preventDefault();
        var formdata = $("#addnewrequisite").serialize();

        var requisite = $("#requisite").val();

        if(requisite.replace(" ","")=="") {
            swal("Warning","Please fill in a Prerequisite","warning");
        }else{
            $.ajax({
            type: "post",
            url: "{{route('cbs.savefacilityprerequisite')}}",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code=="200"){
                    $("#managerequisite").dialog("close");
                    swal({
                    title: "Success",
                    text: data.message,
                    type: "success"
                    }, function() {
                        location.reload();
                    });
                }else{
                    swal("Warning",data.message,data.success)
                }
            }
        });
        }

    });

    $(document).on("click",".editpolicy",function(e){
        e.preventDefault();
        $("#addnewpolicy")[0].reset()
        var dialog = $('#managepolicy');
        var policy = $("#pol-"+$(this).attr("attr-id")).html() ;

        $("#policytext").html(policy);

        $("#edit_policy_id").val($(this).attr("attr-id"));
        dialog.dialog('open').after(function(e){

        })
    })

    $(document).on("click","#savebutton",function(e){
        e.preventDefault();
        $('.errorhtml').html("");
        var formdata = $("#editform").serialize();
        $.ajax({
            type: "post",
            url: "{{route("cbs.editfacilitysignout")}}",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code=="200"){

                        swal({
                        title: "Success",
                        text: data.message,
                        type: "success"
                        }, function() {
                            location.href = {!! json_encode(route("cbs.facilities")) !!}
                        });

                    }else{
                        swal("Warning",data.message,data.success)
                    }
            }
        }).fail(function(data){

                        var response = JSON.parse(data.responseText);
                        $(".error").html("");
                        $.each( response.errors, function( key, value) {

                        var errorString = '<ul>';
                        errorString += '<li>' + value + '</li>';
                        var labelfor = $('label[for="' + $("#"+key).attr('id') + '"]');
                        $(labelfor).html(errorString).addClass("red");
                        });

                });
    });

    $(".removepolicy").click(function (e) {
        e.preventDefault();
        var self = this;
        swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },function(unalloc) {
                if (unalloc){
                    $.ajax({
                        type: "post",
                        url: "{{route('cbs.removefacilitypolicy')}}",
                        data: {model_id:$(self).attr("attr-id")},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            var data = jQuery.parseJSON(response);
                            if(data.code=="200"){

                                    swal({
                                    title: "Removed",
                                    text: "Policy removed successfully",
                                    type: "success"
                                    }, function() {
                                        location.reload();
                                    });

                                }else{
                                    swal("Warning",data.message,data.success)
                                }
                        }
                    });
                }})

    });

    $("#savepolicy").click(function (e) {
        e.preventDefault();
        var formdata = $("#addnewpolicy").serialize();

        var policy = $("#policytext").val();

        if(policy.replace(" ","")=="") {
            swal("Warning","Policy cannot be empty","warning");
        }else{
            $.ajax({
            type: "post",
            url: "{{route('cbs.savefacilitypolicy')}}",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code=="200"){
                    $("#managepolicy").dialog("close");
                    swal({
                    title: "Success",
                    text: data.message,
                    type: "success"
                    }, function() {
                        location.reload();
                    });
                }else{
                    swal("Warning",data.message,data.success)
                }
            }
        });
        }

    });

    $("#savetiming").click(function (e) {
        e.preventDefault();
        var formdata = $("#addnewtiming").serialize();
        var sttime = convertTime12to24($("#st_time").val());
        var entime = convertTime12to24($("#en_time").val());
        var booking_window = $("#booking_window").val();
        var timing_id = $("#edit_facility_id").val();
        var service_id = $("#service_id").val();
        if($("#st_time").val()=="" || $("#en_time").val()=="") {
            swal("Warning","Start time and end time are mandatory","warning");
        }
        else if(sttime>=entime) {
            swal("Warning","Start time should be less than end time","warning");
        }else{
            $.ajax({
            type: "post",
            url: "{{route('cbs.savefacilityservicetiming')}}",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if(data.code=="200"){
                    $("#managetiming").dialog("close");
                    swal({
                    title: "Success",
                    text: data.message,
                    type: "success"
                    }, function() {
                        location.reload();
                    });
                }else{
                    swal("Warning",data.message,data.success)
                }
            }
        });
        }

    });



    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    $(".timepick").timepicker({
            timeFormat: 'h:i A',
            step: 15,
            disableTextInput:true,
        });

        $("#lockdowntable").dataTable({
            autoWidth: false,
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columnDefs: [
                { width: "20%", targets: 0 },
                { width: "20%", targets: 1 },
                { width: "15%", targets: 2 },
                { width: "15%", targets: 3 },
            ]});


$(document).on("click","#cancelbutton",function(e){
    e.preventDefault()
    location.href={!! json_encode(route("cbs.facilities")) !!}; ;
})


    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" ></script>
@endsection
