@extends('layouts.app')
<style>

    .profileImage {
        width: 16rem;
        height: 16rem;
        border-radius: 50%;
        font-size: 2.5rem;
        color: #fff;
        text-align: center;
        line-height: 16rem;
        margin: 3rem 0;
    }

    .user-image-div {
        text-align: left;
        padding-left: 0px !important;
    }
    #candidate-data-left-panel li:last-child {
        margin-bottom: 17px;
    }
    .sidebar-nav{
        width: 99%;
    }
    .clip-td{
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .ssd-table td {
        padding: 0.45rem 0.20rem;
        border: 1px solid #003A63;
        text-align: left;
        vertical-align: middle;
    }
    table tr:first-child td {
        border-top: 1px solid #E2E2E7;
    }
    table tr td:first-child {
        border-left: 1px solid #E2E2E7;
    }
    table tr:last-child td {
        border-bottom: 1px solid #E2E2E7;
    }
    table tr td:last-child {
        border-right: 1px solid #E2E2E7;
    }
    .ssd-text{
        display: block;
        font-weight: bold;
        color: #00395c;
        text-indent: 0;
        line-height: 20px;
        font-size: 13px;
    }
    .ssd-text:hover{
        background: rgba(255, 255, 255, 0.2);
        color: #F48452;
        text-decoration: none;
        text-indent: 0;
    }
    .pts-txt{
        font-size: 13px;
    }
    .filter_checkbox {
        vertical-align: middle;
    }
    input.largerCheckbox{
        margin-top: 5px;
        width: 18px !important;
        height: 17px !important;
    }
    .ssd-table{
        border-collapse: collapse;
    }
    .ssd-cb{
        color: white !important;
    }
    .padding-top-filter
    {
        padding-top: 10px;
    }
</style>
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
    @isset($latitude)
    
    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locationsArray = [];
        var var_url = "{{ route('employee.performance-view',':id') }}";
        var infowindow = new google.maps.InfoWindow();
        var marker, i, contentString;
        let userDetail= {!! json_encode($userDetail) !!};
        let customer= {!! json_encode($customer) !!};
        console.log(userDetail);
        let employeeName= {!! json_encode($employeeName) !!};
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
        let latitude= {!! json_encode($latitude) !!};
        let longitude= {!! json_encode($longitude) !!};
        var icon = "{{ asset('images/markers/green-dot.png') }}";

        var image_html = '';
        if(userDetail.image != null && userDetail.image != "") {
            var image = "{{asset('images/uploads/') }}/" + userDetail.image;
            image_html = '<img name="image" src="'+image+'"  class="profileImage">';
        }else{
            var initial_characters = (userDetail.first_name? userDetail.first_name.charAt(0): '') + ((userDetail.last_name != "")? userDetail.last_name.charAt(0): camelcase(userDetail.first_name.charAt((userDetail.first_name.length - 1))));
            image_html = '<div class="profileImage" style="background: linear-gradient(to bottom, #F2351F, #F17437);">'+initial_characters+'</div>';
        }
        mapCenter = {lat: Number(latitude), lng: Number(longitude)};
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
                icon : icon,
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
                content: `<div id="content" style="min-width:0px;" class="map-tooltip">
                            <h4 id="firstHeading" class="firstHeading firstHeading-left"> ${logo}&nbsp;<a style="color:#f26338;" href="url">${employeeName}</a></h4>
                            <div id="bodyContent"><label class="col-md-12 col-12 scrollable"><div class="row"><div class="col-7">
                            <div class="row"><div class="col-6 p0">Employee Number</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.employee_no}</div></div>
                            <div class="row"><div class="col-6 p0">First Name</div><div class="col-6 p0 map-disc popup-value">${userDetail.first_name}</div></div>
                            <div class="row"><div class="col-6 p0">Last Name</div><div class="col-6 p0 map-disc popup-value">${userDetail.last_name}</div></div>
                            <div class="row"><div class="col-6 p0">Address</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.employee_address}</div></div>
                            <div class="row"><div class="col-6 p0">City</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.employee_city}</div></div>
                            <div class="row"><div class="col-6 p0">Postal Code</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.employee_postal_code}</div></div>
                            <div class="row"><div class="col-6 p0">Phone Number</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.phone}+${userDetail.employee.phone_ext}</div></div>
                            <div class="row"><div class="col-6 p0">Work Email</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.employee_work_email}</div></div>
                            <div class="row"><div class="col-6 p0">Project Number</div><div class="col-6 p0 map-disc popup-value">${customer.project_number}</div></div>
                            <div class="row"><div class="col-6 p0">Project Name</div><div class="col-6 p0 map-disc popup-value">${customer.client_name}</div></div>

                            <div class="row"><div class="col-6 p0">Position</div><div class="col-6 p0 map-disc popup-value">${userDetail.employee.employee_position.position}</div></div></div>
                            <div class="col-5 user-image-div">${image_html}</div></div></label>
                            </div>
                            </div>`,
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

                   
                    
                }
            })(marker, i));
        
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
        @if(isset($latitude))
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


</script>
@stop
