@extends('layouts.app')
@section('content')

<link href="{{ asset('css/dragtable.css') }}" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script src="{{ asset('js/jquery.dragtable.js') }}"></script>

<script type="text/javascript">
  $(document).ready(function() {

    $('.defaultTable').dragtable();

  });
</script>

<style>
  .profileImage {
    width: 60px;
    height: 60px;
    border-radius: 50%;
  }
  .report-red{
    background-color: #f64f4f;
    color: white;
  }
  .report-green{
    background-color: #66c982;
    color: black;
  }
  .report-yellow{
    background-color: #f2d778;
    color: black;
  }
  .report-normal{
    background-color: white;
    color: black;
  }

  .candidate-image-div img {
    transition: transform .5s, filter 1.5s ease-in-out;
  }

  /* [3] Finally, transforming the image when container gets hovered */
  .candidate-image-div:hover img {
    z-index: 9999999;
    transform: scale(5);
    -ms-transform: scale(5);
    /* IE 9 */
    -moz-transform: scale(5);
    /* Firefox */
    -webkit-transform: scale(5);
    /* Safari and Chrome */
    -o-transform: scale(5);
    /* Opera */
    position: relative;
  }

  .table-bordered th {
    background-color: #f36905;
    color: white;
  }
</style>
<div class="table_title">
  <h4> Candidate Comparison Report
  </h4>
</div>
<div class="demo">
  <div class="demo-content">

  
   <table style="width: 100%;" class="defaultTable sar-table">
    <thead>
        <tr style="background-color: #f34105;color: #fafafa;height: 60px;">
          <th></th>
          @foreach($candidates['name'] as $candidate)
          <th style="padding-left: 1%"> {{$candidate}}</th>
          @endforeach

        </tr>
      </thead>
     <tbody>
          @foreach($candidates as $key => $candidate)
          @if($key != 'name')
            <tr>
              <td style="min-width:250px;background-color: #374353;color: #fafafa;padding:4px 1px 4px 10px;" > @if($key != 'Profile Image') {{$key}} @endif</td>
               @foreach($candidate as $result)
               @if($result != '')
                   @if($key == 'Profile Image')
                   <td style="padding: 1%;"> <div id="candidate-image-div" class="candidate-image-div" style="width:10% !important;"><img name="image" src="{{asset('images/uploads').'/'.$result}}"  class="profileImage"></div></td>
                   @elseif(($key == 'Status in Canada') || ($key == 'Drivers License') || ($key == 'Attend Orientation') || ($key == 'Military Vet') || ($key =='Start As Spare'))
                   <td style="padding:1px 1px 1px 8px" class="{{$class_arr[$result]}}" >{{$result}}</td>
                   @elseif(($key == 'Security License Expiry') || ($key == 'First Aid Expiry') || ($key == 'CPR Expiry')) 
                          @if($result <= 0)
                                <td class="report-red" style="padding:1px 1px 1px 8px" >{{$result}}</td>
                          @elseif($result <= 29)
                                <td class="report-yellow" style="padding:1px 1px 1px 8px" >{{$result}}</td>
                          @else
                                <td class="report-green" style="padding:1px 1px 1px 8px" >{{$result}}</td>
                          @endif        
                   @elseif($key == 'Case Study Score') 
                          @if($result < 3)
                                <td class="report-red" style="padding:1px 1px 1px 8px" >{{$result}}</td>
                          @elseif($result < 4)
                                <td class="report-yellow" style="padding:1px 1px 1px 8px" >{{$result}}</td>
                          @elseif($result < 5)
                                <td class="report-green" style="padding:1px 1px 1px 8px" >{{$result}}</td>
                          @endif        
                   @else 
                   <td style="padding:1px 1px 1px 8px" >{{$result}}</td>
                   @endif 
                @else
                <td style="padding:1px 1px 1px 8px">--</td>
                @endif    
               @endforeach  
            </tr>
            @endif 
          @endforeach
     </tbody>      
   </table>
  </div>
</div>

@stop
@section('scripts')
<script>

</script>
@stop