<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>@yield('title', config('app.name', 'CGL360 Facility') ) </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.4.2/b-colvis-1.4.2/b-flash-1.4.2/b-html5-1.4.2/b-print-1.4.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/datatables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/jquery-easy-loading/dist/jquery.loading.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/tabs.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-easy-loading/dist/jquery.loading.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.4.2/b-colvis-1.4.2/b-flash-1.4.2/b-html5-1.4.2/b-print-1.4.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/datatables.min.js"></script>
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('images/favicon.ico')}}" type="image/x-icon">
    <!-- CDN of js for datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ids.css') }}" rel="stylesheet">
  <!-- Select 2 -->
  <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet">
  <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <style>
    /* .navbar-nav .dropdown-menu.dropdown-content {
        left: -69px !important;
        margin-top: -16px;
    } */
    .swal-text {
        color: #868e96!important;
        font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    .sweet-alert button, .sweet-alert button:hover, .swal-button,.swal-button:hover, .sweet-alert button.cancel, .sweet-alert button.cancel:hover {
        background-color: #000000 !important;
    }
    .swal-footer{
        text-align: center;
    }
    .swal-button, .swal-button:hover{
        background-color: #003A63 !important;
        color: #fff;
        font-size: 17px;
        font-weight: 500;
        font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    .dataTables_wrapper{
        padding-bottom: 30px;
    }
    .dropdown{
        width: 200px;
    }
    .navbar-nav .dropdown-menu.dropdown-content {
        top: -170px !important;
        width: 173px;
    }
    .hedder-name {
        color:#f36424;
        text-align:right;
        margin-top: 9px;
        white-space: nowrap;
    }
  </style>
    @yield('css')
</head>
<body>
        <nav class="navbar navbar-expand-md head-bg">
            @if(\Auth::guard('facilityuser')->user())
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <a href="{{ route('facility.booking-page') }}"><img src="{{asset('images/logo.png') }}"></a>
                    </div>
                    <div class="col-md-9">
                        <p class="logo-head m-0">Integrated Security Management System</p>
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
                                <div class="d-flex flex-row-reverse align-items-center">
                                     {{-- <i class="fa fa-3x fa-user"></i> --}}
                                    <div class="info">

                                        <p class="hedder-name user-name" style="">
                                            Hello {{ ucfirst(\Auth::guard('facilityuser')->user()->first_name) }} {{ ucfirst(\Auth::guard('facilityuser')->user()->last_name) }}
                                        </p>
                                        {{-- <p class="user-time" style="color: #44617f">It's {{ \Carbon::now()->format('l F d, Y') }} --}}
                                            <!--Monday September 17, 2018-->
                                            {{-- </p> --}}
                                            {{-- <p id="myclock" class="text-right" style="color:#44617f">{{ \Carbon::now()->format('H : i
                                                A')}}</p> --}}
                                    </div>
                                </div>
                            </span>
                            <ul class="dropdown-menu dropdown-content">
                                <li onclick="window.location=$(this).find('a').prop('href');" ><a href="{{ route('facility.profile-page') }}"> Profile </a></li>
                                <li style="cursor:pointer" onclick="if(confirm('Are you sure to logout?')){ event.preventDefault(); document.getElementById('logout-form').submit(); }"><a>Logout</a>
                                    <form id="logout-form" action="{{ route('facility.logout') }}" method="POST"
                                        style="display: none;">
                                        {{ csrf_field() }}
                                    </form></li>
                            </ul>
                        </div>

                    </ul>
                </div>
                @endif
        </nav>
        <div class="content-div">
            @yield('content')
        </div>
        {{-- @include('layouts.footer') --}}
        <div id="footer">
            <footer class="text-center margin-top-1">
                {{-- <a class="btn submit pull-left" herf="#" title="Help" onclick="showHelpAlert();">Help</a> --}}
                <span>&COPY; Copyright {{ date('Y') }}
                    CGL 360.</span>
            </footer>
        </div>
    </body>
   <script src="{{ asset('js/common.js') }}"></script>
    @yield('scripts')

</html>
