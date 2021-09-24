@extends('layouts.app')
@section('content')
<div class="container-fluid" style="padding: 5px !important">
    
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-10"><h2>Condo Management</h2></div>
        <div class="col-md-2" style="text-align: right">           
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Amenity
        </div>
        <div class="col-md-3" style="text-align: right">    
            <input type="text" class="form-control" value="{{$amenities->facility_name}}" />     
        </div>
        <div class="col-md-1">
            Internal user 
        </div>
        <div class="col-md-1" style="text-align: right"> 
             <input type="checkbox" id="internaluser" name="internaluser" style="width: 30px !important;vertical-align:middle !important" />        
        </div>
        <div class="col-md-1">
            External user 
        </div>
        <div class="col-md-1" style="text-align: right"> 
            <input type="checkbox" id="externaluser" name="externaluser" style="width: 30px !important;vertical-align:middle !important" />       
        </div>
        <div class="col-md-2">
            Single service facility 
        </div>
        <div class="col-md-1" style="text-align: right"> 
            <input type="checkbox" id="singleservicefacility" name="singleservicefacility" style="width: 30px !important;vertical-align:middle !important" />       
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
                Description
        </div>
        <div class="col-md-3" style="text-align: right">
            <textarea class="form-control" rows="5"  >{{$amenities->description}}</textarea>            
        </div>
        <div class="col-md-3">

        </div>
        <div class="col-md-3" style="text-align: right">           
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Maximum Booking per day
        </div>
        <div class="col-md-3" style="text-align: right">
            <input type="text" class="form-control" value="{{$amenities->maxbooking_perday}}" />                 
        </div>
        <div class="col-md-2">
            Booking Interval
        </div>
        <div class="col-md-2" style="text-align: right">  
            <input type="text" class="form-control" value="{{$amenities->booking_interval}}" />         
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Weekend start time
        </div>
        <div class="col-md-3" style="text-align: right">
            <input type="text" class="form-control" value="{{date("h:i a",strtotime($amenities->weekend_start_time))}}" />                 
        </div>
        <div class="col-md-2">
            Weekend end time
        </div>
        <div class="col-md-2" style="text-align: right">  
            <input type="text" class="form-control" value="{{date("h:i a",strtotime($amenities->weekend_end_time))}}" />         
        </div>
    </div>
    
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Start Time
        </div>
        <div class="col-md-3" style="text-align: right">
            <input type="text" class="form-control" value="{{date("h:i a",strtotime($amenities->start_time))}}" />                 
        </div>
        <div class="col-md-2">
            End Time
        </div>
        <div class="col-md-2" style="text-align: right">  
            <input type="text" class="form-control" value="{{date("h:i a",strtotime($amenities->end_time))}}" />         
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-2">
            Postal code
        </div>
        <div class="col-md-3" style="text-align: right">
            <input type="text" class="form-control" value="{{$amenities->postal_code}}" />                 
        </div>
        <div class="col-md-2">
            
        </div>
        <div class="col-md-2" style="text-align: right">  
                     
        </div>
    </div>
    <div class="row" style="margin-top: 20px;margin-bottom: 20px">
        <div class="col-md-10">
            
        </div>
        
        <div class="col-md-2" style="text-align: right">  
                     <button class="btn btn-primary">Save</button>
                     <button class="btn btn-primary">Cancel</button>
        </div>
    </div>
    
</div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            
            $("#amenitytable").DataTable();
        });
    </script>
@endsection