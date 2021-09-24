@extends('layouts.cgl360_osgc_scheduling_layout')
@section('css')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous">
    <style>
         html, body {
        height: 100%;
        margin: 0;
        font-family: 'Montserrat' !important;
            
        }
        .card-title
        {
            font-size: 20px;
        }
        .icon-success{
            padding: 2rem !important;
            color:green
        }
        .icon-warning{
            padding: 2rem !important;
            color:#ffc107
        }
        .icon-danger{
            padding: 2rem !important;
            color:#dc3545
        }

     
   
       
       
    </style>
@endsection

@section('content')
<section class="container" style="width: 45%;">
<div class="card text-center">
        <div class="card-body">
        @if($status =='succeeded')
            <i class="fas fa-check-circle fa-5x success icon-success"></i><br>
            <p class="card-title">Your payment has been processed successfully</p>
        @elseif($status =='processing')
            <i class="fas fa-exclamation-circle fa-5x  icon-warning"></i><br>
            <p class="card-title">Please wait while your payment is being processed</p>
        @else
            <i class="fas fa-times-circle fa-5x success icon-danger"></i><br>
            <p class="card-title">Your payment has been failed</p>
        @endif
        </div>
</div>      
</section>


          
  



@stop
@section('scripts')

<script>
$(document).ready(function() {
    setTimeout(function () {
        window.location.href = "{{ route('osgc.home') }}";
    }, 3000); //will call the function after 3 secs.        
});
</script>
    
@stop



