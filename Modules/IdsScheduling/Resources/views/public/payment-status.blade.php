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
        .card-title-failed
        {
            font-size: 20px;
        }
        .head
        {
            font-size: 15px;
        }
        .card-title
        {
            color: #575757;
            font-size: 23px;
            text-align: center;
            font-weight: 600;
            text-transform: none;
            position: relative;
            padding: 0;
            line-height: 40px;
            display: block;
        }
        .icon-success{
            padding: 1rem !important;
            color:green
        }

        .swal-booking-notes li{
        text-align: left;
        margin-bottom: 7px;
        font-size: 16px;
    }
    .icon-warning{
            padding: 2rem !important;
            color:#ffc107
        }
       
    </style>
@endsection

@section('content')
<section class="container" style="width: 40%;">
<div class="card text-center">
        <div class="card-body">
        @if($status == "succeeded")
            <i class="fas fa-check-circle fa-5x success icon-success"></i><br>
            <p class="card-title">Successful</p>
            <span class="head"> Thank you for booking with us. Your appointment has been confirmed.</span><br>
            <span class="head"> Please check your email for more details.</span>
                                    <br/><br/>
                                    <h5 style='text-align: left;margin-left: 23px;font-size: 16px;'> Please Note </h5>
                                    <ul class='swal-booking-notes'>
                                        <li>
                                            Clients must present two pieces of government issued ID for processing.
                                            At least one ID must contain a photo.
                                        </li>
                                        <li>All ID must be valid</li>
                                        <li>
                                            All ID must be original. If it is not original, it can be a certified copy,
                                            valid and must be translated in English with one ID containing a photo.
                                        </li>
                                        <li>
                                            We do not accept SIN card or red & white health cards.
                                            We accept green health cards as a second piece of ID only not as a primary.
                                        </li>
                                        <li>
                                            Upon arrival, you will be screened for fever. If your body temperature is 37.6°C (99.7°F) or higher,
                                            you will not be granted service and will be turned away from the site.
                                        </li>
                                        <li>
                                            Over the phone service will not be provided.
                                        </li>
                                        <li>
                                            Starting Jan 01, 2021, a surcharge of $2.50 will be applied to all services
                                            to cover additional costs incurred during the pandemic related to automated
                                            scheduling and PPE.
                                        </li>
                                        <li>
                                            Please note a $10 surcharge will be added to your invoice for no-shows or
                                            any cancellation with less than 2 hours notice.
                                        </li>
                                    </ul>
        @elseif($status =='processing')
            <i class="fas fa-exclamation-circle fa-5x  icon-warning"></i><br>
            <p class="card-title">Please wait while your payment is being processed</p>
                                   
        @else
            <i class="far fa-times-circle fa-5x" style="padding: 17px !important;color: red !important;"></i><br>
            <p class="card-title-failed">Your payment has been failed. Please try again</p>
        @endif
        </div>
</div>      
</section>


          
  



@stop
@section('scripts')

<script>
// $(document).ready(function() {
//     setTimeout(function () {
//         window.location.href = "{{ route('osgc.home') }}";
//     }, 3000); //will call the function after 3 secs.        
// });
</script>
    
@stop



