<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>{{  config('app.name', 'CGL360')}} @yield('title') </title>
    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous">
     <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.4.2/b-colvis-1.4.2/b-flash-1.4.2/b-html5-1.4.2/b-print-1.4.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/datatables.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <link href="https://cdn.jsdelivr.net/npm/jquery-easy-loading/dist/jquery.loading.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet">

   
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-easy-loading/dist/jquery.loading.min.js"></script>

    <!-- DataTables -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.4.2/b-colvis-1.4.2/b-flash-1.4.2/b-html5-1.4.2/b-print-1.4.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/datatables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}?t=2" type="image/x-icon">
    <link rel="icon" href="{{asset('images/favicon.ico')}}?t=2" type="image/x-icon">

    <!-- CDN of js for datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.ckeditor.com/4.8.0/basic/ckeditor.js"></script>
    @if(Auth::user())
           @if(Route::current()->getPrefix() == "learningandtraining" || Route::current()->getPrefix() == "learning")
    <link href="{{ asset('css/training/custom.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet">
           @endif
    @endif
     <link href="https://vjs.zencdn.net/7.5.4/video-js.css" rel="stylesheet">

    <!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    <script src="{{ asset('js/dashboard-filter.js') }}" > </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
@yield('css')
<style>
    #footer{
       margin-top: 2%;
    }
</style>

</head>
<body>
    <div class="wrapper">
        <nav @guest style="border-bottom:none; box-shadow: none; background-color:#fff;" @endguest class="navbar navbar-expand-lg navbar-light @if(Auth::user()) bg-light @endif fixed-top">
            @if(Auth::user())
            <div class="row align-items-center">
                <div class="col-md-3">
                    <a href="{{ url('/') }}"><img src="{{asset('images/logo.png') }}"></a>
                </div>

            <div class="col-md-9">

                <p class="logo-head m-0">Integrated Security Management System</p>

                <!--<p class="logo-para m-0 mt-1">Our relentless pursuit of innovation, efficiency and
                            transperancy for security services</p>-->
            </div>
            </div>
            <button class="btn btn-dark toggle-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-align-justify"></i>
            </button>
            @endif
            @guest
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link blue" href="{{ route('login') }}">
                        <strong>Login </strong>
                    </a>
                </li>
            </ul>
            @else
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="nav navbar-nav ml-auto mt-1">
                    <div class="dropdown">
                        <span class="dropdown-toggle user-profile" data-toggle="dropdown">
                           @if(Auth::user()->employee_profile->image)
                                <div class="d-flex flex-row-reverse align-items-center">
                                <img style="border-radius: 50%;" src="{{asset('images/uploads/') }}/{{ Auth::user()->employee_profile->image }}">
                                <div class="info">
                                    <p class="user-name" style="color:#f36424;text-align:right;margin-top: 9px;">Hello {{
                                        ucfirst(auth()->user()->full_name) }}</p>
                                    <p class="user-time" style="color: #44617f">It's {{ \Carbon\Carbon::now()->format('l F d, Y') }}
                                    <!--Monday September 17, 2018-->
                                    </p>
                                    <p id="myclock" class="text-right" style="color:#44617f">{{ \Carbon\Carbon::now()->format('H : i
                                        A')}}</p>
                                </div>
                                </div>
                            @else
                                <div class="d-flex flex-row-reverse align-items-center">
                            <i class="fa fa-3x fa-user"></i>
                                <div class="info">
                                    <p class="user-name" style="color:#f36424;text-align:right;margin-top: 9px;">Hello {{
                                        ucfirst(auth()->user()->full_name) }}</p>
                                    <p class="user-time" style="color: #44617f">It's {{ \Carbon::now()->format('l F d, Y') }}
                                    <!--Monday September 17, 2018-->
                                    </p>
                                    <p id="myclock" class="text-right" style="color:#44617f">{{ \Carbon::now()->format('H : i
                                        A')}}</p>
                                </div>
                                </div>
                            @endif
                        </span>
                        <ul class="dropdown-menu dropdown-content">
                            <li onclick="window.location=$(this).find('a').prop('href');" ><a href="{{ route('profile.edit') }}"> Profile </a></li>
                            <li onclick="if(confirm('Are you sure to logout?')){ event.preventDefault(); document.getElementById('logout-form').submit(); }"><a>Logout</a>
                                <form id="logout-form" action="{{ url(config('adminlte.logout_url ', 'logout ')) }}" method="POST"
                                    style="display: none;">
                                    @if(config('adminlte.logout_method '))
                                    {{ method_field(config('adminlte.logout_method')) }}
                                    @endif
                                    {{ csrf_field() }}
                                </form></li>
                        </ul>
                    </div>

                </ul>
            </div>
            @endguest
        </nav>
        @if(Auth::user())
           @if(Route::current()->getPrefix() == "learningandtraining")
             @include('layouts.cgl360_navigation_menu_training_admin')
           @elseif(Route::current()->getPrefix() == "learning")
             @include('layouts.cgl360_navigation_menu_training_learner')
            @elseif(Route::current()->getPrefix() == "reports")
             @include('layouts.cgl360_navigation_menu_reports_report')
           @else
             @include('layouts.cgl360_navigation_menu')
           @endif
        @endif
        <!-- Page Content  -->

        <div id="content-div">
            @yield('content')
            <!--<footer>
                <p> &copy; CGL360 {{ \Carbon\Carbon::now()->format('Y') }} All rights reserved</p>
            </footer>-->
        </div>
    </div>
    @include('layouts.customer-filter-script')
    @include('layouts.footer')
</body>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

<script src="{{ asset('js/common.js') }}"></script>
<script src="{{ asset('js/helper.js') }}"></script>
<!-- <script src="{{ asset('js/dashboard-filter.js') }}" > </script> -->
@yield('scripts')
<style>
.fa-user{
        color: #f36424 !important;

    }
</style>
<script>
    var clock = 0;
var interval_msec = 1000;
$(function () {
    // set timer
    var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    if(!isSafari)
    {
        clock = setTimeout("UpdateClock()", interval_msec);
    }
    $('#content .row').each(function(){
        var c =12/Number($(this).find('.card-table').length);
        $(this).find('.card-padding').addClass('col-lg-'+c).addClass('col-lg-'+c);
    });

});
var date = null;
// UpdateClock
function UpdateClock() {
    // clear timer
    clearTimeout(clock);
    if(date==null){
        date = new Date('{{ \Carbon\Carbon::now() }}');
    }else{
        date = new Date(date.getTime() + 1*1000);
    }
    //console.log(date);
    var hours = (date.getHours());
    var minutes = (date.getMinutes());
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = (('0'  + hours).slice(-2)) + ' : ' + (('0'  + minutes).slice(-2)) + ' ' + ampm;
    $("#myclock").html(strTime);
    // set timer
    clock = setTimeout("UpdateClock()", interval_msec);
}



    </script>
</html>
</html>

<script>


    </script>
</html>