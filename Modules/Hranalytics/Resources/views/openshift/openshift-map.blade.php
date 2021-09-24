@extends('layouts.app')
@section('content')
<div id="supervisor_panel">
    <div class="table_title">

            <h4> Geomapping </h4>
    </div>
    <div id="wrapper" class="toggled siderbar-panel">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <div class="clearfix"></div>
                <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                <div class="second-child"></div>
                <div id="employee-data-left-panel">
                    @if(isset($list_data))
                    @foreach($list_data as $i=>$employees)
                    <li>
                        <a onmouseover="openInfoWindow({{$i+1}});" href="#">{{ ucwords( $employees['full_name']) }}</a>
                    </li>
                    @endforeach
                    @else
                    <li>No Guards to List</li>
                    @endif
                </div>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <div class="mapping mapping-ie">
            <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>


    <div class="embed-responsive embed-responsive-4by3">
        <div id="map" style="min-height:335px;" class="embed-responsive-item" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>
    </div>
</div>


@stop
@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script type="text/javascript">

    var markers = [];
    @isset($list_data)
       var customer = {!! json_encode($customer) !!};
       var customerPosition = null;

    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locationsArray = [];
        var var_url = "{{ route('employee.performance-view',':id') }}";
        var infowindow = new google.maps.InfoWindow();
        var marker, i, contentString;
        @if($customer!=null)
            position = getLocationCoordinate('{{ $customer->postal_code }}');
            customerPosition = position;
        @endif
        var head = document.getElementsByTagName('head')[0];
        // Save the original method
        var insertBefore = head.insertBefore;
        // Replace it!
        head.insertBefore = function (newElement, referenceElement) {
            if (newElement.href && newElement.href.indexOf('//fonts.googleapis.com/css?family=Roboto') > -1) {
                console.info('Prevented Roboto from loading!');
                return;
            }
            insertBefore.call(head, newElement, referenceElement);
        };

        mapCenter = {lat: Number(customer.geo_location_lat), lng: Number(customer.geo_location_long)};
        {!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\openshift\openshift-map')!!}
         var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: mapCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl   : false,
            panControl       : false,
            gestureHandling  : "greedy",
        });
          marker = new google.maps.Marker({
                position: mapCenter,
                map: map,
                icon:'https://maps.google.com/mapfiles/ms/micons/blue-pushpin.png',
                /*content:'<div id="content" style="min-width:500px;">' +
                        '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + customer.client_name + '</h4>' +
                        '<div id="bodyContent">' +
                        '<label style="width:100%;"><div class="row"  style="flex-wrap: nowrap;width: 100%;">'+
                        '<span class="col-sm-4 col-xs-4 float-left p0">Client Name</span> <span class="float-left p0">'+customer.client_name+
                        '</span></div><div class="row"  style="flex-wrap: nowrap;width: 100%;">'+
                        '<span class="col-sm-4 float-left p0">Project Number</span><span class="float-left p0">'+customer.project_number+
                        '</span></div><div class="row" style="flex-wrap: nowrap;width: 100%;">'+
                        '<span class="col-sm-4 float-left p0">Client Address</span> <span class="float-left p0">'+customer.address+','+customer.city+','+customer.postal_code+'</span></div></label>' +
                        '</div>' +
                        '</div>',*/
                content: '<div id="content" style="min-width:500px;">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + customer.client_name + '</h4>' +
                            '<div id="bodyContent">' +
                            '<label style="width:100%;"><span class="col-sm-7 col-7 float-left p0 map-label">Client Name</span> <span class="col-sm-5 col-5 float-left p0 map-disc">'+customer.client_name+
                            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Project Number</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+customer.project_number+
                            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Address</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+customer.address+','+customer.city+','+customer.postal_code+
                            '</span></label>' +
                            '</div>' +
                            '</div>',
            });
        markers.push(marker);
        var employee = {!! json_encode($list_data) !!};
        var openshift = {!! json_encode($openshift) !!};
        $.each(employee, function(i, item) {
            
             if(item['latitude'] == null || item['latitude'] == '' || item['longitude'] == null || item['longitude'] == ''){
                position = getLocationCoordinate(item['postal_code']);
            //     if(position != '' && position != null){
            //         updateLatLong('emp',"{{route('location.store')}}",item['employee_id'],position);
            
             }
            // }
            else{
                // position = {lat: parseFloat(openshift[i]['latitude']), lng: parseFloat(openshift[i]['longitude'])};
                 position = {lat: parseFloat(item['latitude']), lng: parseFloat(item['longitude'])};
                 locationsArray[item.employee_no]={"position":position,"employee_id":item['employee_id']};
                //  locationsArray[item.employee_no]["position"]=position;
                //  locationsArray[item.employee_no]["employee_id"]=item['employee_id'];
                
           }
            url=var_url.replace(':id',item['employee_id']);
            var employee_phone = item['phone'];
            var employee_id = item['employee_no'];
            var employee_name  = camelcase(item['first_name']);
            var employee_last_name = (item['last_name'])?camelcase(item['last_name']):'--';
            var last_name = (item['last_name'])?(item['last_name']):' ';
            var full_name=camelcase(item['first_name'])+' '+last_name;
            var employee_full_address =(item['address'])? camelcase(item['address']):'--';
            var city = (item['city'])?camelcase(item['city']):'--';
            var postal_code = (item['postal_code'])?item['postal_code']:'--';
            var phone = (item['phone_number'])?item['phone_number']:'--';
            var phone_ext = (item['phone_ext'])?(' x'+item['phone_ext']):'';
            var email = (item['work_email'])?item['work_email']:'--';
            var veteran_status = (item['veteran_status']==1)?'Yes': (item['veteran_status']==0) ? 'No':'--';
            var date_of_birth = (item['date_of_birth'])?(item['date_of_birth']):'--';
            var employee_rating = (item['rating'])?(item['rating']):'--';
            var project_number= item["project_number"];
            var project_name= item["project_name"] ;
            var start_date= (item["start_date"])? (item["start_date"]):'--';
            var length_of_service=item['length_of_service'];
            var age=item["age"];
            var current_wage=(item['current_wage'])?(Number(item['current_wage']).toFixed(2)):'0';
            var positions=(item['position'])?item['position']:'--';

            if(item['security_clearance'] != '--'){
                var security_clearance_length = item['security_clearance'].length;
                var security_clearance = item['security_clearance'];
                var clearance_expiry = item['clearance_expiry'];
            }else{
                var security_clearance_length = 1;
                var security_clearance = [null];
                var clearance_expiry = [null];
            }

            var security_clearance_data = '';
            for(sci=0; sci<security_clearance_length; sci++){
                security_clearance_data += '<span class="col-sm-7 col-7 float-left p0 map-label">Clearance Type</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+(null !=security_clearance[sci]?security_clearance[sci]:'--')+'</span><span class="col-sm-7 col-7 float-left p0 map-label">Clearance Expiry</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+(null != clearance_expiry[sci]?clearance_expiry[sci]:'--')+'</span>';
            }
            var icon = "{{ asset('images/markers/green-dot.png') }}";
            //console.log(Number(employee_rating));
            marker = new google.maps.Marker({
                position: position,
                map: map,
                icon : icon,
                content : '<div id="content" style="min-width:0px;" class="map-tooltip">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;<a style="color:#f26338;" href="#">'+full_name+'</a></h4>' +
                            '<div id="bodyContent">' +
                            '<label style="width:100%;"><span class="col-sm-7 col-7 float-left p0 map-label">Employee Number</span> <span class="col-sm-5 col-5 float-left p0 map-disc employee_id">'+employee_id+
            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">First Name</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+employee_name+
            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Last Name</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+employee_last_name+
            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Address</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+employee_full_address+
                    '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">City</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+city+
            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Postal Code</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+postal_code+
            '</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left p0 map-label">Phone Number</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+phone+phone_ext+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Work Email</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+email+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Project Number</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+project_number+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Project Name</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+project_name+
           '</span><span class="col-sm-7 col-7 float-left p0 map-label">Current Wage</span><span class="col-sm-5 col-5 float-left p0 map-disc">$'+current_wage+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Date of Birth</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+date_of_birth+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Age</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+age+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Start Date</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+start_date+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Length of Service (Year)</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+length_of_service+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Veteran Status</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+veteran_status+'</span>'+
            security_clearance_data+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label">Employee Rating</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+employee_rating+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label positionspan">Position</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+positions+
            '</span><span class="col-sm-7 col-7 float-left p0 map-label positionspan">Distance to Client Location</span><span class="col-sm-5 col-5 float-left p0 map-disc clientlocdist">-</span><span class="col-sm-7 col-7 float-left p0 map-label positionspan">Driving Time</span><span class="col-sm-5 col-5 float-left p0 map-disc drivetime" id="drivetime">-</span></label>' +
                            '</div>' +
                            '</div>'
            });
            markers.push(marker);
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                $(".clientlocdist").html("-")
                $(".drivetime").html("-")
                return function () {
                    let markerContent=marker.content;
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());

                   
                    setTimeout(() => {
                        let employee_no = $(".employee_id").html();
                        let employee_location=locationsArray[employee_no]["position"]
                        let employee_id=locationsArray[employee_no]["employee_id"]
                        if((customerPosition!=null) && (employee_location!=null)) {
                        loadDistanceMatrix(customerPosition, employee_location, employee_id)
                        }
                        

                    }, 1000);
                }
            })(marker, i));
            });

        $.each(markers, function(i, marker) {
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());
                }
            })(marker, i));
        });

        google.maps.event.addDomListener(window, 'resize', function() {
            infowindow.open(map);
        });
    }
   @endisset

    function initEmptyMap(myCenter) {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locations = [];
        var mapProp = {center: myCenter, zoom: 8, mapTypeId: google.maps.MapTypeId.ROADMAP};
        var map = new google.maps.Map(document.getElementById('map'), mapProp);
    }

    function openInfoWindow(id) {
        google.maps.event.trigger(markers[id], 'click');
    }
    $(function () {
        //Reloading to display updated average after employee has rated and pressing back button to view map
        if(!!window.performance && window.performance.navigation.type === 2)
             // value 2 means "The page was accessed by navigating into the history"
        {
            window.location.reload();
        }
        @if(isset($list_data))
        initMap();
        @else
        initEmptyMap(new google.maps.LatLng('43.6532', '-79.3832'));
        @endif
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $("#view-details,.filter-details").css("display", "none");
        });
        $(".search-input").click(function () {
            $(".filter-details").toggleClass("toggled");
            $(".filter-details").css("display", "block");
        });

        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $('#searchbox').on('keyup',function(){
            search = $(this).val();
            $('#employee-data-left-panel li').show();
            $('#employee-data-left-panel li:not(:contains('+search+'))').hide();
        });

        $(".reset").click(function(e) {
            e.preventDefault();
            $(this).closest('form').find("input[type='text']").val("");
            $(this).closest('form').find("select").prop('selectedIndex',0);
        });
    });

     $(window).bind("load", function() {
    $('#sidebar').css('height', $(window).height()-70);
    $('#content-div').css('height', $(window).height()-70);
    $('#content-div').css('overflow', 'hidden');
});

function loadDistanceMatrix(customerPosition, employeePosition, user_id) {
        let distance_details = "";
        $.ajax({
            type: "GET",
            url: "{{ route('candidate.distance_travel_time_by_coordinates') }}",
            global: false,
            data: {
                customer_coordinates:customerPosition,
                employee_coordinates:employeePosition,
                user_id:user_id
            },
            beforeSend: function() {
                // $('.schedule-popup-div').addClass('centeredOverlay');
            },
            success: function (response) {
                // let parseData = jQuery.parseJSON(response)
                $(".clientlocdist").html(response.distance.distance)
                $(".drivetime").html(response.distance.duration)
                
                // $('body').loading('stop');
                // $('.distance_details').remove();
                // let resp = response.distance;
                // distance_details +='<div class="row distance_details"><div class="col-6 p0">Last Schedule Updated</div><div class="col-6 p0 map-disc popup-value">'+response.last_update_date+'</div></div>';
                // distance_details +='<div class="row distance_details"><div class="col-6 p0">Distance to Client Location</div><div class="col-6 p0 map-disc popup-value">'+resp.distance+'</div></div>';
                // distance_details +='<div class="row distance_details"><div class="col-6 p0">Driving Time</div><div class="col-6 p0 map-disc popup-value">'+resp.duration+'</div></div>';
                // $('.popup-listing').append(distance_details);
                // distanceUserArray[user_id] = distance_details;
                // $('.schedule-popup-div').removeClass('centeredOverlay');
            },
            error:function() {
                // $('.schedule-popup-div').removeClass('centeredOverlay');
            }
        });
    }
</script>
@stop
