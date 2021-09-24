@extends('layouts.app')
@section('css')
<style>
    label{
        /* display: none */
        color: red;

    }
    .ssf{
        display: none;
    }
</style>
<link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@endsection
@section('content')
<div class="container-fluid" style="margin-top:-7px;padding: 3px !important">


<form action='{{route("cbs.savefacilitysignout")}}' method="post" >
    {{ csrf_field() }}
    @if($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-10 table_title"><h4 style="margin:0px !important">Facility Signout</h4></div>
        <div class="col-md-2" style="text-align: left">
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Facility&nbsp;&nbsp;<i class="fa fa-question-circle" title="Facility Name" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-3 {{ $errors->has('facility') ? 'has-error' : ''}}" style="text-align: left" id="facility">
            <input type="text" name="facility" class="form-control" value="{{Request::old('facility')}}"  />
            <label for="facility">
                {!! $errors->first('facility', '<p class="help-block">:message</p>') !!}
            </label>
        </div>


    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Customer
        </div>
        <div class="col-md-3 {{ $errors->has('customer_id') ? 'has-error' : ''}}"  id="customer_id">
            <select name="customer_id" class="form-control"   >
                <option value="0">Select Any</option>
                @foreach ($customersarray as $key=>$value)
                    <option {{ old('customer_id') == 1 ? 'selected' : ''}} value="{{$key}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                @endforeach
        </select>
            <label for="customer_id">
                {!! $errors->first('customer_id', '<p class="help-block">:message</p>') !!}
            </label>
        </div>


    </div>
    <div class="row " style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
                Description
        </div>
        <div class="col-md-3  {{ $errors->has('description') ? 'has-error' : ''}}" style="text-align: left" id="description">
            <textarea name="description" class="form-control" rows="5"  >{{Request::old('description')}}</textarea>
            <label for="description">{!! $errors->first('description', '<p class="help-block">:message</p>') !!}</label>
        </div>
        <div class="col-md-3">

        </div>
        <div class="col-md-3" style="text-align: left">
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2 ">
            Maximum Booking Per Day (Hours)&nbsp;&nbsp;<i class="fa fa-question-circle" title="Maximum Booking Hours allowed for an individual , Eg :  0.5 = 30 minutes,1= hour etc" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-3  {{ $errors->has('maxbooking_perday') ? 'has-error' : ''}}" style="text-align: left" id="maxbooking_perday">
            <input type="text" maxlength="5" class="form-control number number" name="maxbooking_perday" value="{{Request::old('maxbooking_perday')}}" />
            <label for="maxbooking_perday">{!! $errors->first('maxbooking_perday', '<p class="help-block">:message</p>') !!}</label>
        </div>
        <div class="col-md-2 ssf">
            Booking Interval&nbsp;&nbsp;<i class="fa fa-question-circle" title="Interval allowed per slot" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-2 ssf  {{ $errors->has('booking_interval') ? 'has-error' : ''}}" style="text-align: left" id="booking_interval">
            <input type="text" maxlength="5"  class="form-control number" name="booking_interval" value="{{Request::old('booking_interval')}}" />
            <label for="booking_interval">{!! $errors->first('booking_interval', '<p class="help-block">:message</p>') !!}</label>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">


        <div class="col-md-2">
            Single Service Facility&nbsp;&nbsp;<i class="fa fa-question-circle" title="Facility provides multiple service or single like swimming pool,rooms etc." style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-3  {{ $errors->has('single_service_facility') ? 'has-error' : ''}}" style="text-align: left">
            <select class="form-control" name="single_service_facility">
                <option value="">Select Any</option>
                <option value="yes" @if (old('single_service_facility') == "yes") {{ 'selected' }}@endif>Yes</option>
                <option value="no" @if (old('single_service_facility') != "yes") {{ 'selected' }} @endif>No</option>

            </select>
            <label for="single_service_facility">{!! $errors->first('single_service_facility', '<p class="help-block">:message</p>') !!}</label>
        </div>
        <div class="col-md-2 ssf">
            Maximum Occupancy Per Slot (Count)&nbsp;&nbsp;<i class="fa fa-question-circle" title="How many people allowed during an interval" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-2 ssf  {{ $errors->has('tolerance_perslot') ? 'has-error' : ''}}" style="text-align: left">
            <input type="text" maxlength="3" class="form-control notdecimal" value="{{Request::old('tolerance_perslot')}}" name="tolerance_perslot" />
            <label for="tolerance_perslot">{!! $errors->first('tolerance_perslot', '<p class="help-block">:message</p>') !!}</label>
        </div>
    </div>

    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Start Time&nbsp;&nbsp;<i class="fa fa-question-circle" title="Work start time" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-3  {{ $errors->has('start_time') ? 'has-error' : ''}}" style="text-align: left" id="start_time">
            <input type="text" class="form-control timepicker" value="{{Request::old('start_time')}}" name="start_time" />
            <label for="start_time">{!! $errors->first('start_time', '<p class="help-block">:message</p>') !!}</label>
        </div>
        <div class="col-md-2">
            End Time&nbsp;&nbsp;<i class="fa fa-question-circle" title="Work End Time" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-2  {{ $errors->has('end_time') ? 'has-error' : ''}}" style="text-align: left" id="end_time">
            <input type="text" class="form-control timepicker" name="end_time" value="{{Request::old('end_time')}}" />
            <label for="end_time">{!! $errors->first('end_time', '<p class="help-block">:message</p>') !!}</label>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Booking Window&nbsp;&nbsp;&nbsp;<i class="fa fa-question-circle" title="Maximum open days allowed for booking Eg : 5 means you can book for next 5 days" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-3  {{ $errors->has('booking_window') ? 'has-error' : ''}}">
            <input type="number" min="1" max="100" class="form-control notdecimal" min="1" value="{{Request::old('booking_window')}}" name="booking_window" />
            <label for="booking_window">{!! $errors->first('booking_window', '<p class="help-block">:message</p>') !!}</label>
        </div>
        <div class="col-md-2">
            Weekend Booking&nbsp;&nbsp;&nbsp;<i title="Weekend Booking allowed?" class="fa fa-question-circle" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-2  {{ $errors->has('weekend_booking') ? 'has-error' : ''}}" style="text-align: left" id="weekend_booking">
            <select class="form-control "  name="weekend_booking" >
                <option value="">Select Any</option>
                <option value="1"  {{ old('weekend_booking') == 1 ? 'selected' : ''}}>Yes</option>
                <option value="0" {{ old('weekend_booking') == 0 ? 'selected' : ''}}>No</option>

            </select>
            <label for="weekend_booking">{!! $errors->first('weekend_booking', '<p class="help-block">:message</p>') !!}</label>
        </div>

    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none" id="weekendtiming">
        <div class="col-md-2">
            Weekend Start Time&nbsp;&nbsp;<i class="fa fa-question-circle" title="Work start time" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-3  {{ $errors->has('start_time') ? 'has-error' : ''}}" style="text-align: left" id="start_time">
            <input type="text" class="form-control timepicker" value="{{Request::old('weekend_start_time')}}" name="weekend_start_time" />
            <label for="weekend_start_time">{!! $errors->first('weekend_start_time', '<p class="help-block">:message</p>') !!}</label>
        </div>
        <div class="col-md-2">
            Weekend End Time&nbsp;&nbsp;<i class="fa fa-question-circle" title="Work End Time" style="cursor:pointer" aria-hidden="true"></i>
        </div>
        <div class="col-md-2  {{ $errors->has('end_time') ? 'has-error' : ''}}" style="text-align: left" id="weekend_end_time">
            <input type="text" class="form-control timepicker" name="weekend_end_time" value="{{Request::old('weekend_end_time')}}" />
            <label for="weekend_end_time">{!! $errors->first('weekend_end_time', '<p class="help-block">:message</p>') !!}</label>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-4">

        </div>

        <div class="col-md-2" style="text-align: right;padding:5px !important">
                     <button class="btn btn-primary">Save</button>
                     <button class="btn btn-primary" id="cancelbutton">Cancel</button>
        </div>
    </div>
</form>

</div>

@stop

@section('scripts')
@include('facility::partials.common')
<script src="{{ asset('js/timepicki.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.timepicker').timepicki();
        $( ".fa-question-circle" ).tooltip();
        try {

            var oldcatid = {!! json_encode(old('customer_id')) !!};
            if (typeof oldcatid !== 'undefined') {
                $('select[name="customer_id"]').val(oldcatid);
            }
        } catch (error) {

        }
        if($('select[name="weekend_booking"]').val()==1){
            $("#weekendtiming").show();
        }

        $('select[name="customer_id"]').select2();
        var ssf = $("select[name=single_service_facility]").val();
        if(ssf=="yes"){
            $(".ssf").css("display","block");
        }
    });

    $(document).on("change","select[name=single_service_facility]",function(e){
        if($(this).val()=="yes"){
            $(".ssf").css("display","block");
            $("input[name=booking_interval]").val("")
            $("input[name=tolerance_perslot]").val("")

        }else{
            $(".ssf").css("display","none");
        }
    })

    $(document).on("change","select[name=weekend_booking]",function(e){
        if($(this).val()==1){
            $("#weekendtiming").show();


        }else{
            $("#weekendtiming").hide()
        }
    })

    $(document).on("click","#cancelbutton",function(e){
    e.preventDefault()
    location.href={!! json_encode(route("cbs.facilities")) !!};
})
</script>
@endsection
