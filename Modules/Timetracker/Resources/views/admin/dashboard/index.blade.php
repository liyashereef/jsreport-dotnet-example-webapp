@extends('layouts.app')
<style>
    .profileImage {
        width: 15rem;
        height: 15rem;
        border-radius: 50%;
        font-size: 2.5rem;
        color: #fff;
        text-align: center;
        line-height: 15rem;
        margin: 2rem 0;
    }

    .user-image-div {
        text-align: left;
        padding-left: 0px !important;
    }

    .g-map {
        /* height: 100%; */
        min-height: 252px;
    }

    .incident-table-wrap {
        min-height: 318px;
    }
    #driver-map-filter-section,
    #alarm-response-filter-section.filter-section{
        width:65%;
    }
    .py-custom{
        padding-top: .3rem !important;
        padding-bottom: .3rem !important;
    }
    .map-widget .g-map {
        min-height: auto !important;
    }

    .map-widget .card-body{padding:0;}

    .pl-2 a{
    color: #212529;
    text-decoration: none;
    }
    .pl-2 a:hover{
        color: #212529;
        text-decoration: none;
    }
    .map-widget .card-body{
        padding:0;

    }
    .card-body{
        overflow-y: auto;
        display:flex;
    }
    .card{
        max-height: 60vh;
        min-height:60vh;
        overflow: hidden;
    }
    .g-map{
        flex-grow: 1;
    }

</style>

@section('content')
    <div class="table_title">
        <h4>MST Dispatch</h4>
    </div>
    {{--Section one--}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <span class="pl-2">MST Overview</span>
                </div>
                <div class="card-body">
                    <canvas id="mst-overview-chart-container"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card map-widget">
                <div class="card-header d-flex justify-content-between py-custom">
                    <span class="pl-2" >
                    <a href="{{ route('timetracker.shift-live-locations', '2') }}">
                        Driver Map
                    </a>
                    </span>
                    {{--Filter container--}}
                    <div id="driver-map-filter-section">
                        <div id="alarm-response-filter-section">
                            <div class="form-group mb-0">
                                <select class="" id="driver-map-filter-input">
                                    <option value="" selected>All</option>
                                    @foreach($mst_drivers as $mst_driver)
                                        <option value="{{$mst_driver->id}}"
                                        >{{$mst_driver->first_name}} {{$mst_driver->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{--Filter button--}}
                    <!-- <a href="javascript:void(0)"
                       class="btn btn-sm"
                       id="js-driver-map-filter-btn">Filter</a> -->



                    <div class="g-map" id="mst-driver-map"></div>
                </div>
            </div>
        </div>
    </div>

    {{--Section two--}}
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="card map-widget ">
                <div class="card-header d-flex justify-content-between py-custom">
                    <span class="pl-2">Alarm Response Status</span>
                    <div id="alarm-response-filter-section" class="filter-section">
                    <!-- <div class="form-group mb-0"> -->
                        <select class="form-control" id="alarm-response-filter-status">
                            <option selected disabled>Filter by Status</option>
                            <option value="0">All</option>
                            <option value="1">Open</option>
                            <option value="2,3">In Progress</option>
                            <option value="4">Closed</option>
                        </select>
                    <!-- </div> -->
                    </div>
                </div>
                <div class="card-body">
                    {{--Filter button--}}
                    <!-- <a href="javascript:void(0)"
                       class="btn btn-sm"
                       id="js-alarm-map-filter-btn">Filter</a> -->


                    <div class="g-map" id="mst-alarm-response-map"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <span class="pl-2"><a href="{{ route('dispatchrequest.index') }}"> Alarm Response List</a></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive incident-table-wrap">
                        <table id="incident-reporting-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Request Type</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Postal Code</th>
                                <th>Status</th>
                                <th>Rate</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script src="https://www.chartjs.org/dist/2.8.0/Chart.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}"></script>
    <script>
var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        // Chart Section
        $(function () {

            window.chartColors = {
                red: 'rgb(255, 99, 132)',
                orange: 'rgb(255, 159, 64)',
                yellow: 'rgb(255, 205, 86)',
                green: 'rgb(75, 192, 192)',
                blue: 'rgb(54, 162, 235)',
                purple: 'rgb(153, 102, 255)',
                grey: 'rgb(231,233,237)'
            };

            var config = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [
                            //blade
                            {{$count_request_open}},
                            {{$count_request_in_progress}},
                            {{$count_request_closed}}
                        ],
                        backgroundColor: [
                            window.chartColors.red,
                            window.chartColors.yellow,
                            window.chartColors.green
                        ],
                        label: 'Dataset 1'
                    }],
                    labels: [
                        'Open ({{$count_request_open}})',
                        'In Progress ({{$count_request_in_progress}})',
                        'Closed ({{$count_request_closed}})',
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Status History'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            };

            var ctx = document.getElementById('mst-overview-chart-container').getContext('2d');
            window.myDoughnut = new Chart(ctx, config);
        });

        // Driver Map Section
        $(function () {
            var icons = {
                car: '{{asset('images/markers/car.png')}}',
                car_active: '{{asset('images/markers/car_active.png')}}',
                car_idle: '{{asset('images/markers/car_idle.png')}}',
            };
            var mstMarkers = [];

            //Create the map
            var mstDriverMap = new google.maps.Map(document.getElementById('mst-driver-map'), {
                center: new google.maps.LatLng({{config('globals.map_default_center_lat')}}, {{config('globals.map_default_center_lng') }}),
                zoom: 7,
                streetViewControl: false,
                gestureHandling: 'greedy'
            });
            //Create the info window
            var mstInfoWindow = new google.maps.InfoWindow({
                content: '',
                maxWidth: 200
            });

            function setDeviceCoordinates(data) { //console.log(data);
                //clean markers in map
                mstMarkers.forEach(function (marker) {
                    marker.setMap(null);
                });
                //clean the marker array
                mstMarkers = [];

                data.content.forEach(function (item) {
                    var currentMarkerIcon = icons.car;

                    // Create markers.
                    // if (Object.prototype.hasOwnProperty.call(item, 'pending_dispatch_request')
                    //     && item.pending_dispatch_request) {
                    //     currentMarkerIcon = icons.car_active;
                    // } else
                     if (item.is_idle) {
                        currentMarkerIcon = icons.car_idle;
                    }

                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(item.latitude, item.longitude),
                        icon: currentMarkerIcon,
                        map: mstDriverMap
                    });

                    //On click the marker
                    google.maps.event.addListener(marker, 'click', function () {
                        mstInfoWindow.setContent(buildVehicleCoordinateInfoWindowContent(item));
                        mstInfoWindow.open(mstDriverMap, marker);
                    });
                    //Save marker reference
                    mstMarkers.push(marker);
                })
            }

            function buildVehicleCoordinateInfoWindowContent(item) {
                // var contentString = '<div id="content">' +
                //     '<div id="siteNotice">' +
                //     '</div>' +
                //     '<div id="bodyContent">' +
                //     '<h5>' + data.user.first_name + ' ' + data.user.last_name + '</h5>\n';
                //     if(data.pending_dispatch_request){
                //         contentString +='<p>' + data.pending_dispatch_request.site_address + '</p>' +
                //     '<p> Subject : ' + data.pending_dispatch_request.subject + '</p>';
                //     }

                //     contentString +='</div>' +
                //     '</div>';

        var employee_phone = item.phone;
        var employee_id = item.employee_no;
        var employee_name  = camelcase(item.user.first_name);
        var employee_last_name = (item.user.last_name)?camelcase(item.user.last_name):'--';
        var last_name = (item.user.last_name)?(item.user.last_name):' ';
        var full_name=camelcase(item.user.first_name)+' '+item.user.last_name;
        var employee_full_address =(item['address'])? camelcase(item['address']):'--';
        var city = (item.city)?camelcase(item.city):'--';
        var postal_code = (item.postal_code)?item.postal_code:'--';
        var phone = (item.user.employee.phone)?item.user.employee.phone:'--';
        var cell_no = (item.user.employee.cell_no)?item.user.employee.cell_no:'--';
        var phone_ext = (item.phone_ext)?(' x'+item.phone_ext):'';
        var email = (item.user.employee.employee_work_email)?item.user.employee.employee_work_email:'--';

        var image_html = '';
        if(item.user.employee.image != null && item.user.employee.image != "") {
            var image = "{{asset('images/uploads/') }}/" + item.user.employee.image;
            image_html = '<img name="image" src="'+image+'"  class="profileImage">';
        }else{
            var initial_characters = (employee_name? employee_name.charAt(0): '') + ((last_name != "")? last_name.charAt(0): camelcase(employee_name.charAt((employee_name.length - 1))));
            image_html = '<div class="profileImage" style="background: linear-gradient(to bottom, #F2351F, #F17437);">'+initial_characters+'</div>';
        }

        var customer ='--';
        if(item.employee_shift){
        if(item.employee_shift.shift_payperiod.customer){
          var customer = (item.employee_shift.shift_payperiod.customer.client_name)?item.employee_shift.shift_payperiod.customer.client_name:'--';
        }
        if(item.employee_shift.shift_payperiod.customer.employee_latest_customer_supervisor){
          var supervisor_first_name = camelcase(item.employee_shift.shift_payperiod.customer.employee_latest_customer_supervisor.supervisor.first_name);
          var supervisor_last_name = camelcase(item.employee_shift.shift_payperiod.customer.employee_latest_customer_supervisor.supervisor.last_name);
          var supervisor = supervisor_first_name+' '+supervisor_last_name;
          var supervisor_contact_no = item.employee_shift.shift_payperiod.customer.employee_latest_customer_supervisor.supervisor.employee.phone;
          var supervisor_cell_no = item.employee_shift.shift_payperiod.customer.employee_latest_customer_supervisor.supervisor.employee.cell_no;
        }else{
          var supervisor = '--';
          var supervisor_contact_no = '--';
          var supervisor_cell_no = '--';
        }
        if(item.employee_shift.shift_payperiod.customer.employee_latest_customer_area_manager){
          var area_manager_first_name = camelcase(item.employee_shift.shift_payperiod.customer.employee_latest_customer_area_manager.area_manager.first_name);
          var area_manager_last_name = camelcase(item.employee_shift.shift_payperiod.customer.employee_latest_customer_area_manager.area_manager.last_name);
          var area_manager = supervisor_first_name+' '+supervisor_last_name;
          var area_manager_contact_no = item.employee_shift.shift_payperiod.customer.employee_latest_customer_area_manager.area_manager.employee.phone;
          var area_manager_cell_no = item.employee_shift.shift_payperiod.customer.employee_latest_customer_area_manager.area_manager.employee.cell_no;
        }else{
          var area_manager = '--';
          var area_manager_contact_no = '--';
          var area_manager_cell_no = '--';
        }
        var rating = (item.user.employee.employee_rating)?item.user.employee.employee_rating:'--';


                    var content='<div id="content" style="min-width:0px;" class="map-tooltip">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;<a style="color:#f26338;" href="">'+full_name+'</a></h4>' +
                            '<div id="bodyContent">' +
                            '<label class="col-md-12 col-12 scrollable"> <div class="row"> <div class="col-7"> <div class="row"> <div class="col-6 p0">Employee Name</div> <div class="col-6 p0 map-disc popup-value">'+full_name+'</div> </div> <div class="row"> <div class="col-6 p0">Work Number</div> <div class="col-6 p0 map-disc popup-value">'+phone+phone_ext+'</div> </div> <div class="row"> <div class="col-6 p0">Cell Number</div> <div class="col-6 p0 map-disc popup-value">'+cell_no+'</div> </div> <div class="row"> <div class="col-6 p0">Work Email</div> <div class="col-6 p0 map-disc popup-value">'+email+'</div> </div> <div class="row"> <div class="col-6 p0">Rating</div> <div class="col-6 p0 map-disc popup-value">'+rating+'</div> </div> <div class="row"> <div class="col-6 p0">Customer</div> <div class="col-6 p0 map-disc popup-value">'+customer+'</div> </div> <div class="row"> <div class="col-6 p0">Supervisor</div> <div class="col-6 p0 map-disc popup-value">'+supervisor+'</div> </div> <div class="row"> <div class="col-6 p0">Work Number</div> <div class="col-6 p0 map-disc popup-value">'+supervisor_contact_no+'</div> </div> <div class="row"> <div class="col-6 p0">Cell Number</div> <div class="col-6 p0 map-disc popup-value">'+supervisor_cell_no+'</div> </div> <div class="row"> <div class="col-6 p0">Area Manager</div> <div class="col-6 p0 map-disc popup-value">'+area_manager+'</div> </div> <div class="row"> <div class="col-6 p0">Work Number</div> <div class="col-6 p0 map-disc popup-value">'+area_manager_contact_no+'</div> </div> <div class="row"> <div class="col-6 p0">Cell Number</div> <div class="col-6 p0 map-disc popup-value">'+area_manager_cell_no+'</div> </div> </div> <div class="col-5 user-image-div">'+image_html+'</div> </div> </label>'+
                            '</div>' +
                            '</div>';
    }
                return content;
            }

            function getDriverMapFilterParams() {
                return options = {
                    "user_id": $('#driver-map-filter-input').val(),
                    "shift_type_flag":2,
                };

            }

            //Fetch device coordinates from the server
            function fetchDeviceCoordinates() {

                var url = '{{ route('dispatch_request_coordinates_web')}}';


                var params = getDriverMapFilterParams();

                // var queryString = Object.keys(params).map(function (key) {
                //     if (params[key] && params[key].length > 0) {
                //         return key + '=' + params[key]
                //     }
                // }).join('&');

                //append the query string
                // url += '?' + queryString
                url += '?' + $.param(params);

                $.get({
                    url: url,
                    type: "GET",
                    global: false,
                    timeout: 15000,
                    success: function (data) {
                        setDeviceCoordinates(data)
                    },
                    complete: function (data) {
                    }
                });
            }

            //initial loading
            fetchDeviceCoordinates();

            //frequently fetch & update locations
            setInterval(function () {
                fetchDeviceCoordinates();
            }, 10000);


            //Select 2 init
            $('#driver-map-filter-input').select2({
                // placeholder: "Select MST Driver",
            });

            //On select mst driver
            $('#driver-map-filter-input').on('select2:select', function (e) {
                fetchDeviceCoordinates();
            });


            $('#js-driver-map-filter-btn').click(function () {
                //Reset the fields
                $('#driver-map-filter-input').val('');
                //toggle the section
                $('#driver-map-filter-section').toggle();
            });


        });

        // Alarm Response Map
        $(function () {

            //current dispatch Requests
            var dispatchRequests = [];

            //Initialize the map
            var mstAlarmResponseMap = new google.maps.Map(document.getElementById('mst-alarm-response-map'), {
                center: new google.maps.LatLng(43.761539, -79.411079),
                zoom: 6,
                streetViewControl: false,
                gestureHandling: 'greedy'
            });

            //Custom marker icons
            var markerIcons = {
                red: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                yellow: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                green: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
            };

            //Create the info window
            var mstAlrInfoWindow = new google.maps.InfoWindow({
                content: '',
                maxWidth: 200
            });

            var alarmResponseMarkers = [];

            function getDispatchMarkerIcon(status) {
                switch (status) {
                    case 1:
                        return markerIcons.red;
                    case 4:
                        return markerIcons.green;
                    case 2:
                    case 3:
                        return markerIcons.yellow;
                    default:
                        return markerIcons.yellow;

                }
            }

            function setAlarmResponseMapMarkers(dispatchRequests) { //console.log(dispatchRequests);
                alarmResponseMarkers.forEach(function (marker) {
                    marker.setMap(null);
                });
                alarmResponseMarkers = [];
                //Render Dispatch Request in map
                dispatchRequests.forEach(function (item) {
                    // Create markers.
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(item.latitude, item.longitude),
                        icon: getDispatchMarkerIcon(item.dispatch_request_status_id),
                        map: mstAlarmResponseMap
                    });

                    //On click the marker
                    google.maps.event.addListener(marker, 'click', function () {
                        // mstAlrInfoWindow.setContent('info window');
                        // mstAlrInfoWindow.open(mstAlarmResponseMap,marker);
                    });
                    alarmResponseMarkers.push(marker);
                });
            }


            //initialize map markers first time
            setAlarmResponseMapMarkers(dispatchRequests);

            //fetch dispatch requests by status filter
            function fetDispatchRequestsByStatus(status) {
                var url = '{{route('dispatch_coordinates_status_array_web')}}'
                $.get({
                    url: url,
                    type: "GET",
                    data: {
                        status
                    },
                    timeout: 15000,
                    global: false,
                    success: function (data) {
                        setAlarmResponseMapMarkers(data)
                    },
                    complete: function (data) {
                    }
                });

            }

            //First time fetch and render all dispatch requests
            fetDispatchRequestsByStatus();

            //On change filter dropdown
            $("#alarm-response-filter-status").change(function () {
                fetDispatchRequestsByStatus(this.value);
            });

            //Alarm map filter button click
            $('#js-alarm-map-filter-btn').click(function () {
                $('#alarm-response-filter-section').toggle();
            });

        });

        //Incident Reporting Section
        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                var table = $('#incident-reporting-table').DataTable({
                    bprocessing: false,
                    processing: false,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('dispatch_request.list') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    order: [
                        [1, "asc"]
                    ],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    columns: [{
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                        {
                            data: 'dispatch_request_type.name',
                            name: 'dispatch_request_type.name'
                        },
                        {
                            data:null,
                            render:function(o){
                                if(o.customer_id != null) {
                                    return o.customer_trashed.client_name
                                }else{
                                    return o.name
                                }
                            },
                            sortable:false,
                            name:'customer_name'
                        },
                        {
                            data: 'site_address',
                            name: 'site_address'
                        },
                        {
                            data: 'site_postalcode',
                            name: 'site_postalcode'
                        },
                        {
                            data: 'dispatch_request_status.name',
                            name: 'dispatch_request_status'
                        },
                        {
                            data: 'rate',
                            name: 'rate'
                        },
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {

                                var actions = '';
                                var show_url = '{{ route("dispatchrequest.show", ":id") }}';
                                show_url = show_url.replace(':id', o.id);
                                actions += '<a title="Show" href="' + show_url + '" class="view btn fa fa-eye" data-id=' + o.id + '></a>';

                                return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
            }
        });
    </script>
@stop
