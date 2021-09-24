<style>
.container {  
    display: grid;  
    grid-gap: 2px;  
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    padding-left: 5px;
    font-size: 0.7rem !important;
}
.noshow-box {
    border-style: solid;
    border-color: black;
    background-color: #333f50;
    color: white;
    padding: 10px;
    margin: -5px;
}
</style>
<div >  
    @foreach($customers as $type => $customer)
    @if(($type !=0) || ($type !=''))
    <h5 style="color: #e96332;">  {{$customer_type[$type]}} </h5>
 
 <div class="container">   
@foreach($customer as $each => $details)
<div id="{{$details['id']}}" data-id="{{$details['project_number']}}" data-toggle="popover" title="Details" data-content="" class="noshow-box"> {{$details['project_number']}} </div>
@endforeach
</div>
<br>
@endif
@endforeach
</div>




