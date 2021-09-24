@extends('layouts.cgl360_osgc_scheduling_layout')
@section('css')
    <style>
        .navbar{
            display: none;
        }


        .activation-container .header-container {
            padding-top: 20px;
            /* font-weight: bold; */
            font-size: 23px;
            text-align: center;
        }

        .main-container {
            background-color: #FFFFFF;
            width: auto;
            max-width: 600px;
            min-width: 320px;
           
            margin: 6em auto;
            border-radius: 1.5em;
            box-shadow: 0px 11px 35px 2px rgba(0, 0, 0, 0.14);
        }

       
        .cgl-logo {
            width: 25%;
            text-align: center;
            margin-top: -55px;
            margin-left: 39%;
            margin-bottom: -35px;
        }
        .btn{
   
            border-radius: 5px;
            box-shadow: none;
            
            font-weight: 500;
            cursor: pointer;
            margin-top: 15px;
            border: 0;
            font-size: 17px;
            background: #ea660f;
            margin-bottom: 5px;
            color: #ffffff;  

            }
       
       
    </style>
@endsection
@section('content')

<section class="container main-container">
        <img class="cgl-logo" src="{{asset('images/cgl-512-circle.png')}}" alt="cgl-360-logo"/>
        <div class="activation-container">
            <div class="row">
                <div class="col-md-12 header-container">
                    <p>Your account has been successfully activated.</p>
                    <a href="{{ url('osgc/login') }}" class="btn">Login</a>
                    <hr>
                </div>
            </div>
          </div>
</section>
   
 


@endsection