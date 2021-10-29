<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <!-- Our Custom CSS -->
    <link href="{{ asset('css/style.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}?t=2" type="image/x-icon">
    <link rel="icon" href="{{asset('images/favicon.ico')}}?t=2" type="image/x-icon">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ"
        crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY"
        crossorigin="anonymous"></script>
    <!--Table-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- <script src="{{ asset('js/dashboard-filter.js') }}" > </script> -->
    @include('layouts.customer-filter-script')
    @include('layouts.partials.sidebar_dynamic_script')
</head>

<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container-fluid" style="padding-left: 6px;">
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
                                    <p class="user-time" style="color: #44617f">It's {{ \Carbon::now()->format('l F d, Y') }}
                                        <!--Monday September 17, 2018-->
                                    </p>
                                    <p id="myclock" class="text-right" style="color:#44617f">{{ \Carbon::now()->format('H : i
                                        A')}}</p>
                                </div>
                                </div>
                                @else
                                <!-- <div class="info">
                            <p class="user-name" style="color:#f36424;text-align:right;margin-top: 9px;">Hello {{
                                ucfirst(auth()->user()->full_name) }}</p>
                            <p class="user-time" style="color: #44617f">It's {{ \Carbon::now()->format('l F d, Y') }}

                            </p>
                            <p id="myclock" class="text-right" style="color:#44617f">{{ \Carbon::now()->format('H : i
                                A')}}</p>
                        </div> -->


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
                                    <li onclick="window.location=$(this).find('a').prop('href');"><a  href="{{ route('profile.edit') }}">
                                            Profile </a></li>
                                    <li onclick="if(confirm('Are you sure to logout?')){ event.preventDefault(); document.getElementById('logout-form').submit(); }" ><a >Logout</a>
                                        <form id="logout-form" action="{{ url(config('adminlte.logout_url ', 'logout ')) }}"
                                            method="POST" style="display: none;">
                                            @if(config('adminlte.logout_method '))
                                            {{ method_field(config('adminlte.logout_method')) }}
                                            @endif
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                        </div>

                    </ul>
                </div>
            </div>
        </nav>
        @include('layouts.cgl360_navigation_menu')
        <!-- Page Content  -->
        <div id="content">
            @yield('content')
            <footer>
                <p> &copy; CGL360 {{ \Carbon::now()->format('Y') }} All rights reserved</p>
            </footer>
        </div>
    </div>
</body>
<style>
    .dataTables_filter {
        width: 100% !important;
    }

    .dataTables_filter label,
    .dataTables_filter input {
        width: 100% !important;
    }
    .fa-user{
        color: #f36424 !important;

    }
</style>
@yield('scripts')

</html>
