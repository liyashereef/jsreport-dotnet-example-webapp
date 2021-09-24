@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" />

<style>
    label{
        display: none
    }
    .Menu
{
    display: table-cell;
}
.managetiming{
    display: none
}
.timingbuttons{

}
#footer{
       margin-top: 0% !important;
    }





</style>

@endsection
@section('content')
<div class="container-fluid" style="padding: 5px !important">
<div class="row">
    <div class="col-md-6">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form id="saveservice" action='{{route("cbs.updatefacilityservice")}}' method="post">
                        @csrf
                        <input type="hidden" name="facilityid"  id="facilityid" value="{{$facilityid}}" />

                        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                            <div class="col-md-12 table_title"><h4>Manage Facility Service </h4></div>

                        </div>
                        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                            <div class="col-md-4">
                                Facility
                            </div>
                            <div class="col-md-8" style="text-align: right" id="facility">
                            <input type="text" name="facility" class="form-control" value="{{$data->service}}" />
                                <label for="facility"></label>
                            </div>


                        </div>

                        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                            <div class="col-md-4">
                                    Description
                            </div>
                            <div class="col-md-8" style="text-align: right" id="description">
                                <textarea name="description" class="form-control" rows="5"  >{{$data->description}}</textarea><label for="description"></label>
                            </div>

                        </div>
                        <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">
                            <div class="col-md-4">
                                Maximum Booking Per Day (Hours)
                            </div>
                            <div class="col-md-8" style="text-align: right" id="maxbooking_perday">
                                <input type="text" class="form-control" name="maxbooking_perday" value="{{$facilitydata->maxbooking_perday}}" /><label for="maxbooking_perday"></label>
                            </div>

                        </div>
                        <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                            <div class="col-md-4">
                                Booking Interval (Hours)
                            </div>
                            <div class="col-md-8" style="text-align: right" id="booking_interval">
                                <input type="text" maxlength="5"  class="form-control number" name="booking_interval" value="{{$slot_interval}}" /><label for="booking_interval"></label>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;margin-bottom: 20px">

                            <div class="col-md-4">
                                Maximum Occupancy Per Slot
                            </div>
                            <div class="col-md-8" style="text-align: right">
                                <input type="text" class="form-control" value="{{$facilitydata->tolerance_perslot}}" name="tolerance_perslot" />    <label for="tolerance_perslot"></label>
                            </div>

                        </div>
                        <div class="row" style="margin-top: 20px;margin-bottom: 20px;display:none">


                            <div class="col-md-4">
                                Weekend Booking
                            </div>
                            <div class="col-md-8" style="text-align: right" id="weekend_booking">
                                <select class="form-control " id="weekendbooking"  name="weekend_booking" >
                                    <option value="">Select Any</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>

                                </select>

                                <label for="weekend_booking"></label>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;margin-bottom: 20px">
                            <div class="col-md-6">

                            </div>

                            <div class="col-md-6" style="text-align: right">
                                         <button class="btn btn-primary save" attr-mode="save">Save</button>

                                         <button type="button" class="btn btn-primary cancel">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-md-6">
        <div class="container-fluid" style="margin-top: 25px">
            <div class="row">
                <div class="col-md-2 table_title">
                    <h4>Timings</h4>

                </div>
                <div class="col-md-2 ">
                <button type="button" style="" class="btn btn-primary addnewtiming">Add New</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered" id="timingtable">
                        <thead>
                            <th>Start time</th>
                            <th>End time</th>
                            <th>Start date</th>
                            <th>Expiry date</th>
                            <th>Weekday</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @foreach ($facilitytiming as $timings)
                                <tr>
                                    <td>{{$timings->start_time}}</td>
                                    <td>{{$timings->end_time}}</td>
                                    <td>{{date("Y-m-d",strtotime($timings->start_date))}}</td>
                                    <td>{{$timings->expiry_date}}</td>
                                    <td>
                                        @if ($timings->weekend_timing==0)
                                            Weekday
                                            @else
                                            Weekend
                                        @endif
                                    </td>
                                    <td>
                                        <button attr-id="{{$timings->id}}"
                                            attr-start_time="{{date('h:i:s A', strtotime(date("Y-m-d")." ".$timings->start_time))}}"
                                            attr-end_time="{{date('h:i:s A', strtotime(date("Y-m-d")." ".$timings->end_time))}}"
                                            attr-expiry_time="{{$timings->expiry_time}}"
                                            attr-weekend_timing="{{$timings->weekend_timing}}" class="btn btn-primary edittiming">Edit</button>
                                        <button attr-id="{{$timings->id}}" class="btn btn-primary removetiming">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
</div>

</div>
<form name="addnewtiming"  id="addnewtiming" action="" method="POST">
<div class="container_fluid managetiming" id="managetiming">
    <input type="hidden" id="service_id" name="service_id" value="{{$facilityid}}" />
    <input type="hidden" name="booking_window"  id="booking_window" value="{{$booking_window}}" />
    <div class="row form-group">
        <div class="col-md-4">
            Start Time
        </div>
        <div class="col-md-8">
            <input type="text" id="st_time" name="st_time" class="form-control timepick" value="" />
            <input type="hidden" id="edit_facility_id" name="edit_facility_id" class="form-control" value="" />
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
    <div class="row form-group" id="">
        <div class="col-md-4 ">
            Weekend Timing
        </div>
        <div class="col-md-8">
            <select name="weekend_timing" id="weekend_timing" class="form-control">

                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
        </div>
    </div>
    <div class="row form-group" id="">
        <div class="col-md-4 ">

        </div>
        <div class="col-md-8">
            <button type="button" class="btn btn-primary timingbuttons" id="savetiming">Save</button>

        </div>
    </div>
</div>
</form>
@stop
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
<script>
    $(document).ready(function () {

    });

    $("#managetiming").dialog({
                autoOpen: false,
                width:"500",
                title: "<h4>MANAGE TIMINGS</h4>",
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
        $("#addnewtiming")[0].reset()
        var dialog = $('#managetiming');

        $("#edit_facility_id").val("");
        dialog.dialog('open').after(function(e){

        });
    })



    $(document).on("click",".edittiming",function(e){
        e.preventDefault();
        $("#addnewtiming")[0].reset()
        var dialog = $('#managetiming');
        var st_time = $(this).attr("attr-start_time");
        var en_time = $(this).attr("attr-end_time");
        var expiry = $(this).attr("attr-expiry_time");
        $("#st_time").val(st_time);
        $("#en_time").val(en_time);
        $("#edit_facility_id").val($(this).attr("attr-id"));
        dialog.dialog('open').after(function(e){

        })
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

    $("#savetiming").click(function (e) {
        e.preventDefault();
        var formdata = $("#addnewtiming").serialize();
        var sttime = convertTime12to24($("#st_time").val());
        var entime = convertTime12to24($("#en_time").val());
        var booking_window = $("#booking_window").val();
        var timing_id = $("#edit_facility_id").val();
        var service_id = $("#service_id").val();
        if(sttime>=entime) {
            swal("Warning","Starting timing should be less that start time","warning");
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

    $(".removetiming").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: "{{route('cbs.removeservicetiming')}}",
            data: {model_id:$(this).attr("attr-id"),model_type:"service"},
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
                            location.reload();
                        });

                    }else{
                        swal("Warning",data.message,data.success)
                    }
            }
        });
    });

    $(".cancel").on("click",function(e){
        e.preventDefault();
        history.go(-1);
    })
    $(document).on("click",".save",function(e){
        e.preventDefault();
        var attrmode = $(this).attr("attr-mode");
        var data = $('#saveservice').serialize();
        $.ajax({
        type: "post",
        url: "{{route('cbs.updatefacilityservice')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        success: function (response) {
            var data = jQuery.parseJSON(response);
            if(data.code==200){
            if(attrmode=="saveadd"){
                swal({
                title: "Success",
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
                    location.reload();
                });
            }
            }else{
                swal("Warning",data.message,"warning")
            }
        }
    });
    })

    $(document).ready(function () {

        try {
            var weekendbooking = {!! json_encode($facilitydata->weekend_booking) !!};
            $('select[name="weekend_booking"]').val("1");
        } catch (error) {

        }

        $("#timingtable").dataTable();

        $(".timepick").timepicker({
            timeFormat: 'h:i A',
            step: 15,
        });

    });



</script>
@endsection
