<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="format-detection" content="telephone=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- DataTables -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.4.2/b-colvis-1.4.2/b-flash-1.4.2/b-html5-1.4.2/b-print-1.4.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/datatables.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
        <link href="https://cdn.jsdelivr.net/npm/jquery-easy-loading/dist/jquery.loading.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="{{ asset('js/tabs.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert.js') }}"></script>
        <script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-easy-loading/dist/jquery.loading.min.js"></script>
        <!-- DataTables -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/af-2.2.2/b-1.4.2/b-colvis-1.4.2/b-flash-1.4.2/b-html5-1.4.2/b-print-1.4.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/datatables.min.js"></script>
        <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}" type="image/x-icon">
        <link rel="icon" href="{{asset('images/favicon.ico')}}" type="image/x-icon">
        <!-- CDN of js for datepicker -->
        <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/js/gijgo.min.js" type="text/javascript"></script>
        <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.9/css/gijgo.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/custom.css') }}?rev={{config('globals.resource_cache_rev','1')}}" rel="stylesheet">
        <!-- select2 -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        <style>
            .attachmenttext{
                font-weight: bold;
                padding-top:5px;
                color: black
            }
            .attachmenttext a{
                color: black
            }
            .table_title h4{
                margin: 0px !important;
                padding-bottom:5px;
            }
        </style>
    </head>
    <body>

        <nav class="navbar navbar-expand-md head-bg">
            <a class="navbar-brand" href="{{ route('applyjob') }}">
                <img src="{{asset('images/logo.png')}}" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                {{-- <ul class="navbar-nav ml-auto">
                    @if(Auth::user())
                    <li class="nav-item nav-link">Welcome, {{ Auth::user()->getFullNameAttribute()}} |</li>
                    <li class="nav-item dropdown">
                        <a class="nav-link blue" href="{{ route('applyjob.logout') }}" >
                            <i class="fa fa-sign-out" aria-hidden="true"></i> <strong>Logout </strong></a>
                        @else
                        <a class="nav-link blue" href="{{ route('applyjob') }}" >
                            <strong>Login </strong></a>
                        @endif
                    </li>
                </ul> --}}
        </nav>
        <div class="container-fluid">
            @yield('content')
        </div>
        @include('layouts.footer')
    </body>
   <script src="{{ asset('js/common.js') }}"></script>
    @yield('scripts')
</html>
