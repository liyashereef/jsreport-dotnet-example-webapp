@extends('layouts.app') @section('content')
<style>

    .sticky-th {
        position: -webkit-sticky; position: sticky; top: 0; z-index: 2;
    }

.schedule-overview th{
    color: white;
}

.span-dot {
  height: 10px;
  width: 10px;
  border-radius: 50%;
  display: inline-block;
}


.live_status_1 {
    background-color: #21a71d !important;
}

.live_status_2 {
    background-color: #f8b30e !important;
}

.live_status_3 {
    background-color: #d21a1a !important;
}


</style>
<div class="table_title">
    <h4> Customer Details </h4>
</div>

<div class="form-group row content-holder">
    <div class="col-sm-8" id="postingdetails">
        <input type="hidden" name="customer-contract-type" id="customer-contract-type" value="" />
        <span style="padding-left: 10px;" id="posting-detail-validation" class="help-block text-danger"></span>
        <input type="hidden" name="timeoff_requestid" id="timeoff_requestid" value="{{$timeoff_id}}" />
        <input type="hidden" name="timeoffcustomer" id="timeoffcustomer" value="{{$timeoff_customer}}" />
        <input type="hidden" name="timeoffcustomerstc" id="timeoffcustomerstc" value="{{$timeoff_customerstc}}" />
        <input type="hidden" name="timeoffstartdate" id="timeoffstartdate" value="{{$timeoff_startdate}}" />
        <input type="hidden" name="timeoffstarttime" id="timeoffstarttime" value="{{$timeoff_starttime}}" />
        <input type="hidden" name="timeoffenddate" id="timeoffenddate" value="{{$timeoff_enddate}}" />
        <input type="hidden" name="timeoffendtime" id="timeoffendtime" value="{{$timeoff_endtime}}" />
        <input type="hidden" name="timeoff_payrate" id="timeoff_payrate" value="{{$timeoff_payrate}}" />
        <input type="hidden" name="employeename" id="employeename" value="{{$employeename}}" />
        <input type="hidden" name="employeetimeoffreference" id="employeetimeoffreference" value="{{$employeetimeoffreference}}" />
        <input type="hidden" name="timeoff_formattedstartdate" id="timeoff_formattedstartdate" value="{{$timeoff_formattedstartdate}}" />
        <input type="hidden" name="timeoff_formattedenddate" id="timeoff_formattedenddate" value="{{$timeoff_formattedenddate}}" />
    </div>
</div>


<div class="modal fade" id="overtimeNotesModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><input type="hidden" name="requirements" value="" id="requirement_id_hidden">
                <h4 class="modal-title" id="myModalLabel">Overtime Notes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="overtime_notes" class="form-group">
                    <label for="overtime_notes" class="col-sm-12 control-label">Overtime Notes</label>
                    <div class="col-sm-12">
                       {{ Form::textarea('overtime_notes',null, array('class'=>'form-control', 'placeholder' => 'Overtime Notes')) }}
                        <small class="help-block" id="overtime_validation" style="display:none;"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class='button btn btn-primary blue' id='overtime_notes_submit'>Save Notes</button>
            </div>
        </div>
    </div>
</div>

{{ Form::open(array('id'=>'schedule-customer-requirements-form','class'=>'', 'method'=> 'POST')) }} {{csrf_field()}} {{ Form::hidden('id',null)}}

<div id="project-no-new-stc" class="form-group row content-holder" style="display:none;margin-top: -30px;">
    {{ Form::hidden('cust_id') }}

    <label for="customer_id" class="col-sm-3 col-form-label">Choose Project Number </label>
    <div class="col-sm-3" id="customer_id">
         <span id="customer_id_label"></span>
        <select name="customer_id" class="form-control" required>
            <option selected disabled>Please Select</option>
        </select>
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('permanent_id', ':message') !!}</div>
    </div>

    <label class="col-sm-3 col-form-label empidblock" for="employeeid">Employee Name</label>
    <div class="col-sm-3" id="empname" class="empidblock">
    </div>
    @can('create-stc-customer')
    <div class="col-sm-3" id="new-stc-button" style="display:none;">
        <button type="button" onclick="addnew()" class="btn submit">
            <i class="fa fa-refresh"></i> New Schedule</button>
    </div>
    @endcan
</div>

<div id="project-no-details" style="display:none;" class="content-holder">
    <div class="form-group row">
        <label for="client_name" class="col-sm-3 col-form-label">Client Name</label>
        <div class="col-sm-3">
            <input type="text" max="50" class="form-control " name="client_name" value="{{old('client_name')}}" placeholder="Client Name"
                readonly>
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('client_name', ':message') !!}</div>
        </div>
        <label for="site_city" class="col-sm-3 col-form-label">Site City</label>
        <div class="col-sm-3">
            <input type="text" max="50" class="form-control " name="site_city" value="{{old('site_city')}}" placeholder="Site City" readonly>
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_city', ':message') !!}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="site_address" class="col-sm-3 col-form-label">Site Address</label>
        <div class="col-sm-3">
            <input type="text" max="50" class="form-control " name="site_address" value="{{old('site_address')}}" placeholder="Site Address" readonly>
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_address', ':message') !!}</div>
        </div>
        <label for="site_postal_code" class="col-sm-3 col-form-label">Site Postal Code</label>
        <div class="col-sm-3">
            <input type="text" max="50" class="form-control " name="site_postal_code" value="{{old('site_postal_code')}}" placeholder="Site Postal Code"
                readonly>
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_postal_code', ':message') !!}</div>
        </div>
    </div>
    <div class="form-group row" id="nmso_account_details">
        <label for="customer_nmso_account" class="col-sm-3 col-form-label">Is this a NMSO Account?</label>
        <div class="col-sm-3 form-group" id="customer_nmso_account">
            <input type="text" max="50" class="form-control " name="customer_nmso_account" value="{{old('customer_nmso_account')}}" placeholder="NMSO Account" style="text-transform:capitalize" readonly>
        </div>
        <label for="customer_security_clearance_lookup_id" class="col-sm-3 col-form-label customer_security_clearance">What is the candidate security level?</label>
        <div class="col-sm-3 form-group customer_security_clearance">
            <input type="text" max="50" class="form-control " name="customer_security_clearance_lookup_id" value="{{old('customer_security_clearance_lookup_id')}}" placeholder="Security Level" readonly>
        </div>
    </div>
    <div class="form-group row" id="site_note">
         <label for="site_note" class="col-sm-3 col-form-label"><a href="#" id="sitenote">Site Notes</a></label>
        <div class="col-sm-3 form-group" id="site_note">
            <textarea max="500" class="form-control" name="site_note" value="{{old('site_note')}}" placeholder="Site Notes" readonly></textarea>
        </div>
    </div>

    <div id="requirements">
        <div class="table_title">
            <h4> Requirements </h4>
        </div>
        <div class="row">
            <label for="assignment_type" class="col-sm-3 col-form-label assignment_type_class">Assignment Type
                <span class="mandatory">*</span>
            </label>
            <div class="col-sm-3 form-group assignment_type_class" id="type">
                <span id="assignment_type_label"></span>
                <select class="form-control" name="type" id="assignment_type" required >
                    <option value="">Select Assignment Type </option>
                    @foreach($schedule_assignment_type as $id=>$assignment_type)
                    <option value="{{$id}}">{{$assignment_type}}</option>
                    @endforeach
                </select>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('type', ':message') !!}</div>
            </div>

            <label for="fill_type" class="col-sm-3 col-form-label fill_type_class" style="display:none;">Fill Type
                <span class="mandatory">*</span>
            </label>
            <div class="col-sm-3 form-group fill_type_class" id="fill_type" style="display:none;">
                <span id="fill_type_label"></span>
                <select class="form-control" name="fill_type" id="fill_type">
                    <option value="">Select Fill Type </option>
                    @foreach($schedule_assignment_type as $id=>$assignment_type)
                    <option value="{{$id}}">{{$assignment_type}}</option>
                    @endforeach
                </select>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('fill_type', ':message') !!}</div>
            </div>

            <label for="start_date" class="col-sm-3 col-form-label">Start Date
                <span class="mandatory">*</span>
            </label>
            <div class="col-sm-3 form-group" id="start_date">
                <span id="start_date_label" ></span>
                <input type="text" class="form-control datepicker start_date" id="start_date" name="start_date" value="{{old('start_date')}}" max="2900-12-31"
                    placeholder="Start Date" required>
                    <span id="start_datelabel"></span>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('start_date', ':message') !!}</div>
            </div>
        </div>
        <div class="form-group row">
            <label for="site_rate" class="col-sm-3 col-form-label">Site Rate</label>
            <div class="col-sm-3 form-group" id="site_rate">
                <input type="text" max="10" class="form-control " name="site_rate" value="{{old('site_rate')}}" placeholder="Site Rate" required>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_rate', ':message') !!}</div>
            </div>
            <label for="end_date" class="col-sm-3 col-form-label">End Date
                <span class="mandatory">*</span>
            </label>
            <div class="col-sm-3 form-group" id="end_date">
                <span id="end_date_label" ></span>
                <input type="text" class="form-control datepicker end_date" id="end_date" name="end_date" value="{{old('end_date')}}" max="2900-12-31"
                    placeholder="End Date" required>
                    <span id="end_datelabel"></span>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('end_date', ':message') !!}</div>
            </div>
        </div>
        <div class="form-group row">
            
            <label for="expiry_date" class="col-sm-3 col-form-label">Expiry Date
                <i style="padding-left:10px;font-size: 13px " data-toggle="tooltip" data-placement="top" title="This shift will no longer available on the app after the expiry date and time" class="fas fa-question"></i>
               
            </label>
            <div class="col-sm-3 form-group" id="expiry_date">
                <span id="expiry_date_label" ></span>
                <input type="text" class="form-control datepicker expiry_date" id="expiry_date" name="expiry_date" 
                value="{{old('expiry_date')}}" max="2900-12-31"
                    placeholder="Expiry Date" >
                    <span id="expiry_datelabel"></span>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>
                    {!! $errors->first('expiry_date', ':message') !!}</div>
            </div>
            <label for="expiry_time" class="col-sm-3 col-form-label">Expiry Time
                <i style="padding-left:10px;font-size: 13px " data-toggle="tooltip" data-placement="top" title="This shift will no longer available on the app after the expiry date and time" class="fas fa-question"></i>
               
            </label>
            <div class="col-sm-3 form-group" id="expiry_time">
                <span id="expiry_time_label" ></span>
                <input type="text" class="form-control expiry_time" id="expiry_time" name="expiry_time" 
                value="{{old('expiry_time')}}" 
                    placeholder="Expiry Time" >
                    <span id="expiry_timelabel"></span>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>
                    {!! $errors->first('expiry_time', ':message') !!}</div>
            </div>
        </div>
        <div class="form-group row" id="schedule-shift">
            <label for="time_scheduled" class="col-sm-3 col-form-label">Time Scheduled</label>
            <div class="col-sm-3 form-group" id="time_scheduled">
                 <input type="text"  class="form-control" name="time_scheduled" value="{{old('time_scheduled')}}" placeholder="Time Scheduled(HH:MM AM/PM)" id="timepicker">
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('time_scheduled', ':message') !!}</div>
            </div>
            <label for="length_of_shift" class="col-sm-3 col-form-label">Length of Shift (Hrs)</label>
            <div class="col-sm-3 form-group" id="length_of_shift">
                <input type="text" max="50" class="form-control " name="length_of_shift" value="{{old('length_of_shift')}}">
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('length_of_shift', ':message') !!}</div>
            </div>
        </div>
        <div class="form-group row" id="stc-security-cearance">
            <label for="require_security_clearance" class="col-sm-3 col-form-label">Does this post require security clearance<span class="mandatory">*</span></label>
            <div class="col-sm-3 form-group" id="require_security_clearance">
                {{ Form::select('require_security_clearance',[null=>'Select','yes'=>'Yes','no'=>'No'], null, array('class' => 'form-control','id'=>'security_clearence_require')) }}
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('require_security_clearance', ':message') !!}</div>
            </div>
            <label for="security_clearance_level" class="col-sm-3 col-form-label security_clearance_level">What Level is Required? <span class="mandatory">*</span>
            </label>
            <div class="col-sm-3 form-group security_clearance_level" id="security_clearance_level">
                {{ Form::select('security_clearance_level',[null=>'Select']+$lookups['securityClearanceLookup'], old('security_clearance_level'),array('class'=> 'form-control')) }}
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('security_clearance_level', ':message') !!}</div>
            </div>
        </div>

        <div class="form-group row" id="total-shift-timing" style="display:none;">
        <label for="notes" class="col-sm-3 col-form-label">Notes</label>
            <div class="col-sm-3 form-group" id="notes">
                <textarea maxlength="500" rows="6" class="form-control" name="notes" placeholder="Notes">{{old('notes')}}</textarea>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('notes', ':message') !!}</div>
            </div>

            <label for="shift_timing" class="col-sm-3 col-form-label shift_timing">Shift Timing<span class="mandatory">*</span></label>
            <div class="col-sm-3 form-group shift_timing" id="shift_timing_id">
                @foreach($lookups['shiftTiming'] as $id=>$shift_timing)
                    <input type="checkbox" value="{{$id}}" name="shift_timing_id[]"
                        data-shift-timing-index="{{$id}}"
                        data-shift-timing="{{str_replace(array( '(', ')', '-' ), '', $shift_timing) }}"
                        data-shift-start-timing="{{$lookups['shiftTimingFrom'][$id]}}"
                        data-shift-end-timing="{{$lookups['shiftTimingTo'][$id]}}">
                        <label class='padding-right-10'>{{ ucwords(str_replace("_", " ", $shift_timing)) }}</label>
                @endforeach
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('shift_timing_id', ':message') !!}</div>
            </div>
        </div>
        {{ Form::Number('no_of_shifts', null , array('class'=>'form-control hidden', 'id'=>'total_no_of_shifts', 'placeholder'=>'Total No of Shifts', 'min'=>'1', 'readonly' => true)) }}


        <div class="form-group row">
            <table class="col-sm-12 table table-bordered" id="shift-timing-table" style="display:none;max-width:90% !important;margin-left:5% !important;">
                <thead>
                    <tr>
                        <td>Shift Timing</td>
                        <td class="text-center shift-timing-table-multi-fill-date">Date</td>
                        <td class="text-center">From</td>
                        <td class="text-center">To</td>
                        <td class="text-center shift-timing-table-multi-fill-positions">No of Positions</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div class="form-group row" id="maximum-hours-notes" style="display:none;">
            <label for="overtime_hours_notes" class="col-sm-3 col-form-label">Overtime Notes</label>
            <div class="col-sm-3 form-group" id="overtime_hours_notes">
                <textarea maxlength="500" rows="6" class="form-control" name="overtime_hours_notes" placeholder="Overtime Notes" readonly>{{old('overtime_hours_notes')}}</textarea>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('overtime_hours_notes', ':message') !!}</div>
            </div>
        </div>
        <!-- For overtime notes shift timing labels display - Start -->
        <div id="overtime_shift_timing_id" class="form-group" style="display:none;">
            <input type="hidden" class="form-control col-sm-1" name="overtime_shift_timing_id" value="1">
            <small class="help-block" id="overtime_shift_timing_id_validation" style="display:none;"></small>
        </div>
        <!-- For overtime notes shift timing labels display - End -->
        <div class="form-group row">
            <div class="col-sm-12 col-form-label text-center" id="requirements-button">
                <button id="resetbutton" type="button" class="btn btn-primary blue" data-dismiss="modal" aria-hidden="true">Cancel</button>
                <button type='submit' class='button btn btn-primary blue' id='schedule-requirement'>Save</button>
                {{--{{ Form::submit('Save', array('class'=>'button btn btn-primary blue', 'id'=>'schedule-requirement'))}} --}}
            </div>
        </div>
        <div class="form-group row schedule-requirement-row" style="display: none">
            <div class="col-sm-12 col-form-label text-center" id="requirements-button-update">
                <button id="resetbutton" type="button" class="btn btn-primary blue" data-dismiss="modal" aria-hidden="true">Cancel</button>
                <button type='button' class='button btn btn-primary blue' id='schedule-requirement-update'>Save</button>
                {{--{{ Form::submit('Save', array('class'=>'button btn btn-primary blue', 'id'=>'schedule-requirement'))}} --}}
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
<div class="table_title">
    <h4> Candidate Schedule</h4>
</div>
<div class="responsive-calendar" id="schedule-grid">
    <div class="check-controlline checkboxes" id="parttimefulltime">
        <input type="hidden" name="requirements" value="" id="requirement_id_hidden">
        <div class="checkboxs">
            <input type="checkbox" id="parttimeCheck" class="chk" name="timecheck[]" value="2"
                checked="checked">
            <label for="parttimeCheck">
                Select Part Time Candidates
            </label>
        </div>
        <div class="checkboxs">
            <input type="checkbox" id="farttimeCheck" class="chk" name="timecheck[]" value="1" checked="checked">
            <label for="farttimeCheck">
                Select Full Time Candidates
            </label>
        </div>
    </div>
    <div class="calendar-container" id="candidate-schedule-calender">
        <!--header row-->
        <div class="row-line text-right zero-fix calendarheader-row">
            @foreach ($arr_all_days as $header_days)
            <div class="day-item header-day">
                <div class="day-inner">
                    {{$header_days}}
                </div>
            </div>
            @endforeach
        </div>
        <!--header row END-->
        <!--All row-->
        <div class="row-line text-right calender-listline zero-fix">
            @foreach ($arr_schedule as $shifts => $arr_days)
            <br>
            <div class="day-item left-calendar-label">
                <div class="day-inner">
                    <span>{{ucwords($arr_all_shifts[$shifts])}}</span>
                </div>
            </div>
            @foreach ($arr_days as $days => $avail )
            <div class="day-item">
                <div class="day-inner">
                    <span class="schedule-data-selector" data-days="{{ $days }}" data-shifts="{{ $shifts }}" data-level="{{$security_clearence_id}}" data-shift-id="0">{{count($avail)}}
                    </span>

                </div>
            </div>
            @endforeach @endforeach
        </div>
    </div>
</div>



<!--All row END-->
<!--Day row-->
<!--Candidate Schedule END-->

<div class="table-responsive padding-bottom-10" id="types-table">
    <table class="table table-bordered" id="dynamic-table">
        <thead>
            <tr>
               {{--  <th width="15%" class="sorting">#
                </th> --}}
                     <th width="5%" >Sl.No</th>

                <th width="5%" class="sorting">Start Date
                </th>

                <th width="5%" class="sorting">Start Time
                </th>
                <th width="5%" class="sorting">End Date
                </th>
                <th width="5%" class="sorting">End Time
                </th>
                <th width="5%" class="sorting">Rate
                </th>
                <th width="2%" class="sorting">Shift Length
                </th>
                <th width="5%" class="sorting">Availability
                </th>
                <th width="10%" class="sorting">Assigned Employees
                </th>
                <th width="5%">Actions
                </th>

            </tr>
        </thead>
    </table>
</div>
@can('candidate-mapping')
<div id="map_view_div" class="row margin-bottom-8 padding-15">
    <div class="col-xs-12 col-sm-12 col-md-12 text-right">
        <form action="{{ route('candidate.schedule.mapping') }}" target="_blank" method="POST" id="map_view_submit">
            {{csrf_field()}} {{ Form::hidden('selected_project_no') }} {{ Form::hidden('employee_id_array') }}
            <input type="submit" data-days="" data-shifts="" value="Map View" class="btn submit schedule-map-view" />
        </form>
    </div>
</div>
@endcan

<div class="checkboxs hide-this-block">
            <input type="checkbox" id="secClearance" class="chk" name="security_clearence" value=""
                >
            <label for="secClearance">
                Select Employees with Security Clearance
            </label>
        </div>
<div class="table-responsive">
    <table class="table table-bordered" id="schedules-table">
        <thead>
            <tr>
                <th width="15%" class="sorting">Employee Name
                </th>
                <th width="15%" class="sorting">Address
                </th>
                <th width="5%" class="sorting">City
                </th>
                <th width="5%" class="sorting">Postal Code
                </th>
                <th width="7%" class="sorting">Security Experience
                </th>
                <th width="15%" class="sorting">Security Clearance
                </th>
                <th width="7%" class="sorting">Arrival In Canada
                </th>
                <th width="5%" class="sorting">Wage
                </th>
                <th width="2%" class="sorting">Previous Attempts
                </th>
                <th width="10%" class="sorting">Cell Phone
                </th>
                <th width="10%" class="sorting">Email Address
                </th>
                <th width="3%">Event Log
                </th>
                <th width="5%">Score
                </th>
                 <th width="3%">Availability Status
                </th>
            </tr>
        </thead>
    </table>
</div>


<!-- New STC project Modal - Start -->
<div id="myModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog stc-modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header stc-modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title stc-modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'stc-customer-form','class'=>'', 'method'=> 'POST')) }} {{csrf_field()}} {{ Form::hidden('id',null)}}
            <div class="modal-body">
                <div class="form-group" id="customer-type">
                    <label for="customer-type" class="col-lg-4 control-label">Customer Type
                        <span class="mandatory">*</span>
                    </label>
                    <input type='radio' name='stc' checked value='1'> Short Term Contract
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="project_number">
                    <label for="project_number" class="col-lg-12 control-label">Project Number
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::text('project_number', null, array('class'=>'form-control project-number', 'placeholder'=>'Project Number')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="client_name">
                    <label for="client_name" class="col-lg-12 control-label">Client Name
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::text('client_name', null, array('class'=>'form-control', 'placeholder'=>'Client Name')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="contact_person_name">
                    <label for="contact_person_name" class="col-lg-12 control-label">Contact Person Name</label>
                    {{ Form::text('contact_person_name', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Name')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="contact_person_email_id">
                    <label for="contact_person_email_id" class="col-lg-12 control-label">Contact Person Email Id</label>
                    {{ Form::email('contact_person_email_id', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Email Id'))}}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="contact_person_phone">
                    <label for="contact_person_phone" class="col-lg-12 control-label">Contact Person Phone</label>
                    {{ Form::text('contact_person_phone', null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Phone')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="contact_person_phone_ext">
                    <label for="contact_person_phone_ext" class="col-lg-12 control-label">Ext.</label>
                    {{ Form::text('contact_person_phone_ext', null, array('class'=>'form-control', 'placeholder'=>'Ext.','maxlength'=>3)) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_cell_phone">
                    <label for="contact_person_cell_phone" class="col-lg-12 control-label">Contact Person Cell Phone</label>
                    {{ Form::text('contact_person_cell_phone', null, array('class'=>'form-control phone', 'placeholder'=>'Contact Person Cell')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="contact_person_position">
                    <label for="contact_person_position" class="col-lg-12 control-label">Contact Person Position</label>
                    {{ Form::text('contact_person_position', null, array('class'=>'form-control', 'placeholder'=>'Contact Person Position'))}}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="address">
                    <label for="address" class="col-lg-12 control-label">Address
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::text('address', null, array('class'=>'form-control', 'placeholder'=>'Address')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="city">
                    <label for="city" class="col-lg-12 control-label">City
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::text('city', null, array('class'=>'form-control', 'placeholder'=>'City')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="province">
                    <label for="Province" class="col-lg-12 control-label">Province
                        <span class="mandatory">*</span>
                    </label>

                    {{ Form::text('province', null, array('class'=>'form-control', 'placeholder'=>'Province')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="postal_code">
                    <label for="postal_code" class="col-lg-12 control-label">Postal Code
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::text('postal_code', null, array('class'=>' postal-code form-control','min'=>6, 'max'=>6, 'placeholder'=>'Postal Code')) }}
                    <small class="help-block"></small>
                </div>
                <div class="form-group" id="billing_address">
                    <label for="billing_address" class="col-lg-4 control-label">@lang('Billing Address <span class="mandatory">*</span>')</label>
                    <label for="same_address_check" class="col-lg-4 control-label">@lang('Same as site address')</label>
                    {{ Form::checkbox('same_address_check',null,null, array('id'=>'check_same_address')) }}
                    {{ Form::text('billing_address', null, array('class'=>'form-control', 'placeholder'=>'Billing Address')) }}

                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="industry_sector_lookup_id">
                    <label for="industry_sector_lookup_id" class="col-lg-12 control-label">Industry Sector
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::select('industry_sector_lookup_id',[null=>'Select']+$lookups['industrySectorLookup'], old('industry_sector_lookup_id'),array('class'=> 'form-control')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="region_lookup_id">
                    <label for="region_lookup_id" class="col-lg-12 control-label">Region
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::select('region_lookup_id',[null=>'Select']+$lookups['regionLookup'], old('region_lookup_id'),array('class' => 'form-control'))}}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="description">
                    <label for="description" class="col-lg-12 control-label">Description</label>
                    {{ Form::text('description', null, array('class'=>'form-control', 'placeholder'=>'Description')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="proj_open">
                    <label for="proj_open" class="col-lg-12 control-label">Project Open Date</label>
                    {{ Form::text('proj_open', null, array('class'=>'form-control datepicker stc_date', 'placeholder'=>'Project Open Date (Y-m-d)'))}}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="arpurchase_order_no">
                    <label for="arpurchase_order_no" class="col-lg-12 control-label">AR Purchase Order Number</label>
                    {{ Form::text('arpurchase_order_no', null, array('class'=>'form-control', 'placeholder'=>'AR Purchase Order Number')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="arcust_type">
                    <label for="arcust_type" class="col-lg-12 control-label">AR Customer Type</label>
                    {{ Form::text('arcust_type', null, array('class'=>'form-control', 'placeholder'=>'AR Customer Type')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="requester_name">
                    <label for="requester_name" class="col-lg-12 control-label">Requestor Name
                        <span class="mandatory">*</span>
                    </label>
                    {{Form::select('requester_name',$lookups['requesterLookup'], null,['id'=>'requester_id',
                     'class' => 'form-control', 'placeholder' => 'Please Select','style'=>'width: 100%'])}}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="requester_position">
                    <label for="requester_position" class="col-lg-12 control-label">Requestor Position</label>
                    {{ Form::text('requester_position', null, array('class'=>'form-control',
                    'placeholder'=>'Requestor Position','readonly'=>true,'disabled'=>true)) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="requester_empno">
                    <label for="requester_empno" class="col-lg-12 control-label">Requestor Employee Number</label>
                    {{ Form::text('requester_empno', null, array('class'=>'form-control', 'placeholder'=>'Requestor Employee Number')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="job_description">
                    <label for="job_description" class="col-lg-12 control-label">Site Notes</label>
                    {{ Form::textarea('job_description', null , array('class'=>'form-control', 'placeholder'=>'Site Notes','required'=>true))}}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="nmso_account">
                    <label for="nmso_account" class="col-lg-12 control-label">Is this a NMSO Account?</label>
                    {{ Form::select('nmso_account',['no'=>'No','yes'=>'Yes',], null, array('class' => 'form-control', 'required')) }}
                    <small class="help-block"></small>
                </div>

                <div class="form-group" id="security_clearance_lookup_id" style="display:none">
                    <label for="security_clearance_lookup_id" class="col-lg-12 control-label">What is the security clearance required for this post?
                        <span class="mandatory">*</span>
                    </label>
                    {{ Form::select('security_clearance_lookup_id',[null=>'Select']+$lookups['securityClearanceLookup'], null, array('class'=> 'form-control')) }}

                    <small class="help-block"></small>
                </div>
            </div>
            <div class="modal-footer stc-modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::reset('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<div class="modal fade" id="scheduleOverViewModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Schedule Overview</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer stc-modal-footer"></div>
        </div>
    </div>
</div>

<!-- New STC project Modal - End -->
@stop
@section('scripts')
@include('hranalytics::schedule.schedule-script')
@stop
