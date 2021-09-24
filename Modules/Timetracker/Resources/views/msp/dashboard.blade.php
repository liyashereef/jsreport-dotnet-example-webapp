@extends('layouts.app')
@section('css')
<link href="{{ asset('faclitymanagementdashboard/dashboard-styles.css') }}" rel="stylesheet">
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}"></script>
@endsection
@section('content')
<div class="content-component px-3 pt-3 pb-5">
    <!-- top card area -->
    <div class=" mainlink-component card-view-section mb-2  position-relative">
        <div>
            <div class="mapping mapping-ie mapping-site-dashboard" id="openbtn">
                <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x" aria-hidden="true"></i></a>
            </div>
            <div class="position-absolute icons-top-left d-flex flex-column">
                <div class="dropdown tab-btn">
                    <button type="button" class=" dropdown-toggle" data-toggle="dropdown">
                        <img src="{{asset('images/pie.png') }}" alt="">
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item stc-tab-view" href="#" data-value="action-1">Map</a>
                        <a class="dropdown-item stc-tab-view" href="#" data-value="action-2">Table</a>
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
                                <label for="" id="sat-dc-locations" class="mb-0 text-white label-day">0 </label>
                                <label for="" class="mb-0 text-white label-name">Locations</label>
                            </span>
                        </div>
                        <img src="{{asset('images/visitors.png') }}" alt="date" class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <div class="d-flex pr-4">
                            <span class="d-flex flex-column  pr-2">
                                <label for="" id="sat-dc-c-visits" class="mb-0 text-white label-day">0</label>
                                <label for="" class="mb-0 text-white label-name">Contractual Visits</label>
                            </span>
                        </div>
                        <img src="{{asset('images/Incidents.png') }}" alt="date" class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" id="sat-dc-actual-visits" class="mb-0 text-white label-day">0</label>
                            <label class="mb-0 text-white label-name">Actual Visits</label>
                        </span>
                        <img src="{{asset('images/tickets.png') }}" alt="date" class="position-absolute logos-top-card">
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12  px-2 pb-2">
                <div class="card">
                    <div class="card-body d-flex align-items-center py-2">
                        <span class="d-flex flex-column  pr-2">
                            <label for="" id="sat-dc-delta" class="mb-0 text-white label-day">0</label>
                            <label class="mb-0 text-white label-name">Delta</label>
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
            <div class="fixedsideblock" style="padding:10">
                
                    <label for="" class="label-head w-90 position-relative mb-0">Customer
                    </label>

                
            </div>
            <div class="fixedsideblock">
                <div class="form-group has-search position-relative mb-0">
                    <span class="fa fa-search form-control-feedback"></span>
                    <input type="text" id="customerSearch" class="form-control search-customer" placeholder="Enter Customer Name">
                </div>
                
            </div>
            <div class="fixedsideblock">
                <button type="button" class="btn btn-primary filterbutton ">Search</button>
            </div>
            <div class="h-100 row" style="">
                <div class="px-3 py-2 bg-white rounded h-100 col-md-12">
                    
                    <div class="tab d-flex tab-customer list-unstyled">

                        <li class="tablinks pb-4 ml-0 mb-0 pl-3 pr-0 cursor-pointer" onclick="getCustomerList(event, 'actual')" id="defaultOpen">Permanent</li>
                        <li class="tablinks pb-4 ml-0 mb-0 pl-3 pr-0 cursor-pointer" onclick="getCustomerList(event, 'ytd')">Temporary</li>
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
                                                <li class="customer-name{{$i}}" style="text-align:left;">
                                                    <div class="filter_checkbox atl m-r-checkbox">
                                                        <input type="checkbox" name="atl" id="chk-atl{{$customer->id}}" class="sat-filter-checkbox largerCheckbox" data-customerid="{{$customer->id}}" style="margin-top:12px;float:right;">
                                                    </div>

                                                    <div id="{{$i}}">
                                                        <div class="float-right" style="width:60px;margin-right:5px;" aria-hidden="true">
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
                                <tbody id="myTable">
                                    <tr>
                                        <td>
                                            @if(isset($stcCustomers))
                                            <div class="scrollable">
                                                @foreach($stcCustomers as $i=>$customer)
                                                <li class="customer-name{{$i}}" style="text-align:left;">
                                                    <div class="filter_checkbox atl m-r-checkbox">
                                                        <input type="checkbox" name="atl" id="chk-atl{{$customer->id}}" class="largerCheckbox sat-filter-checkbox" data-customerid="{{$customer->id}}" style="margin-top:12px;float:right;">
                                                    </div>

                                                    <div id="{{$i}}">
                                                        <div class="float-right" style="width:60px;margin-right:5px;" aria-hidden="true">
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

                <!-- time section-->
                <section class="col-xl-12 px-1 pb-3   section-common action-2">
                    <div class="position-relative">
                    </div>
                    <div class="px-3 py-2 bg-white rounded position-relative">
                        <table class="table table-bordered" id="geosat-table">
                             

                                <div id="filter">
                                    <label><input type="radio"  id="all" class="all" name="list_type" value="1"> <span>All</span></label>
                                    <label><input type="radio" class="visited" name="list_type" value="2" > <span>Visited</span></label>
                                    <label><input type="radio" class="missed" name="list_type" value="3"> <span>Missed</span></label>
                                </div>
                            <thead>
                                  
                                <tr>
                                    <th></th>
                                    <th>Location Name</th>
                                    <th>Address</th>
                                    <th>Project Name and No</th>
                                    <th>Contractual Visits</th>
                                    <th>Actual visits</th>
                                    <th>Delta</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </section>


                <!-- site dashboard section-->
                <section class="col-xl-12 px-1 pb-2   section-common  action-1" id="item3">
                    <div id="map"></div>
                </section>



            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalContent" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div id="modal-content" style="height: 500px;" class="modal-body">
            </div>
            <div align="center"  style="display: none;"  id="modal-img-content" style="height: 550px;" class="modal-body">
                <div style="text-align: center;" >
                    <img  style="left: 50%;max-width: 600px;"  height="400px" id="ImgContainer" src="">
                </div>

            </div>

        </div>
    </div>
</div>

@stop
@section('scripts')
<script>
    //initialize variables
    $("#startdate").val(moment().subtract(30, 'days').format('YYYY-MM-DD'));
    $("#enddate").val(moment().format('YYYY-MM-DD'));
    const logo = '<img src="{{ asset("images/short_logo.png") }}">';
    const centralLocation = new google.maps.LatLng(44.402640, -80.080687);
    const markerConfiguration = {!!$markerConfigurations!!};
    var satTrackingMap = null;
    var infowindow = new google.maps.InfoWindow();
    var bounds = null;
    var markers = [];
    var geoSatTable = null;
    var dashboardCardData = {};
    var allocatedCustomerExtraInfo = {!!json_encode($customerExtraInfo)!!};
    var currentRequest = null; 
    var tabrequest = 0;
    const satc = {
        init() {
            let root = this;
            //user click customer checkbox
            $('.sat-filter-checkbox').on('change', function(e) {
                //root.onInputChanged();
            });

            $('.filterbutton').on('click',function(e){
                $('body').loading({
                    stoppable: false,
                    message: 'Please wait...'
                });
                $(this).prop("disabled",true);
                root.onInputChanged();
            })
            //dates
            $('#startdate').datepicker({
                change: function(e) {
                    if (sessionStorage.getItem("sat-start-date") != $('#startdate').val()) {
                        root.onInputChanged()
                    }
                },
                value: moment().subtract(30, 'days').format('YYYY-MM-DD'),
                format: 'yyyy-mm-dd',
                showRightIcon: false
            });
            $('#enddate').datepicker({
                change: function(e) {
                    if (sessionStorage.getItem("sat-end-date") != $('#enddate').val()) {
                        root.onInputChanged();
                    }
                },
                value: moment().format("YYYY-MM-DD"),
                format: 'yyyy-mm-dd',
                showRightIcon: false
            });
            //initialize map
            this.initMap();
            //apply logic on page load
            this.triggerEntryPoint();
        },
        triggerEntryPoint() {
            //on input change fetch the dashboard  datas
            this.fetchSatDashboardData();
        },
        initMap() {
            // The map, centered at given location
            satTrackingMap = new google.maps.Map(
                document.getElementById('map'), {
                    zoom: 8,
                    center: centralLocation,
                    gestureHandling: 'greedy'
                });
        },
        calculateDelta(expected, actual) {
            let delta = Number(expected) - Number(actual)
            return delta >= 0 ? delta : 0;
        },
        getMangerInfoItem(customerId, managerType, key) {
            let defaultString = '';
            //get current customer extra info
            let object = allocatedCustomerExtraInfo.find(x => x.customerId === customerId);
            //check invalid object or array
            if (!object || Array.isArray(object)) {
                return defaultString;
            }
            //re-assign object of type managerType
            object = object[managerType];
            //check the object has property
            if (!Object.prototype.hasOwnProperty.call(object, key)) {
                return defaultString;
            }
            //return value from the object.
            return object[key] ? object[key] : defaultString;
        },
        //global object 
        getFenceSummaryItem(parentObject, key) {
            let defaultString = '';

            let summary = parentObject.mobile_security_patrol_fence_summaries;
            //check has item
            if (summary.length <= 0) {
                return defaultString;
            }
            //server relation is #hasMany
            let summaryItem = summary[0];
            //if the propery not exitst return default string
            if (!Object.prototype.hasOwnProperty.call(summaryItem, key)) {
                return defaultString;
            }
            return summaryItem[key] ? summaryItem[key] : defaultString;
        },
        generateInfoWindowContent(data,isActive) {
            let customerId = data.customer_trashed.id;
            let fenceSummaryInfo = '';
            //if active process the fence summay info
            if(isActive){
                fenceSummaryInfo = `
                        <div class="row">
                                <div class="col-md-6 map-label">Actual Visits</div>
                                    <div class="col-md-6 map-disc">${
                                        this.getFenceSummaryItem(data,'total_visit_count_actual')
                                        }
                                    </div>
                                </div>

                            <div class="row">
                                <div class="col-md-6 map-label">Delta</div>
                                <div class="col-md-6 map-disc">
                                    ${this.calculateDelta(
                                        data.contractual_visit,
                                        this.getFenceSummaryItem(data,'total_visit_count_actual')
                                    )}
                                </div>
                            </div>
                        `;
            }

            $unitofmeasurerow = '';

            if(data.contractual_visit_unit!=null){
                $unitofmeasurerow ='<div class="row"><div class="col-md-6 map-label">Unit of measure</div><div class="col-md-6 map-disc">';
                $unitofmeasurerow += data.contractual_visit_unit.value+'</div></div>';
            }
            return `
            <div id="content" class="map-tooltip">
                    <h4 id="firstHeading" class="firstHeading">${logo}&nbsp;
                        <span>${data.title}</span>
                    </h4>

                    <div id="bodyContent">
                        <div class="row">
                            <div class="col-md-6 map-label">Location Address</div>
                            <div class="col-md-6 map-disc">${data.address}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 map-label">Contractual Visits</div>
                            <div class="col-md-6 map-disc">
                            ${(data.contractual_visit)?data.contractual_visit:0}
                            </div>
                        </div>
                        ${(data.contractual_visit_unit != null) 
                            ? $unitofmeasurerow :''}
                        
                        ${fenceSummaryInfo}
                        <div class="row">
                            <div class="col-md-6 map-label">Project Name and No</div>
                            <div class="col-md-6 map-disc">
                            ${data.customer_trashed.client_name}
                            -${data.customer_trashed.project_number}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 map-label">Regional Manager</div>
                            <div class="col-md-6 map-disc">
                            ${this.getMangerInfoItem(customerId,'areamanager','first_name')}
                            ${this.getMangerInfoItem(customerId,'areamanager','last_name')}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 map-label" style="white-space: nowrap">RM Contact</div>
                            <div class="col-md-6 map-disc">
                            ${this.getMangerInfoItem(customerId,'areamanager','phone')}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 map-label">Supervisor</div>
                            <div class="col-md-6 map-disc">
                            ${this.getMangerInfoItem(customerId,'supervisor','first_name')}
                            ${this.getMangerInfoItem(customerId,'supervisor','last_name')}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 map-label">Supervisor Contact</div>
                            <div class="col-md-6 map-disc">
                            ${this.getMangerInfoItem(customerId,'supervisor','phone')}
                            </div>
                        </div>

                    </div>
                </div>`;

        },
        getMarkerIconColor(item) {
            let color = 'red'; //as default color
            //find actual visit percentage
            let average = (Number(this.getFenceSummaryItem(item,'total_visit_count_actual')) /
                    Number(item.contractual_visit)) *
                100;
            average = average <= 100 ? average : 100;
            markerConfiguration.forEach(function(mc) {
                if (Number(mc.min) <= average && Number(mc.max) >= average) {
                    color = mc.color;
                }
            });
            return color;
        },
        getMarkerIcon(item) {
            let iconBase = '{{asset("images/markers")}}';
            let color = this.getMarkerIconColor(item);
            return `${iconBase}/${color}-dot.png`;
        },
        getInactiveMarkerIcon(){
            let iconBase = '{{asset("images/markers")}}';
            return `${iconBase}/black-dot.png`;
        },
        generateMarker(item,isActive) {
            //generate info window content
            let root = this;
            let position = new google.maps.LatLng(item.geo_lat, item.geo_lon);
            let markerIcon = (isActive) ? this.getMarkerIcon(item): this.getInactiveMarkerIcon();
            //The marker, positioned at given location
            let marker = new google.maps.Marker({
                position,
                map: satTrackingMap,
                icon: markerIcon
            });
            bounds.extend(position);
            marker.addListener('click', function() {
                let infowindowContent = root.generateInfoWindowContent(item,isActive);
                infowindow.setContent(infowindowContent);
                infowindow.open(satTrackingMap, marker);
            });
            return marker;
        },
        processMapDataAndRender(data) {
            let root = this;
            //clear dashboard data
            dashboardCardData = {
                locations: 0,
                contractualVisits: 0,
                actualVisits: 0,
                expectedVisits: 0,
                delta: 0
            };
            //clear all existing markers
            while (markers.length) {
                markers.pop().setMap(null);
            }
            //satTrackingMap.fitBounds();
            bounds = new google.maps.LatLngBounds();

            //set active markers
            data.map_data.active.forEach(function(item) {
                let marker = root.generateMarker(item,true);
                markers.push(marker);
                dashboardCardData.actualVisits += Number(root.getFenceSummaryItem(item,'total_visit_count_actual'));
                dashboardCardData.expectedVisits += Number(root.getFenceSummaryItem(item,'total_visit_count_expected'));
                dashboardCardData.contractualVisits += Number(item.contractual_visit);
            });
            //set inactive markers
            data.map_data.inactive.forEach(function(item) {
                let marker = root.generateMarker(item,false);
                dashboardCardData.contractualVisits += Number(item.contractual_visit);
                markers.push(marker);
            });
            //bound marker postions
            if (markers.length > 0) {
                satTrackingMap.fitBounds(bounds);
            }

            dashboardCardData.locations = data.map_data.active.length + data.map_data.inactive.length;
            dashboardCardData.delta = this.calculateDelta(
                dashboardCardData.contractualVisits,
                dashboardCardData.actualVisits
            );
            //set dashboard card data
            this.setDashboardCardData();
        },
        satTrackChildTable(responsedata) {
            var html = '';
            $.each(responsedata, function(key, gfs) {
                console.log(responsedata[key]);
                if(responsedata[key][7]=="00:00:00"){
                    var cls = 'missed_row';
                }
                else {
                    var cls ='visited_row';
                }
                html += `
                    <tr  class = "`+cls+`">
                        <td>${(!responsedata[key][4])?'-':moment(responsedata[key][4]).format('DD-MMM-YY')}</td>
                        <td>${(responsedata[key][7]=="00:00:00")?'-':responsedata[key][5]}</td>
                        <td>${responsedata[key][3]}
                        
                        </td>
                        <td>${responsedata[key][2]}</td>
                        <td>${responsedata[key][5]}</td>
                        <td>${responsedata[key][6]}</td>
                        <td>${responsedata[key][7]}</td>
                        <td>${(responsedata[key][8])?'<a id="location" onclick="showlocation(' + responsedata[key][9] + ',' + responsedata[key][10] + ');"  href="javascript:void(0);"><img width="40px" src="{{url("images/map_pointer.png")}}" ></a>':'-'}</td>
                    </tr>
                `;
            });
            /*
            $.each(data.mobile_security_patrol_fence_data, function(key, gfs) {
                if(gfs.time_entry == null){
                    var cls = 'missed_row';
                }
                else {
                    var cls ='visited_row';
                }
                html += `
                    <tr  class = "`+cls+`">
                        <td>${(!gfs.time_entry)?'-':moment(gfs.time_entry).format('DD-MMM-YY')}</td>
                        <td>${(!gfs.time_entry)?'-':moment(gfs.time_entry).format('h:mm A')}</td>
                        <td>${gfs.shift.shift_payperiod.trashed_user.first_name}
                        ${gfs.shift.shift_payperiod.trashed_user.last_name}
                        </td>
                        <td>${gfs.shift.shift_payperiod.trashed_employee.employee_no}</td>
                        <td>${moment(gfs.shift.start).format('h:mm A')}</td>
                        <td>${moment(gfs.shift.end).format('h:mm A')}</td>
                        <td>${moment.utc(gfs.duration*1000).format('HH:mm:ss')}</td>
                        <td>${(gfs.start_coordinate)?'<a id="location" onclick="showlocation(' + gfs.start_coordinate.latitude + ',' + gfs.start_coordinate.longitude + ');"  href="javascript:void(0);"><img width="40px" src="{{url("images/map_pointer.png")}}" ></a>':'-'}</td>
                    </tr>
                `;
            });
            */
            return `<table  class="table table-geofence-info">
                        <thead>
                            <tr>
                                <th>Visited Date</th>
                                <th>Visited Time</th>
                                <th>Employee Name</th>
                                <th>Employee No</th>
                                <th>Shift Start Time</th>
                                <th>Shift End Time</th>
                                <th>Total Visit Time</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody class="child_elements">${html}</tbody>
                    </table>`;
        },
        onInputChanged() {
            sessionStorage.setItem('sat-start-date', $('#startdate').val());
            sessionStorage.setItem('sat-end-date', $('#enddate').val());
            //on input change fetch the dashboard  datas
            this.fetchSatDashboardData();
            
            //fetch table data
            var success = geoSatTable.ajax.reload(function(){
                
                
            });
        },
        collectInputData() {
            let customerIds = [];
            let selectedCustomerEls = $('.sat-filter-checkbox:checked');
            selectedCustomerEls.each(function(i, item) {
                customerIds.push($(item).data('customerid'));
            });

            return {
                customerIds,
                startDate: $('#startdate').val(),
                endDate: $('#enddate').val()
            };
        },
        fetchSatDashboardData() {
            let root = this;
            let input = this.collectInputData();
               
            currentRequest = $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('msp.geofence.sat-dashboard-map')}}",               
                type: 'GET',
                data: input,
                global: false,
                beforeSend:function(){
                    if(currentRequest != null) {
                         currentRequest.abort();
                    }
                },
                success: function(data) {
                    root.processMapDataAndRender(data);
                    $(".filterbutton").prop("disabled",false);
                    $('body').loading('stop');
                },
                error: function(xhr, textStatus, thrownError) {
                    //swal("Warning", "Something went wrong", "warning");
                    console.log("Warning! Something went wrong! warning");
                    $('body').loading('stop');
                },
            });
        },
        setDashboardCardData() {
            $('#sat-dc-locations').html(dashboardCardData.locations);
            $('#sat-dc-c-visits').html(dashboardCardData.contractualVisits);
            $('#sat-dc-actual-visits').html(dashboardCardData.actualVisits);
            $('#sat-dc-delta').html(dashboardCardData.delta);
        }
    }

    //Sattelite tracking tables
    $(function() {
        $(".filterbutton").prop('disabled','disabled');
        $.fn.dataTable.ext.errMode = 'hide';
        let dateFormat = 'DD-MMM-YY';
        let timeFormat = 'h:mm A';
        $('body').loading({
        stoppable: false,
        message: 'Please wait...'
    });
        try {
            geoSatTable = $('#geosat-table').DataTable({
                bProcessing: false,
                responsive: false,
                dom: 'Blfrtip',
                buttons: [

                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    beforeSend: function (jqXHR, settings) {
                        $('body').loading({
                            stoppable: false,
                            message: 'Please wait...'
                        });
                        //
                        if(tabrequest == 0){
                            tabrequest = 1;
                        }
                        else{
                            geoSatTable.settings()[0].jqXHR.abort();
                          //  jqXHR.abort();
                        }
                    },
                    url: "{{ route('msp.geofence.sat-dashboard-table') }}",
                    complete:function(){
                        tabrequest = 0;
                        $('body').loading('stop');
                        
                        
                    },
                    global: false,
                    data: function(d) {
                        let data = satc.collectInputData();
                        d.customerIds = data.customerIds;
                        d.startDate = $("#startdate").val();
                        d.endDate = $("#enddate").val();
                        return d;
                    },
                    error: function(xhr, textStatus, thrownError) {
                        $('body').loading('stop');
                    }
                },
                columns: [{
                        data: 'id',
                        render: function(o) {
                            if(o.mobile_security_patrol_fence_data.length <= 0){
                                return '';
                            }
                            return '<button  class="btn fa fa-plus-square "></button>';
                        },
                        orderable: false,
                        className: 'details-control',
                        data: null,
                        defaultContent: ''

                    },
                    {
                        data: 'title',
                    },
                    {
                        data: 'address',
                    },
                    {
                        data: 'customer_trashed.client_details',
                    },
                    {
                        data: 'contractual_visit',
                        render: function(o) {
                            return o ? o : '--';
                        },
                    },
                    {
                        data: 'mobile_security_patrol_fence_summaries',
                        render: function(o) {
                          
                            
                            
                            if (o.length > 0) {
                                return o[0].total_visit_count_actual;
                            }
                            return '--';
                        },
                    },
                    {
                        data: 'mobile_security_patrol_fence_summaries',
                        sortable: false,
                        render: function(o, t, r) {
                            
                            if (o.length > 0) {
                                return satc.calculateDelta(
                                    r.contractual_visit,
                                    o[0].total_visit_count_actual
                                );
                            }
                            return '--';
                        }
                    },
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        //show geolocation summary table
        $('#geosat-table tbody').on('click', 'td.details-control', function() {
            
            var tr = $(this).closest('tr');
            var row = geoSatTable.row(tr);
            let btnNode = tr.find('td.details-control');
            var fenceid = row.data().id;
            if (row.child.isShown()) {
                    btnNode.html('<button  class="btn fa fa-plus-square "></button>');
                    row.child.hide(); // This row is already open - close it
                    } else {
                        
                    
            $.ajax({
                type: "get",
                url: "{{route('msp.geofence.sat-dashboard-mapchildrows')}}",
                data: {fenceid:fenceid,startdate:$("#startdate").val(),enddate:$("#enddate").val()},
                success: function (response) {
                    var jqdata = jQuery.parseJSON(response);
                    btnNode.html('<button  class="btn fa fa-minus-square "></button>');
                    row.child(satc.satTrackChildTable(jqdata)).show(); // Open this row
                    
                }
            }).done(function(e){
                var type = $("input[name='list_type']:checked").val();
                if(type == '2') {
                $('.visited_row').show();
                $('.missed_row').hide();
                    }

                else if (type== '3') {
                $('.visited_row').hide();
                $('.missed_row').show();
                        }

            else{
                $('.visited_row').show();
                $('.missed_row').show();
                }
            
                refreshSideMenu();
            });
           }
            
            
        });
    });

    $(document).ready(function() {
        satc.init();
    });

    function initialize(myCenter, radius) {

        var renderContainer = document.getElementById("modal-content");
        var mapProp = {center: myCenter, zoom: 8};
        var map = new google.maps.Map(renderContainer, mapProp,{
            gestureHandling  : 'greedy',
        });

        //Marker in the Map
        var marker = new google.maps.Marker({
            position: myCenter,
            draggable: true,
            //animation: google.maps.Animation.DROP,
        });
        marker.setMap(map);

        //Circle in the Map
        var circle = new google.maps.Circle({
            center: myCenter,
            map: map,
            radius: radius, // IN METERS.
            fillColor: '#FF6600',
            fillOpacity: 0.3,
            strokeColor: "#FFF",
            strokeWeight: 1,
            //draggable: true,
            editable: true
        });
        circle.setMap(map);

        //Add listner to change latlong value on dragging the marker
        marker.addListener('dragend', function (event)
        {
            $('#lat').val(event.latLng.lat());
            $('#long').val(event.latLng.lng());
        });

        //Add event listner on drag event of marker
        marker.addListener('drag', function (event) {
            circle.setOptions({center: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
        });

        //Add listner to change radius value on field
        circle.addListener('radius_changed', function () {
            $('#radius').val(circle.getRadius());
        });

        //Add event listner on drag event of circle
        circle.addListener('drag', function (event) {
            marker.setOptions({position: {lat: event.latLng.lat(), lng: event.latLng.lng()}});
        });

        //changing the radius of circle on changing the numeric field value
        $("#radius").on("change paste keyup keydown", function () {
            //radius = $("#radius").val();
            circle.setRadius(Number($("#radius").val()));

        });
    }
</script>


<!-- Dashboard Scripts -->
<script>
    //sidemenu script
    $(document).ready(function() {
        $("#customerSearch").on("keyup", function() {
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
        //Filter Function for All , Visited, Missed
        $(".visited").prop("checked", true);
        $('#filter').on('change', 'input[name=list_type]', function () {
           
            if ($(this).val() == '2') {       
            $('.visited_row').show();
            $('.missed_row').hide();
            }
            else  if ($(this).val() == '3') {
                $('.visited_row').hide();
                $('.missed_row').show();
            }
            else{
                $('.visited_row').show();
                $('.missed_row').show();
            }
            
                });      

        $('.tab-btn .dropdown-menu a').click(function() {
            var selText = $(this).attr('data-value');
            $('.section-common').hide();
            $('.' + selText).show();
        });
    });

    //dropdown script
    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
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

    function showlocation(lat,long){
        $('#modalContent').modal('show');
        $('#modal-content').show();
        $('#modal-img-content').hide();
        $('#modalLabel').text('Location');

        var radius = 150;
        $('#modalContent').on('shown.bs.modal', function (e) {
            initialize(new google.maps.LatLng(lat, long), radius);
        });
    }
</script>


<style type="text/css">
    .customer-sidebar{
        min-height: 76vh;
    }
    #content-div {
        padding-bottom: 60px;
        margin-top: 5px;
    }

    table td,
    table th {
        text-align: center !important;
    }

    .location_name {
        margin-bottom: 0;
    }

    .table-geofence-info {
        margin-left: 9px;
    }

    #geosat-table thead th {
        color: white;
    }

    .table-geofence-info thead th {
        background: #a27972;
    }

    .table-geofence-info tbody td {
        background: #efdedb;
        border: solid 1px #f1c7bf !important
    }

    .fence-title {
        font-style: italic;
        font-size: 16px;
    }

    .fence-details-section table th {
        background-color: #fdd5c3;
    }

    #customizeWidgetModal .modal-dialog {
        max-width: 500px !important;
    }

    #map {
        width: 100%;
        height: 100%;
        min-height: 76vh;
        cursor: pointer;
    }

    .firstHeading {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    #bodyContent {
        padding: 16px 32px;
    }

    #bodyContent .map-label {
        /* color: #003a63; */
        font-weight: bold;
    }

    #bodyContent .row {
        margin-bottom: .5rem;
    }

    @media screen and (min-width: 992px) {
        .map-tooltip {
            min-width: 500px !important;
        }
    }
    fieldset{
        font-size: 14px;
        position: absolute;
        left: 50%;
        margin-left: -50px;
        transform: translate(-50%, 0%);
        margin-bottom: 0;
        z-index: 999999;
    }
    #filter label:not(:last-child){
        margin-right: .5rem;
    }
    #filter{
        display: flex;
    }
    #filter label{
        display: flex;
        margin-bottom: 0;
    }
    #filter span{
        margin-left: .5rem;
    }
    .fixedsideblock{
       
        padding: 5px;
        z-index: 200
    }
</style>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
