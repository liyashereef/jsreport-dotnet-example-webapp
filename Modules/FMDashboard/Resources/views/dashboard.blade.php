@extends('layouts.app')
@section('css')

<link href="{{ asset('faclitymanagementdashboard/dashboard-styles.css') }}" rel="stylesheet">
<script src="{{ asset('faclitymanagementdashboard/zepto.min.js') }}"></script>
<script src="{{ asset('faclitymanagementdashboard/zepto.dragswap.js') }}"></script>

<!-- End facility management dashboard css js  -->
<style>
    #customizeWidgetModal .modal-dialog {
        max-width: 500px !important;
    }
</style>
@endsection
@section('content')
<div class="content-component px-3 py-3">
    <!-- top card area -->
    <div class=" mainlink-component card-view-section mb-2  position-relative">
        <div>
            <div class="mapping mapping-ie mapping-site-dashboard" id="openbtn">
                <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x"
                        aria-hidden="true"></i></a>
            </div>
            <div class="position-absolute icons-top-left d-flex flex-column">
                <!-- <button type="button" 
                    style="z-index:1500"
                    data-toggle="modal" 
                    data-target="#customizeWidgetModal">
                    Customize Widgets
                    </button> -->
                <span style="z-index: 10;cursor: pointer;" data-toggle="modal" data-target="#customizeWidgetModal">
                    <img src="{{asset('images/settings.png') }}" data-toggle="modal" data-target="#customizeWidgetModal"
                        alt="settings">
                </span>

                <!-- <span class=""><img src="{{asset('images/pie.png') }}" alt="settings"></span> -->
                <div class="js-chart-menu js-chart-menu-global global-chart-filter">
                    <img src="{{asset('images/pie.png') }}" alt="" class="position-absolute menu-down dropbtn">
                    <div class="dropdown">
                        <div class="dropdown-content drop-card-details js-chart-menu-dropdown">
                            <li class="item" data-type="0">
                                <img src="{{asset('images/data.png') }}" alt="">
                                <span class="pl-2">Data</span>
                            </li>
                            <li class="item" data-type="bar|column">
                                <img src="{{asset('images/bar.png') }}" alt="">
                                <span class="pl-2">Bar Chart</span>
                            </li>
                            <li class="item" data-type="pie">
                                <img src="{{asset('images/pie.png') }}" alt="">
                                <span class="pl-2">Pie Chart</span>
                            </li>
                            <li class="item" data-type="dount">
                                <img src="{{asset('images/Shape 6.png') }}" alt="">
                                <span class="pl-2">Donut Chart</span>
                            </li>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row ml-4">
            <div class="col-xl-2  col-lg-3 col-md-4 col-sm-6 col-12 pl-4 pr-1 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="d-flex ">
                            <span class="d-flex flex-column b-right-color pr-2">

                                <input id="startdate" width="100%" class="custom-datepicker" />
                                <label for="" class="mb-0 text-white label-name">Start Date</label>

                            </span>
                            <span class="d-flex flex-column pl-2">
                                <input type="text" id="enddate" width="100%" class="custom-datepicker" />
                                <label for="" class="mb-0 text-white label-name">End Date</label>
                            </span>
                        </div>
                        <!-- <img src="{{asset('images/date.png') }}" alt="date" class="position-absolute logos-top-card"> -->
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="d-flex pr-4">
                            <span class="d-flex flex-column  pr-2">
                                <label for="" id="visitor_count" class="mb-0 text-white label-day">0 </label>
                                <label for="" class="mb-0 text-white label-name">Visitors</label>
                            </span>
                        </div>
                        <img src="{{asset('images/visitors.png') }}" alt="date"
                            class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="d-flex pr-4">
                            <span class="d-flex flex-column  pr-2">
                                <label for="" id="incident_count" class="mb-0 text-white label-day">0</label>
                                <label for="" class="mb-0 text-white label-name">Incidents</label>
                            </span>
                        </div>
                        <img src="{{asset('images/Incidents.png') }}" alt="date"
                            class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" id="job_tickets_count" class="mb-0 text-white label-day">0</label>
                            <label for="" class="mb-0 text-white label-name">Job Tickets</label>
                        </span>
                        <img src="{{asset('images/tickets.png') }}" alt="date" class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" id="hours_worked_count" class="mb-0 text-white label-day">0</label>
                            <label for="" class="mb-0 text-white label-name">Hours Worked</label>
                        </span>
                        <img src="{{asset('images/hours.png') }}" alt="date" class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- card area -->


    <div class="row content-cards position-relative customer-show">

        <!-- <button id="openbtn" class="mx-1">☰ <span>Customers</span></button> -->
        <div class="customer-sidebar">

            <div class="h-100">
                <div class="shadow px-3 py-2 bg-white rounded">
                    <div class="d-flex align-items-center">

                        <label for="" class="label-head w-90 position-relative mb-0">Customer
                        </label>

                    </div>
                    <div class="form-group has-search position-relative mb-0">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" id="myInput" class="form-control search-customer"
                            placeholder="Enter Customer Name">
                    </div>
                    <div class="tab d-flex tab-customer list-unstyled">

                        <li class="tablinks pb-4 ml-0 mb-0 pl-3 pr-0 cursor-pointer"
                            onclick="getCustomerList(event, 'actual')" id="defaultOpen">Permanent</li>
                        <li class="tablinks pb-4 ml-0 mb-0 pl-3 pr-0 cursor-pointer"
                            onclick="getCustomerList(event, 'ytd')">Temporary</li>

                        {{--<div class="form-group has-search position-relative mb-0">

                            <span class="fa fa-search form-control-feedback"></span>
                            <input type="text" id="myInput" class="form-control search-customer pr-5">

                        </div>--}}
                    </div>

                    <div id="actual" class="tabcontent">
                        <span onclick="this.parentElement.style.display='none'" class="topright"></span>

                        <div class="table-responsive ">
                            <table class="table customer-table">
                                <thead>

                                </thead>
                                <tbody id="myTable">
                                    <tr>
                                        <td>
                                            @if(isset($permenentCustomers))
                                            <div class="scrollable">
                                                @foreach($permenentCustomers as $i => $customer)
                                                <li class="customer-name{{$i}}">
                                                    <div class="filter_checkbox atl m-r-checkbox">
                                                        <input type="checkbox" name="atl" id="chk-atl{{$customer->id}}"
                                                            class="fcm-filter-checkbox largerCheckbox"
                                                            data-customerid="{{$customer->id}}"
                                                            style="margin-top:12px;float:right;">
                                                    </div>

                                                    <div id="{{$i}}">
                                                        <div class="float-right" style="width:60px;margin-right:5px;"
                                                            aria-hidden="true">
                                                        </div>
                                                    </div>
                                                    <a><span>{{ ucwords($customer->client_name) }}</span></a>
                                                </li>

                                                @endforeach
                                            </div>
                                            @else
                                            <li>No Customers</li>
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="ytd" class="tabcontent">
                        <span onclick="this.parentElement.style.display='none'" class="topright"></span>
                        <div class="table-responsive ">
                            <table class="table customer-table">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-group has-search position-relative mb-0">
                                                <span class="fa fa-search form-control-feedback"></span>
                                                <input type="text" id="myInput" class="form-control search-customer"
                                                    placeholder="Enter Customer Name">
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="myTable">
                                    <tr>
                                        <td>
                                            @if(isset($stcCustomers))
                                            <div class="scrollable">
                                                @foreach($stcCustomers as $i=>$customer)
                                                <li class="customer-name{{$i}}">
                                                    <div class="filter_checkbox atl m-r-checkbox">
                                                        <input type="checkbox" name="atl" id="chk-atl{{$customer->id}}"
                                                            class="largerCheckbox fcm-filter-checkbox"
                                                            data-customerid="{{$customer->id}}"
                                                            style="margin-top:12px;float:right;">
                                                    </div>

                                                    <div id="{{$i}}">
                                                        <div class="float-right" style="width:60px;margin-right:5px;"
                                                            aria-hidden="true">
                                                        </div>
                                                    </div>
                                                    <a><span>{{ ucwords($customer->client_name) }}</span></a>
                                                </li>

                                                @endforeach
                                            </div>
                                            @else
                                            <li>No Customers</li>
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- customer section-->

        <!-- drag and swap section-->
        <div class="col-xl-12 col-md-12 px-0 main-section">

            <div class="row  sortable grid">
                @if($dashboardWidgetRepository->canSeeWidget('view_time_widget'))
                <!-- time section-->
                <section class="col-xl- px-1 pb-3 table-section section-common-height" id="fcm_time">
                    <div class="position-relative">
                        {{-- <button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>--}}
                        <div class="js-chart-menu">
                            <img src="{{asset('images/menu-down.png') }}" alt=""
                                class="position-absolute menu-down dropbtn">
                            <div class="dropdown">
                                <div class="dropdown-content drop-card-details js-chart-menu-dropdown">

                                    <li data-parent="fcm_time" data-type="column">
                                        <img src="{{asset('images/bar.png') }}" alt="">
                                        <span class="pl-2">Bar Chart</span>
                                    </li>

                                    <li data-parent="fcm_time" data-type="pie">
                                        <img src="{{asset('images/pie.png') }}" alt="">
                                        <span class="pl-2">Pie Chart</span>
                                    </li>
                                    <li data-parent="fcm_time" data-type="dount">
                                        <img src="{{asset('images/Shape 6.png') }}" alt="">
                                        <span class="pl-2">Donut Chart</span>
                                    </li>

                                    <li data-parent="fcm_time" data-type="0">
                                        <img src="{{asset('images/data.png') }}" alt="">
                                        <span class="pl-2">Data</span>
                                    </li>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="shadow px-3 py-2 bg-white rounded h-100"><label for="" class="label-head">Time</label>
                        <div class="js-area js-chart-area"></div>
                        <div class="js-area js-data-area table-responsive">
                        </div>
                        <div>

                        </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_hr_widget'))
                <!-- human resource section-->
                <section class="col-xl- px-1 pb-3 table-section section-common-height" id="fcm_hr">
                    <div class="position-relative">
                        {{--<button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>--}}
                        <div class="js-chart-menu">
                            <img src="{{asset('images/menu-down.png') }}" alt=""
                                class="position-absolute menu-down dropbtn">
                            <div class="dropdown">
                                <div class="dropdown-content drop-card-details js-chart-menu-dropdown">
                                    <li data-parent="fcm_hr" data-type="pie">
                                        <img src="{{asset('images/pie.png') }}" alt="">
                                        <span class="pl-2">Pie Chart</span>
                                    </li>

                                    <li data-parent="fcm_hr" data-type="column">
                                        <img src="{{asset('images/bar.png') }}" alt="">
                                        <span class="pl-2">Bar Chart</span>
                                    </li>

                                    <li data-parent="fcm_hr" data-type="dount">
                                        <img src="{{asset('images/Shape 6.png') }}" alt="">
                                        <span class="pl-2">Donut Chart</span>
                                    </li>

                                    <li data-parent="fcm_hr" data-type="0">
                                        <img src="{{asset('images/data.png') }}" alt="">
                                        <span class="pl-2">Data</span>
                                    </li>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="shadow px-3 py-2 bg-white rounded h-100">
                        <label for="" class="label-head">Human Resource</label>
                        <div class="js-area js-chart-area"></div>
                        <div class="js-area js-data-area table-responsive ">
                        </div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_site_dashboard_widget'))
                <!-- site dashboard section-->
                <section class="col-xl- px-1 pb-3 table-section section-common-height" id="item3">
                    {{--<button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>--}}
                    <div class="shadow bg-white rounded h-100"><label for="" class="label-head px-3 py-2">Site
                            Dashboard</label>
                        <div class="site-dashboard h-100">
                            <div class="embed-responsive embed-responsive-4by3">
                                <div id="map" style="min-height:335px;" class="embed-responsive-item">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L
                                    o a d
                                    i n g . . . . . .
                                </div>
                            </div>
                            {{--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2893.2169960558967!2d-79.68202338450662!3d43.51866747912607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4cb322b2347bb%3A0xf540f736eee68eef!2sCommissionaires+-+Oakville!5e0!3m2!1sen!2sin!4v1564661774342!5m2!1sen!2sin"
                                            width="400" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>--}}
                        </div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_incident_summary_widget'))
                <!-- incident summary section-->
                <section class="col-xl- px-1 pb-3 graph-section-incident section-common-height"
                    id="fcm_incident_summary">
                    {{--<button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>--}}
                    <div class="js-chart-menu">
                        <div class="dropdown">
                            <div class="dropdown-content drop-card-details js-chart-menu-dropdown">
                                <li data-parent="fcm_incident_summary" data-type="bar">
                                    <img src="{{asset('images/data.png') }}" alt="">
                                    <span class="pl-2">Data</span>
                                </li>
                                <!-- <li data-parent="fcm_incident_summary" data-type="column">
                                                <img src="{{asset('images/bar.png') }}" alt="">
                                                <span class="pl-2">Bar Chart</span>
                                            </li>
                                            <li data-parent="fcm_incident_summary" data-type="pie">
                                                <img src="{{asset('images/pie.png') }}" alt="">
                                                <span class="pl-2">Pie Chart</span>
                                            </li>
                                            <li data-parent="fcm_incident_summary" data-type="dount">
                                                <img src="{{asset('images/Shape 6.png') }}" alt="">
                                                <span class="pl-2">Donut Chart</span>
                                            </li> -->
                            </div>
                        </div>

                    </div>
                    <div class="shadow px-3 py-2 bg-white rounded h-100">
                        <label for="" class="label-head">Incident Summary</label>
                        <div id="container-incident" class="js-area js-chart-area"></div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_incident_priority_widget'))
                <!-- incident priority section-->
                <section class="col-xl- px-1 pb-3 graph-section section-common-height" id="fcm_incident_priority">
                    <div class="position-relative">
                        <div class="js-chart-menu">
                            <img src="{{asset('images/menu-down.png') }}" alt=""
                                class="position-absolute menu-down dropbtn">
                            <div class="dropdown">
                                <div class="dropdown-content drop-card-details js-chart-menu-dropdown">

                                    <li data-parent="fcm_incident_priority" data-type="bar">
                                        <img src="{{asset('images/bar.png') }}" alt="">
                                        <span class="pl-2">Bar Chart</span>
                                    </li>

                                    <li data-parent="fcm_incident_priority" data-type="0">
                                        <img src="{{asset('images/data.png') }}" alt="">
                                        <span class="pl-2">Data</span>
                                    </li>

                                    <!-- <li data-parent="fcm_incident_priority" data-type="pie">
                                                <img src="{{asset('images/pie.png') }}" alt="">
                                                <span class="pl-2">Pie Chart</span>
                                            </li>
                                            <li data-parent="fcm_incident_priority" data-type="dount">
                                                <img src="{{asset('images/Shape 6.png') }}" alt="">
                                                <span class="pl-2">Donut Chart</span>
                                            </li> -->
                                </div>
                            </div>

                        </div>
                        {{--<button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>--}}
                    </div>
                    <div class="shadow px-3 py-2 bg-white rounded h-100"><label for="" class="label-head">Incident
                            Priority</label>
                        <div class="js-area js-chart-area"></div>
                        <div class="js-area js-data-area table-responsive ">
                        </div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_site_metrics_widget'))
                <!-- site metrics section-->
                <section class="col-xl- px-1 pb-3 graph-section-metrics" id="fcm_site_metrics">
                    {{--<button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>--}}
                    <div class="shadow px-3 py-2 bg-white rounded h-100 ">
                        <div class="customer-section pb-3">
                            <label for="" class="label-head">Site Metrics - Trend Analysis</label>
                            <select id="metrics_customer_id" class="form-control">
                                <option disabled selected>Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->client_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex flex-resp" id="js_site_metrix_inject">
                            <div class="no-customer">
                                <p>No customer selected.</p>
                            </div>
                        </div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_timesheet_reconciliation_widget'))
                <section class="col-xl- px-1 pb-3 graph-section-metrics" id="fcm_timesheet_reconciliation">
                    {{--<button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>--}}
                    <div class="shadow px-3 py-2 bg-white rounded h-100 ">
                        <div class="customer-section pb-3">
                            <label for="" class="label-head">Timesheet Reconciliation</label>
                            <select id="reconciliation_customer_id" class="form-control">
                                <option disabled selected>Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->client_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex flex-resp flex-column" id="js_timesheet_reconcilation_inject">
                            <div class="no-customer">
                                <p>No customer selected.</p>
                            </div>
                        </div>
                </section>
                @endif
                @if($dashboardWidgetRepository->canSeeWidget('view_courses_widget'))
                <!-- courses section-->
                <section style="" class="col-xl- px-1 pb-3 table-section section-common-height" id="fcm_courses">
                    <div class="position-relative">
                        {{--<button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>--}}
                        <div class="js-chart-menu">
                            <img src="{{asset('images/menu-down.png') }}" alt=""
                                class="position-absolute menu-down dropbtn">
                            <div class="dropdown">
                                <div class="dropdown-content drop-card-details js-chart-menu-dropdown">
                                    <li data-parent="fcm_courses" data-type="pie">
                                        <img src="{{asset('images/pie.png') }}" alt="">
                                        <span class="pl-2">Pie Chart</span>
                                    </li>

                                    <li data-parent="fcm_courses" data-type="column">
                                        <img src="{{asset('images/bar.png') }}" alt="">
                                        <span class="pl-2">Bar Chart</span>
                                    </li>

                                    <li data-parent="fcm_courses" data-type="dount">
                                        <img src="{{asset('images/Shape 6.png') }}" alt="">
                                        <span class="pl-2">Donut Chart</span>
                                    </li>

                                    <li data-parent="fcm_courses" data-type="0">
                                        <img src="{{asset('images/data.png') }}" alt="">
                                        <span class="pl-2">Data</span>
                                    </li>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="shadow px-3 py-2 bg-white rounded h-100">
                        <div class="d-flex">
                            <label for="" class="label-head">Courses</label>

                            <select id="course_id" class="form-control">
                                <option disabled selected>Select Course</option>
                                @foreach($courses as $key => $value)
                                <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="js-area js-chart-area"></div>
                        <div class="js-area js-data-area table-responsive ">
                        </div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_job_tickets_widget'))
                <!-- job tickets section section-->
                <section style="" class="col-xl- px-1 pb-3 table-section section-common-height" id="fcm_job_tickets">
                    <div class="position-relative">
                        {{--<button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>--}}
                        <div class="js-chart-menu">
                            <img src="{{asset('images/menu-down.png') }}" alt=""
                                class="position-absolute menu-down dropbtn">
                            <div class="dropdown">
                                <div class="dropdown-content drop-card-details js-chart-menu-dropdown">
                                    <li data-parent="fcm_job_tickets" data-type="pie">
                                        <img src="{{asset('images/pie.png') }}" alt="">
                                        <span class="pl-2">Pie Chart</span>
                                    </li>

                                    <li data-parent="fcm_job_tickets" data-type="column">
                                        <img src="{{asset('images/bar.png') }}" alt="">
                                        <span class="pl-2">Bar Chart</span>
                                    </li>

                                    <li data-parent="fcm_job_tickets" data-type="dount">
                                        <img src="{{asset('images/Shape 6.png') }}" alt="">
                                        <span class="pl-2">Donut Chart</span>
                                    </li>

                                    <li data-parent="fcm_job_tickets" data-type="0">
                                        <img src="{{asset('images/data.png') }}" alt="">
                                        <span class="pl-2">Data</span>
                                    </li>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="shadow px-3 py-2 bg-white rounded h-100">
                        <label for="" class="label-head">Job Tickets</label>
                        <div class="js-area js-chart-area"></div>
                        <div class="js-area js-data-area table-responsive "></div>
                    </div>
                </section>
                @endif

                @if($dashboardWidgetRepository->canSeeWidget('view_training_compliance'))
                <section style="width:100%; min-height:500px;" class="col-xl- px-1 pb-3 table-section"
                    id="fcm_training_compliance">
                    <div class="position-relative">
                        {{--<button type="button" class="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>--}}
                        <div class="js-chart-menu">
                            <img src="{{asset('images/menu-down.png') }}" alt=""
                                class="position-absolute menu-down dropbtn">
                            <div class="dropdown">
                                <div class="dropdown-content drop-card-details js-chart-menu-dropdown">
                                    <li data-parent="fcm_training_compliance" data-type="pie">
                                        <img src="{{asset('images/pie.png') }}" alt="">
                                        <span class="pl-2">Pie Chart</span>
                                    </li>

                                    <li data-parent="fcm_training_compliance" data-type="column">
                                        <img src="{{asset('images/bar.png') }}" alt="">
                                        <span class="pl-2">Bar Chart</span>
                                    </li>

                                    <li data-parent="fcm_training_compliance" data-type="dount">
                                        <img src="{{asset('images/Shape 6.png') }}" alt="">
                                        <span class="pl-2">Donut Chart</span>
                                    </li>

                                    <li data-parent="fcm_training_compliance" data-type="0">
                                        <img src="{{asset('images/data.png') }}" alt="">
                                        <span class="pl-2">Data</span>
                                    </li>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="shadow px-3 py-2 bg-white rounded h-100">
                        <label for="" class="label-head">Training Compliance</label>
                        <div class="js-area js-chart-area"></div>
                        <div class="js-area js-data-area table-responsive "></div>
                    </div>
                </section>
                @endif


            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="customizeWidgetModal" tabindex="-1" role="dialog"
    aria-labelledby="customizeWidgetModalTitle" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered  modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customizeWidgetModalTitle">Customize Widget</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="dashboard-widget-form">
                    @foreach($dashboardWidgets as $dashboardWidget)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input cb-widget"
                            {{in_array($dashboardWidget->id,$userWidgetsArray)?'checked':''}} name="dashboard_widget[]"
                            value="{{$dashboardWidget->id}}">
                        <label class="form-check-label">{{$dashboardWidget->name}}</label>
                    </div>
                    @endforeach
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="widget-config-save" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')
@include('fmdashboard::customer_map')
<!-- <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script> -->

<script src="{{ asset('js/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('js/highcharts/exporting.js') }}"></script>
<script src="{{ asset('js/highcharts/export-data.js') }}"></script>
<script>
    $(function () {
        Zepto('.sortable').dragswap({
            element: 'section',
            dropAnimation: true
        });
    });
    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable li").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        //customer sidemenu 

        $("#openbtn").click(function(e) {
            e.preventDefault();
            $(".content-cards").toggleClass("customer-show");
        });
    });
</script>
<script>
    // Close the dropdown if the user clicks outside of it
     window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("drop-card-details");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
    
    function getCustomerList(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>
<script>
    //section to process (chart/data) generation
    var sections =[
        'fcm_time',
        'fcm_hr',
        'fcm_incident_summary',
        'fcm_incident_priority',
        //'fcm_site_metrics',
        'fcm_courses',
        'fcm_job_tickets',
        'fcm_training_compliance'
    ];

    //schema for charts
    var chartSchema ={
        'default': fcmDefaultChartSchema(),
        'fcm_time':fcmDefaultChartSchema(),
        'fcm_hr':fcmDefaultChartSchema(),
        'fcm_incident_summary': fcmStackedBarSchema(),
        'fcm_incident_priority':fcmStackedBarSchema(),
        'fcm_courses':fcmDefaultChartSchema(),
        'fcm_job_tickets':fcmDefaultChartSchema(),
        'fcm_training_compliance':fcmBarChartSchema() 
        //'fcm_training_compliance':fcmDefaultChartSchema()
        //'fcm_site_metrics':fcmDefaultChartSchema()
    }

    var fcmDashboardData ={}; //inital dashboard data
    var fcmFetchDashbordApiUrl = '{{route("facility-management-dashboard-api")}}';
    

    //faclility management controller
    function fmtLogger(log){
        //console.log(log);
    }
    function fcmStackedBarSchema(){
        return  {
            exporting: { enabled: false },
			chart: {
				type: 'bar'
			},
			credits: {
                enabled: false
            },
			title: {
				text: ''
			},
			xAxis: {
				categories:[]
			},
			plotOptions: {
				series: {
					stacking: 'normal',
					cursor: 'pointer',
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: ''

                },
			},
			legend: {
				reversed: true
            },
            tooltip: {
        formatter: function() {
            if(this.point.incidents){
                return '<b> '+this.point.category +' - '+ this.series.name +' : '+this.point.y +'</b> <br/>'
           + this.point.incidents;
            }else{
                return '<b> '+this.point.category +' <b> <br>'+ this.series.name +' : <b>'+this.point.y +'</b> <br/>';
            }
         
        }
      },
			series: []
            };
    }

    function fcmDefaultChartSchema(){ 
         return {
            exporting: { enabled: false },
            chart: {
                    type: 'pie'
                },
                title:{
                    text:''
                },
                credits:{
                    enabled:false
                },
                xAxis: {
                        categories:[],
                    },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true,
                        
                    }
                },
                series: [{
                    colorByPoint: true,
                    data: [],
                    cursor: 'pointer',
                }],
                lang: {
                    noData: "Nothing to show"
                },
                noData: {
                    style: {
                        fontWeight: 'bold',
                        fontSize: '15px',
                        color: '#303030'
                    }
                }
                
             }
    }
//used for fcm complaince barstacked
    function fcmBarChartSchema(){ 
         return {
            exporting: { enabled: false },
            chart: {
                    type: 'pie'
                },
                title:{
                    text:''
                },
                credits:{
                    enabled:false
                },
                xAxis: {
                        categories:[],
                    },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true,
                        
                    },
                    column:{
					stacking: 'normal',
					cursor: 'pointer',				
                    }
                },
                series: [{
                    colorByPoint: true,
                    data: [],
                    cursor: 'pointer',
                }],
                lang: {
                    noData: "Nothing to show"
                },
                noData: {
                    style: {
                        fontWeight: 'bold',
                        fontSize: '15px',
                        color: '#303030'
                    }
                }
                
             }
    }
    var fcmc = {
        init:function(){
            var root = this;
            //initialize date picker
            
            $('#startdate').datepicker({
                change: function(e) {
                    if(sessionStorage.getItem("fmd_start_date") != $('#startdate').val()){
                        root.filterTriggered();
                    }                    
                },
                value: moment().subtract(30, 'days').format('YYYY-MM-DD'),
                format: 'yyyy-mm-dd',
                showRightIcon: false
            });
             $('#enddate').datepicker({
                change: function(e) {
                    if(sessionStorage.getItem("fmd_end_date") != $('#enddate').val()){
                        root.filterTriggered();
                    }  
                },
                value:moment().format("YYYY-MM-DD"),
                format: 'yyyy-mm-dd',
                showRightIcon: false
            });

            //handle chart menu events
            $('body').on('click','.js-chart-menu',function(e){
                $(this).find('.js-chart-menu-dropdown').toggleClass('show');
            });

            //chart buttons click function
            $('body').on('click','.js-chart-menu-dropdown li',function(e){
               var parent = $(this).data('parent');
               var type = $(this).data('type');
               root.proceesChartFilter(parent,type);
            });
            //handle filter checkbox change event
            $('.fcm-filter-checkbox').on('change',function(e){
                root.filterTriggered();
            });

            //matrix customer change function
            $('#metrics_customer_id').on('change',function(){
                root.fetchSiteMetrics();
            });
            //matrix customer change function
            $('#reconciliation_customer_id').on('change',function(){
                root.fetchTimeSheetReconciliation();
            });
            //global filter selection
            $('.js-chart-menu-global li').on('click',function(){
                var type = $(this).data('type');
                typeArr = [0];

                if(type){
                    typeArr = type.split('|');
                }
                //process each section and triger the click event
                sections.forEach(function(item){
                    var _section = $('#'+item);
                    if(_section.length > 0){
                        _section.find('.js-chart-menu-dropdown li').each(function(index,el){
                           var _val = $(el).data('type');
                           if(typeArr.indexOf(_val) > -1){
                               $(el).trigger('click');
                               return; //done
                           }
                        });
                    }
                });
            });
            //select2
            $('#metrics_customer_id').select2();
            $('#reconciliation_customer_id').select2();
            $('#course_id').select2();
                        
            $('#course_id').on('change',function(){
                root.fetchCourseData().then(function(res){
                    fcmDashboardData.fcm_courses =res.fcm_courses;
                    let _el = $('#fcm_courses');
                    if(_el.length > 0){
                        var _menus = _el.find('.js-chart-menu-dropdown li');
                        if(_menus.length > 0){
                            //$(_menus[1]).trigger('click');
                            $(_menus[0]).trigger('click');
                        }
                    }
                });
            });


            //On widget save event
            $('#widget-config-save').on('click',function(e){
                root.onSyncWidgetConfiguration();
            });
            //on load if no widgets are selected prompt widget allocation.
            this.onLoadCheckForEmptyWidgetAllocation();
            //fetch chart data onload
            this.filterTriggered();

            //if the widget has customer selection dropdown when the page is load script will fetch 
            ['metrics_customer_id','reconciliation_customer_id','course_id'].forEach(function(el){
                var _target = $("#"+el);
                if(_target.length > 0){
                    _target.prop("selectedIndex", 1).trigger('change');
                }
            });
        },
        onLoadCheckForEmptyWidgetAllocation:function(){
            var widgets = $('#customizeWidgetModal .cb-widget');

            if(widgets.filter(':checked').length <= 0){
                $('#customizeWidgetModal').modal('show');
            }
        },
        onSyncWidgetConfiguration:function(){
            var inputData = $('#dashboard-widget-form').serializeArray();
            //sync data to server.
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('fm-sync-widget-config')}}",
                type: 'POST',
                data: inputData,
                global:false,
                success: function (data) {
                    window.location.reload();
                },
                error: function (xhr, textStatus, thrownError) {

                    swal("Warning", "Something went wrong", "warning");
                },
            });
        },
        fetchChartData:function(){
            return $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    url: fcmFetchDashbordApiUrl,
                    type: 'GET',
                    data:this.collectFilterData(),//collect all required datas
                    global:false
                 });
        },
        afterFetch:function(response){
               fcmDashboardData = response;
               //trigger events
               sections.forEach(function(item){
                    var _el = $('#'+item);
                    //if the section exits then process
                    if(_el.length > 0){
                        var _menus = _el.find('.js-chart-menu-dropdown li');
                        if(_menus.length > 0){
                            //$(_menus[1]).trigger('click');
                            $(_menus[0]).trigger('click');
                        }
                    }
                });
                //set filter attributes
                this.setCounts(response.counts);
        },
        fetchSiteMetrics:function(){
            var filter = this.collectFilterData();
            var customerId = $('#metrics_customer_id').val();

            var url = "{{ route('customer.trendreport',[':customer_id',':payperiod_start',':payperiod_end']) }}";
                url = url.replace(':customer_id', customerId);
                url = url.replace(':payperiod_start', filter.from_date);
                url = url.replace(':payperiod_end', filter.to_date);

            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    url: url,
                    type: 'GET',
                    data:this.collectFilterData(),
                    global:false
            }).then(function(response){
                $('#js_site_metrix_inject').html(response.content);
                // $('#js_site_metrix_inject').html(response.content);
                $(".graph-section-metrics thead tr:nth-of-type(2) th").addClass('trend-report-font');
                $(".graph-section-metrics tbody tr td").addClass('trend-report-font');
                $('.graph-section-metrics tbody tr td span').css("font-weight","");
                refreshSideMenu();
            });
        },
        fetchTimeSheetReconciliation:function(){
            var filter = this.collectFilterData();
            var customerId = $('#reconciliation_customer_id').val();

            var url = "{{ route('fm-timesheet-reconciliation',[':customer_id']) }}";
            url = url.replace(':customer_id', customerId);


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                url: url,
                type: 'GET',
                data:this.collectFilterData(),
                global:false
            }).then(function(response){
                // console.log(response.content)
                $('#js_timesheet_reconcilation_inject').html(response.content);

                refreshSideMenu();
            });
        },
        fetchCourseData(){
            return $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    url: "{{route('fm-training-course-counts')}}",
                    type: 'GET',
                    data:this.collectFilterData(),
                    global:false
             });
        },
        filterTriggered:function(){
            sessionStorage.setItem("fmd_start_date", $('#startdate').val());
            sessionStorage.setItem("fmd_end_date", $('#enddate').val());
            
            var root = this;
            this.fetchChartData().then(function(response){
                //set fcm dashboard data
                root.fetchCourseData().then(function(res){
                    //outside api calls include n the flow
                    response.fcm_courses =res.fcm_courses;
                    root.afterFetch(response);
                   
                });
            });
            //fetch other api calls
            this.fetchSiteMetrics();
            this.fetchTimeSheetReconciliation();
        },
        collectFilterData:function(){
            var selected = [];
            $('.fcm-filter-checkbox:checkbox:checked').each(function(item) {
                    selected.push($(this).data('customerid'));
            });
            return {
                customer_ids:selected,
                from_date: $('#startdate').val(),
                to_date: $('#enddate').val(),
                course_id:$('#course_id').val(),
            }
        },
        setCounts(counts){
            $('#visitor_count').html(counts.visitor_count);
            $('#hours_worked_count').html(counts.hours_worked_count);
            $('#incident_count').html(counts.incident_count);
            $('#job_tickets_count').html(counts.job_tickets_count);
        },
        generateTableRows:function(data){
            //process 
            var headRow ='';
            var bodyRow ='';
            data.head.forEach(function(data){
                headRow += '<th>'+data+'</th>'
            });
            data.body.forEach(function(data){
                var child ='';
               data.forEach(function(el){
                   child+= '<td>'+el+'</td>';
               });
               bodyRow+= '<tr>'+child+'</tr>';
            });
            return ('<table class="table priority-table"><thead>'+headRow+'</thead><tbody>'+bodyRow+'</tbody>');
        },
        //generate matrix table data initialy set for incident priority section
        generateMatrixUi:function(data){
            //process 
            var headRow ='<th></th>';
            var bodyRow ='';
            data.head.x.forEach(function(data){
                headRow += '<th>'+data+'</th>';
            });

            data.body.forEach(function(item,index){
                var child ='<td>'+data.head.y[index]+'</td>';
                item.forEach(function(el,i){
                        child+= '<td><span class="'+data.head.x[i].split(' ').join('').toLowerCase()+'">'+el+'</span></td>';
                });
                bodyRow+= '<tr>'+child+'</tr>';
            });

            return ('<table class="table"><thead>'+headRow+'</thead><tbody>'+bodyRow+'</tbody>');
        },
        generateDataSection:function(options){
            var parentId = options.parent;
            
            if(fcmDashboardData.hasOwnProperty(parentId)){
                var dataObject = fcmDashboardData[parentId];

                if($('#'+parentId+' .js-data-area').length > 0){
                    //render table
                    if(dataObject.hasOwnProperty('table')){
                        $('#'+parentId+' .js-data-area').html(this.generateTableRows(dataObject.table));
                    }
                    //rener matrix
                    if(dataObject.hasOwnProperty('matrix')){
                        $('#'+parentId+' .js-data-area').html(this.generateMatrixUi(dataObject.matrix));
                    }
                }else{
                    fmtLogger('Data Area not found #'+ parentId);
                }
              
            }
        },
        /**
             parent: #parent element
             type: #chart type
         */
        generateChart:function(options){
            var parentId = options.parent;
            //assign default schema
            let chart = JSON.parse(JSON.stringify(chartSchema.default));
            if(chartSchema.hasOwnProperty(parentId)){
               chart = JSON.parse(JSON.stringify(chartSchema[parentId]));
            }
            //dount chart attributes
            if(options.type === 'dount'){
                chart.chart.type = 'pie';
                chart.plotOptions.pie.innerSize = '50%'; //for daunt chart
            }else if(options.type === 'pie'){
                chart.chart.type = 'pie';
                chart.plotOptions.pie.innerSize = '0%';
            }
            else{
                chart.chart.type = options.type;
            }

            if(fcmDashboardData.hasOwnProperty(parentId)){
                var dataObject = fcmDashboardData[parentId];

                //Fix for training compliance custom bar chart.
                if(options.type == 'column' && dataObject.hasOwnProperty('barChart')){
                    if(dataObject.barChart.hasOwnProperty('series'))
                    {
                        chart.series = dataObject.barChart.series;
                    }
                    if(dataObject.barChart.hasOwnProperty('label'))
                    {
                        chart.xAxis.categories = dataObject.barChart.label;
                    }
                }
               else if(dataObject.hasOwnProperty('chart')){
                    //if the data is in series mode
                    if(dataObject.chart.hasOwnProperty('series')){
                        chart.series = dataObject.chart.series;
                    }
                    //if the data is in value mode set single data
                    if(dataObject.chart.hasOwnProperty('value')){
                        chart.series[0].data = dataObject.chart.value;
                        chart.series[0].name = '';
                    }
                    //set categories if exists in the request
                    if(dataObject.chart.hasOwnProperty('label')){
                        chart.xAxis.categories = dataObject.chart.label;
                    }
                }
            }
            $('#'+parentId).find('.js-chart-area').highcharts(chart);
        },
        /**
            parent:#parent element
            type:#selected chart type
         */
        proceesChartFilter:function(parent,type){
            var root = this;
            var parentEl = $('#'+parent);
            if(parentEl.length <= 0){
                fmtLogger('proceesChartFilter: parent not found.');
            }
            //toggle data section
            if(!type || type === 0){
                root.generateDataSection({
                    parent,
                    type
                });
                parentEl.find('.js-area').hide();
                parentEl.find('.js-data-area').show();
            }
            else{
                //toggle chart section
                root.generateChart({
                    parent,
                    type
                })
                //display the chart
                parentEl.find('.js-area').hide();
                parentEl.find('.js-chart-area').show();
            }
        },
    }
    
    $(function(){
        fcmc.init();
    });
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
{{--

</body>

</html>--}}