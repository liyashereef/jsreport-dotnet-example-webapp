<style>
.container {  
    display: grid;  
    grid-gap: 2px;  
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    padding-left: 5px;
    font-size: 0.7rem !important;
}
.box {
    border-style: solid;
    border-color: black;
    background-color: #333f50;
    color: white;
    padding: 8px;
    margin: -2px;
}
.breadcrumb {
    margin-bottom: 0px !important;
}   
</style>
<div >  
    @foreach($customers as $type => $customer)
    @if(($type !=0) || ($type !=''))
    <h5 style="color: #e96332;margin-left: 4px;">  {{$customer_type[$type]}} </h5>
 
 <div class="container">   
@foreach($customer as $each => $details)
<div id="{{$details['id']}}" data-id="{{$details['project_number']}}" data-reg="{{$details['region_lookup_id']}}" data-toggle="popover" title="Incident Details" data-content="" class="box"> {{$details['project_number']}} </div>
@endforeach
</div>
<br>
@endif
@endforeach
</div>




