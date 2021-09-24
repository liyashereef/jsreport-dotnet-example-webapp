@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>Mobile Security Patrol Trips Details</h4>
</div>
<div id="message"></div>
<table  class="table table-bordered DataTable subtable">
        <tr><b><td><b>Start Time</b></td><td><b>End Time<b></td><td><b>Starting Location</b></td><td><b>Destination</b></td><td><b>Travel time</b></td><td><b>Total kms</b></td><td><b>Map View</b></td></tr>
        <tbody class="child_elements">
            @if(isset($tripDetails['trip_details']))
            @foreach ($tripDetails['trip_details'] as $item)
            <tr"><td>{{$item['start_time']}}</td><td>{{$item['end_time']}}</td><td><div class="d-flex align-items-center"><p class="location_name">{{$item['source']}}</p><div class="location_marker"><span class="marker_font">{{$item['source_count']}}</span></div> </div></td><td><div class="d-flex align-items-center"><p class="location_name">{{$item['destination']}}</p><div class="location_marker"><span class="marker_font">{{$item['destination_count']}}</span></div></div></td><td>{{ $item['travel_time']}}</td><td>{{$item['total_km']}}</td><td><a class="fa fa-map-marker view_map" href="javascript:void(0);" id="{{$item['trip_id']}}"></a></td></tr><tr class="border_bottom hide-this-block" id="trip_{{$item['trip_id']}}"><td colspan=4> <div class="w3-container" style="margin: 10px; padding: 5px;> <div class="clearfix"></div><div class="col-sm-6"><label  style="width:100%;"><span class="col-sm-7 col-7 float-left">Employee Name</span> <span class="col-sm-5 col-5 float-left"> {{$tripDetails['employee_name']}}</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left ">Employee Number</span><span class="col-sm-5 col-5 float-left">{{$tripDetails['employee_no']}}</span>
            <div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Start Time</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['start']}}</span>
            <div class="clearfix"></div><span class="col-sm-7 col-7 float-left">End Time</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['end']}}</span>
             <div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Date</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['created_at']}}</span>
             <div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Name</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['first_name']}}</span>
             <div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Incident Reported</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['incident_reported']}}</span>
             

                <div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Project Number</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['project_number']}}</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Client Name</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['client_name']}}</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Payperiod</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['pay_period']}}</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Date</span> <span class="col-sm-5 col-5 float-left">{{$tripDetails['created_at']}}</span><div class="clearfix"></label></div><div class="col-sm-6"></td><td colspan=4 class="test"><iframe src="../mapview/{{$item['trip_id']}}" style="float:left;display:inline-block;height:200px;width:500px"></iframe></div></div></td></tr>
          
            @endforeach
            @endif

        </tbody>
    </table>

@stop
@section('scripts')
<script>
 $(function () {
   /*Showing map and details on next row*/
             $(document).on('click','.view_map',function(e) {
                if( $("#trip_"+$(this).attr('id')).hasClass('hide-this-block')){
                 $("#trip_"+$(this).attr('id')).removeClass('hide-this-block');
                }
                else
                {
                    $("#trip_"+$(this).attr('id')).addClass('hide-this-block');
                }

         });
}); 




</script>
<style type="text/css">
    #content-div {
        padding-bottom:60px;
    }

     .location_name
    {
        max-width:327px;
        min-width:327px;
        display:inline-block ;
    }
    .location_marker
    {
        width: 3rem;
        height: 3rem;
        display: inline-block;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-image: url({{ asset('images/map_pointer.png') }});
    }
    .marker_font
    {
        color:#fff;
        font-weight:bold
    }
    table td{
        vertical-align: middle !important;
    }
    .location_name{
        margin-bottom: 0;
    }
</style>
@stop
