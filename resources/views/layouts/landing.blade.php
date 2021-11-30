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
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
    <!--Table-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/js/gijgo.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('js/dashboard-filter.js') }}" > </script> -->
    <style>
        .word-wrap {
            white-space: break-spaces;
        }
        .text-wrap{
            white-space:normal !important;
        }

        .tbl-line-height-1 tr {
            line-height: 1 !important;
        }
        .mt-4 {
            margin-top: 1rem!important;
        }

        .search-component {
            margin-top: 0.8rem!important;
        }

        .dboard-search input[type="search"] {
            height: auto !important;
        }

        .clear-btn {
            padding: 0px !important;
        }


        .hidden_date_span {
            display: none;
        }

        .select2-container--default .select2-selection--single {
            width: 100% !important;
            float: right !important;
        }

        .left_padding_40 {
            padding-left: 40px !important;
        }

        .span-site-schedule>div>.select2>.selection {
            float: right !important;
        }

        .live_status_1 {
            background-color: #21a71d !important;
        }

        .live_status_2 {
            background-color: #f8b30e !important;
        }

        .live_status_3 {
            background-color: #d21a1a !important;
        }

        .landing-page-nav-link {
            background-color: #f36424 !important;
            color: white !important;
            border: 1px solid transparent;
            border-top-left-radius: 0.50em !important;
            border-top-right-radius: 0.50em !important;
            border-color: white !important;
        }

        .landing-page-nav-link.active {
            background-color: #13486b !important;
            color: white !important;
            border: 1px solid transparent;
            border-top-left-radius: 0.50em !important;
            border-top-right-radius: 0.50em !important;
            border-color: white !important;
        }

        .custom-dashboard-th {
            color: #212529 !important;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: -2.7em !important;
            width: 50% !important;
            float: right !important;
            white-space: nowrap !important;
        }

        div.dataTables_wrapper div.dataTables_info {
            padding-top: 3.5em !important;
            white-space: nowrap !important;
            width: 50% !important;
            padding-left: 12px !important;
        }

        #content {
            overflow-x: hidden !important;
        }

        #span-site-schedule .select2-selection {
            width: 100% !important;
        }

        #span-site-schedule {
            text-align: right !important;
            width: 100%;
            overflow: hidden;
            height: 40px;
        }

        #span-site-schedule .select2-selection__arrow {
            display: none !important;
        }

        .dasboard-card-body>.dataTables_wrapper {
            min-width: 400px;
        }

        tbody tr {
            font-size: 14px !important;
        }

        .adj-nv {
            flex-grow: 0;
        }

        .dataTables_filter {
            width: 100% !important;
        }

        .dataTables_filter label,
        .dataTables_filter input {
            width: 100% !important;
        }

        .fa-user {
            color: #f36424 !important;

        }

        .select2-dropdown {
            z-index: 18000;
        }

        .gj-textbox-md {
    padding: .375rem .75rem !important;
    font-size: 1rem !important;
    line-height: 1.5 !important;
    color: #495057 !important;
    background-color: #fff !important;
    background-image: none !important;
    background-clip: padding-box !important;
    border: 1px solid #ced4da !important;
    border-radius: .25rem !important;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}

.gj-datepicker {
    width: 100%;
}

.gj-icon {
    text-align: center;
    height: 100%;
    padding: 8px 5px 0 0;
    color: #f48452;
}

    </style>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @include('layouts.customer-filter-script')
    @include('layouts.partials.sidebar_dynamic_script')
</head>


<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container-fluid" style="padding-left: 6px;">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <a href="{{ url('/') }}"><img src="{{asset('images/logo.png') }}"></a>
                    </div>
                    <div class="col-md-8">
                        <p class="logo-head m-0">Integrated Security Management System</p>
                    </div>
                </div>

                @if(isset($customers) && !empty($customers))
                    <div id="dashboard_filter_section" class="col-md-4">
                        <div id="customer-filter-container" class="dboard-search d-flex search-topbar justify-content-end search-handler">
                            <select id="dashboard-filter-customer" class="largerCheckbox" name="customer_ids[]" multiple>
                                @foreach($customers as $customer)
                                <option value='{{ $customer->id }}'>{{$customer->project_number.' - '.$customer->client_name}}</option>
                                @endforeach
                            </select>

                            <button class="search-btn">
                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="620.692px" height="620.692px" viewBox="0 0 620.692 620.692" style="enable-background:new 0 0 620.692 620.692;" xml:space="preserve" class="search-ico">
                                    <g>
                                        <g id="Search_1_">
                                            <g>
                                                <path d="M605.232,555.851L479.167,429.786c35.489-45.192,56.852-102.025,56.852-163.947C536.019,119.02,416.999,0,270.18,0
                                            C123.36,0,4.34,119.02,4.34,265.839c0,146.819,119.02,265.839,265.839,265.839c57.478,0,110.532-18.419,154.016-49.428
                                            l127.317,127.318c14.83,14.83,38.87,14.83,53.7,0C620.062,594.739,620.062,570.681,605.232,555.851z M417.778,399.557
                                            c-5.07,1.842-9.894,4.538-13.957,8.62c-3.74,3.741-6.513,8.07-8.374,12.685c-34.236,27.704-77.796,44.357-125.267,44.357
                                            c-110.115,0-199.379-89.265-199.379-199.379S160.065,66.46,270.18,66.46s199.379,89.265,199.379,199.379
                                            C469.559,317.355,449.849,364.162,417.778,399.557z" />
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </button>

                            <button class="clear-btn">
                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="357px" height="357px" viewBox="0 0 357 357" style="enable-background:new 0 0 357 357;" xml:space="preserve" class="clear-ico">
                                    <g>
                                        <g id="close">
                                            <polygon points="357,35.7 321.3,0 178.5,142.8 35.7,0 0,35.7 142.8,178.5 0,321.3 35.7,357 178.5,214.2 321.3,357 357,321.3
                                            214.2,178.5" />
                                        </g>
                                    </g>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <button class="btn btn-dark toggle-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-align-justify"></i>
                </button>
                <div class="collapse navbar-collapse adj-nv" id="navbarSupportedContent">
                    <ul class="nav navbar-nav ml-auto mt-1">
                        <div class="dropdown">
                            <span class="dropdown-toggle user-profile" data-toggle="dropdown">
                                @if(Auth::user()->employee_profile->image)
                                <div class="d-flex flex-row-reverse align-items-center">
                                    <img src="{{asset('images/uploads/') }}/{{ Auth::user()->employee_profile->image }}">
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
                                <li onclick="window.location=$(this).find('a').prop('href');"><a href="{{ route('profile.edit') }}">
                                        Profile </a></li>
                                <li onclick="if(confirm('Are you sure to logout?')){ event.preventDefault(); document.getElementById('logout-form').submit(); }"><a>Logout</a>
                                    <form id="logout-form" action="{{ url(config('adminlte.logout_url ', 'logout ')) }}" method="POST" style="display: none;">
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
    </header>

    <div class="wrapper">
        @include('layouts.cgl360_navigation_menu')
        <!-- Page Content  -->
        <div id="content">
            @yield('content')
            <footer>
                <p> &copy; CGL360 {{ \Carbon\Carbon::now()->format('Y') }} All rights reserved</p>
            </footer>
        </div>
    </div>
</body>
<script src="{{ asset('js/helper.js') }}"></script>
@yield('scripts')

</html>
