@extends('layouts.app')
@section('css')
<style>
    label{
        /* display: none */
    }
    .error{
        color: red;
    }
</style>
<link rel='stylesheet' type='text/css' href='{{ asset('css/timepicki.css') }}' />
@endsection
@section('content')
<div class="container-fluid" style="padding: 5px !important">
<form id="saveservice" action='{{route("cbs.savefacilitysignout")}}' method="post">
    @csrf
    <input type="hidden" name="facilityid"  id="facilityid" value="{{$facilityid}}" />
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-10 table_title"><h4>Facility Service</h4></div>
    <div class="col-md-2" style="text-align: left">

        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Facility
        </div>
        <div class="col-md-3" style="text-align: left" id="facility">
            <input type="text" name="facility" class="form-control"  />
            <label class="error" for="facility"></label>
        </div>


    </div>

    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
                Description
        </div>
        <div class="col-md-3" style="text-align: left" id="description">
            <textarea name="description" class="form-control" rows="5"  ></textarea><label class="error" for="description"></label>
        </div>
        <div class="col-md-3">

        </div>
        <div class="col-md-3" style="text-align: left">
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">

        <div class="col-md-2">
            Booking Interval
        </div>
        <div class="col-md-3" style="text-align: left" id="booking_interval">
            <input type="text" class="form-control number" name="booking_interval" value="" /><label class="error" for="booking_interval"></label>
        </div>
    </div>

    <div class="row" style="margin-top: 20px;margin-bottom: 20px">

        <div class="col-md-2">
            Maximum Occupancy Per Slot(Count)
        </div>
        <div class="col-md-3" style="text-align: left">
            <input type="text" class="form-control number" value="" name="tolerance_perslot" />    <label class="error" for="tolerance_perslot"></label>
        </div>
        <div class="col-md-2" style="display: none">
            Weekend Booking
        </div>
        <div class="col-md-3" style="text-align: left;display: none" id="weekend_booking">
            <select class="form-control "  name="weekend_booking" >
                <option value="">Select Any</option>
                <option value="1">Yes</option>
                <option value="0">No</option>

            </select>
            <label class="error" for="weekend_booking"></label>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">
        <div class="col-md-2">
            Start Time
        </div>
        <div class="col-md-3" style="text-align: left" id="start_time">
            <input type="text" class="form-control timepicker" value="" name="start_time" />    <label class="error" for="start_time"></label>
        </div>
        <div class="col-md-2">
            End Time
        </div>
        <div class="col-md-3" style="text-align: left" id="end_time">
            <input type="text" class="form-control timepicker" name="end_time" value="" />        <label class="error" for="end_time"></label>
        </div>
    </div>

    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-9">

        </div>

        <div class="col-md-3" style="text-align: right">
                     <button class="btn btn-primary save" attr-mode="save">Save</button>
                     <button class="btn btn-primary save"  attr-mode="saveadd">Save & Add Other</button>
                     <button class="btn btn-primary">Cancel</button>
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
    });

    $(document).on("click",".save",function(e){
        e.preventDefault();
        var attrmode = $(this).attr("attr-mode");
        var data = $('#saveservice').serialize();
        $.ajax({
        type: "post",
        url: "{{route('cbs.savefacilityservice')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        success: function (response) {
            var data = jQuery.parseJSON(response);
            if(data.code==200){
            if(attrmode=="saveadd"){
                swal({
                title: "Service Added Successfully",
                text: data.message,
                type: "success"
                }, function() {
                    location.reload();
                });
            }else{
                swal({
                title: "Success",
                text: data.message,
                type: "success"
                }, function() {
                    window.location = "manageservice/{{$facilityid}}";
                });
            }
            }else{
                swal("Warning",data.message,data.success)
            }
        }
    }).fail(function( data ) {
        $(".error").html("");
        var response = JSON.parse(data.responseText);
        var errorString = '<ul>';
        $.each( response.errors, function( key, value) {
            console.log(key);
            $('label[for="'+key+'"]').html(value);
            errorString += '<li>' + value + '</li>';
        });
        errorString += '</ul>';

    });
    })

</script>
@endsection
