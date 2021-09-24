@extends('layouts.app')
@section('content')
<style>
.centeredOverlay {
    opacity: 0.5;
}
.c-avatar {
  position:relative;
  display:inline-block;
}
.c-avatar__image {
  width:13rem;
  height:13rem;
  object-fit:cover;
  border-radius:100%;
}
.c-avatar__status {
    width: 1rem;
    height: 1rem;
    background: #99CC00;
    border: 0.5px solid white;
    position: absolute;
    top: 7.5%;
    right: 15%;
    border-radius: 100%;
}


    .profileImage {
    font-size: 2.5rem;
    color: #fff;
    text-align: center;
    line-height: 12rem;
}

    .user-image-div {
        text-align: left;
        padding-left: 0px !important;
    }
</style>
<div id="candidate_map">
<div class="table_title">
    <h4>Employee Schedule - Employee Mapping </h4>
</div>
<div id="wrapper" class="toggled siderbar-panel">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
               {{--  <li id="filter-view" style="cursor: pointer;">
                    <a class="float-left width-65 location-top">Filters</a><i class="fa fa-search float-left pt-10" aria-hidden="true"></i>
                </li> --}}
                <div class="clearfix"></div>
                <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                <div class="second-child"></div>
                <div id="candidate-data-left-panel">

                    @foreach($employees as $i=>$employee)
                        <li><a onmouseover="openInfoWindow('{{$i+1}}');" href="#">{{ ucfirst($employee->user->name) }}</a></li>
                    @endforeach
                </div>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
         <div class="mapping mapping-ie">
            <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>
<div class="embed-responsive embed-responsive-4by3">
        <div id="map" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>
    </div>
@stop
@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script type="text/javascript">

    @php
        $salaryPerm = "view_salary_stc_employee_mapping";
    @endphp

    var todayDate = new Date("{{date('Y-m-d')}}");
    var markers = [];
    var distanceUserArray = [];
    var customerPosition = null;
    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locations = [];
        var employees = {!! json_encode($employees) !!};
        var view_url = "{{ route('candidate.view',[':id',':job_id']) }}";
        var mapCenter = null; //default center
        @if($customer!=null)
            position = getLocationCoordinate('{{ $customer->postal_code }}');
            if(position!=null){
                let customerAddress="";
                var address = {!! json_encode($customer->address) !!};
                var city = {!! json_encode($customer->city) !!};
                var postal_code = {!! json_encode($customer->postal_code) !!};
                if(address!=""){
                    customerAddress= address;
                }
                if(city!=""){
                    if(customerAddress.length>0){
                        customerAddress= customerAddress+", "+city;
                    }else{
                        customerAddress= city;
                    }
                }
                if(postal_code!=""){
                    if(customerAddress.length>0){
                        customerAddress= customerAddress+", "+postal_code;
                    }else{
                        customerAddress= postal_code;
                    }
                }
                customerPosition = position;
                mapCenter = position;
                locations.push({title: '{{ $customer->client_name }}', latlng: position,tracking:'customer', info: '<div class="row"  style="flex-wrap: nowrap;width: 100%;"><span class="col-sm-4 col-xs-4 float-left p0">Client Name</span> <span class="float-left p0">{{ $customer->client_name }}</span></div><div class="row"  style="flex-wrap: nowrap;width: 100%;"><span class="col-sm-4 float-left p0">Project Number</span><span class="float-left p0">{{ $customer->project_number }}</span></div><div class="row"  style="flex-wrap: nowrap;width: 100%;"><span class="col-sm-4 float-left p0">Client Address</span> <span class="float-left p0">'+customerAddress+'</span></div>', icon: 'https://maps.google.com/mapfiles/ms/micons/blue-pushpin.png'});
            }
        @endif
        $.each(employees, function(i, item) {
            if(item.employee_postal_code!=null)
            {
                position = getLocationCoordinate(item.employee_postal_code);
                if(position != '' && position != null){
                    updateLatLong('cand',"{{route('location.store')}}",item.id,position);
                }
            }else{
                position = {lat: Number(item.geo_location_lat), lng: Number(item.geo_location_long)};
            }

            let statusColor = '#d21a1a';
            let statusTitle = 'Offline';
            if(item.user!=null && item.user.employee_shift_payperiods !=null) {
                $.each(item.user.employee_shift_payperiods, function(j, shift_pay_period){
                    if(shift_pay_period.available_shift!=null) {
                        if(shift_pay_period.available_shift.live_status_id == 1) {
                            statusColor = '#21a71d';
                            statusTitle = 'Online';
                        }else if(shift_pay_period.available_shift.live_status_id == 2) {
                            statusColor = '#f8b30e';
                            statusTitle = 'Meeting';
                        }
                    }
                });
            }
            if(position!=null && mapCenter==null){
                mapCenter = position;
            }
            if(item.work_type_id == 2)
            {
                icon = 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
            }else{
                icon = null;
            }
            if(item.employee_city !=null)
            {
                var city=item.employee_city;
            }else{
                var city='--';
            }
            if(item.employee_postal_code !=null)
            {
                var postalCode=item.employee_postal_code;
            }else{
                var postalCode='--';
            }
            if(item.employee_address !=null)
            {
                var employeeAddress=item.employee_address;
            }else{
                var employeeAddress='--';
            }
            var phone = (item.phone)?item.phone:'--';
            var phone_ext = (item.phone_ext)?(' x'+item.phone_ext):'';
            if(item.user.security_clearance_user.length>0){
                var security_clearance_length = item.user.security_clearance_user.length;
                var security_clearances = item.user.security_clearance_user;
                var clearance_expiry = [];
                var security_clearance=[];
                var clearance_expiry_colors =[];
                for(sci=0; sci<security_clearance_length; sci++){
                     security_clearance.push(security_clearances[sci]['security_clearance_lookups']['security_clearance']);
                     clearance_expiry.push(moment(security_clearances[sci]['valid_until']).format('MMM D, YYYY'));

                    let expiryDate = new Date(security_clearances[sci]['valid_until']);
                    if(expiryDate < todayDate) {
                        clearance_expiry_colors.push('red');
                    }else{
                        clearance_expiry_colors.push('#34373B');
                    }
                }
            }else{
                var security_clearance_length = 1;
                var security_clearance = [null];
                var clearance_expiry = [null];
                var clearance_expiry_colors =[];
            }

            if((item.user!=null) && (item.user.user_certificate!=null) && (item.user.user_certificate.length>0)){
                var certificateCount = item.user.user_certificate.length;
                var certificateObjects = item.user.user_certificate;
                var certificateName = [];
                var certificateExpiry = [];
                var certificateColors= [];
                for(let ce=0; ce<certificateCount; ce++){
                    if(certificateObjects[ce]['certificate_master'] && certificateObjects[ce]['certificate_master']['certificate_name']) {
                        certificateName.push(certificateObjects[ce]['certificate_master']['certificate_name']+' Expiry');
                        certificateExpiry.push(moment(certificateObjects[ce]['expires_on']).format('MMM D, YYYY'));

                        let expiryDate = new Date(certificateObjects[ce]['expires_on']);
                        if(expiryDate < todayDate) {
                            certificateColors.push('red');
                        }else{
                            certificateColors.push('#34373B');
                        }
                    }
                }
            }else{
                var certificateCount = 0;
                var certificateName = [null];
                var certificateExpiry = [null];
                var certificateColors = [];
            }


            var security_clearance_data = '';
            for(let sci=0; sci<security_clearance_length; sci++){
                if(null !=security_clearance[sci]) {
                    security_clearance_data += '<div class="row"><div class="col-6 p0">Clearance Type</div><div class="col-6 p0 map-disc popup-value">'+(security_clearance[sci])+'</div></div><div class="row"><div class="col-6 p0">Clearance Expiry</div><div class="col-6 p0 map-disc popup-value" style="color:'+(clearance_expiry_colors[sci]?clearance_expiry_colors[sci]:'')+'">'+(null != clearance_expiry[sci]?clearance_expiry[sci]:'--')+'</div></div>';
                }
            }

            var user_certificate_data = "";
            for(let ce=0; ce<certificateCount; ce++){
                if(null !=certificateName[ce]) {
                    user_certificate_data += '<div class="row"><div class="col-6 p0">'+(certificateName[ce])+'</div><div class="col-6 p0 map-disc popup-value" style="color:'+(certificateColors[ce]?certificateColors[ce]:'')+'">'+(null !=certificateExpiry[ce]?certificateExpiry[ce]:'--')+'</div></div>';
                }
            }

            let employee_name = item.user? item.user.first_name : "";
            let last_name = item.user? item.user.last_name: "";
            let image_html = "";
            if(item.image != null && item.image != "") {
                var image = "{{asset('images/uploads/') }}/" + item.image;
                image_html = "<div class='c-avatar'><img class='c-avatar__image profileImage' src='"+image+"' alt=''><span class='c-avatar__status' title='"+statusTitle+"' style='background-color:"+statusColor+";'></span></div>";
            }else{
                var initial_characters = (((employee_name != null) && (employee_name != ""))? employee_name.charAt(0): '') + (((last_name != null) && (last_name != ""))? last_name.charAt(0): ((employee_name != null) && (employee_name != ""))? camelcase(employee_name.charAt((employee_name.length - 1))):'');
                image_html = "<div class='c-avatar'><div class='c-avatar__image profileImage' style='background: linear-gradient(to bottom, #F2351F, #F17437);'>"+initial_characters+"</div><span class='c-avatar__status' title='"+statusTitle+"' style='background-color:"+statusColor+";'></span></div>";
            }

            let reliability_score = '<div class="row"><div class="col-6 p0">Reliability Score</div><div class="col-6 p0 map-disc popup-value">'+((item.user.eventlog_score != null && item.user.eventlog_score.length > 0) ? Math.round(item.user.eventlog_score[0].avg_score): 0)+'%</div></div>';

            locations.push({icon:icon, user: item.user, title: '<a target="_blank" style="color:#f26338;" href="{{ route("management.userViewMore")}}/'+ item.user.id +'">'+item.user.name+'</a>', latlng: position,
            info:'<div class="row"><div class="col-7 popup-listing">'
                +'<div class="row">'
                    +'<div class="col-6 p0">Full Name</div><div class="col-6 p0 map-disc popup-value">'+item.user.name+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Address</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+employeeAddress+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">City</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+city+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Postal Code</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+postalCode+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Email Address</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+item.user.email+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Phone</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+phone+phone_ext+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Cellular Phone</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+(null!=item.cell_no?item.cell_no:'--')+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Security Experience</div>'
                    +'<div class="col-6 p0 map-disc popup-value">'+((null!=item.years_of_security)?item.years_of_security:0)+' Years</div>'
                +'</div>'@can($salaryPerm)
                +'<div class="row">'
                    +'<div class="col-6 p0">Low Wage</div>'
                    +'<div class="col-6 map-disc popup-value">$'+((null!=item.wage_expectations_from)?(Number(item.wage_expectations_from).toFixed(2)):0)+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">High Wage</div>'
                    +'<div class="col-6 p0 map-disc popup-value">$'+((null!=item.wage_expectations_to)?(Number(item.wage_expectations_to).toFixed(2)):0)+'</div>'
                +'</div>'
                +'<div class="row">'
                    +'<div class="col-6 p0">Current Wage</div>'
                    +'<div class="col-6 p0 map-disc popup-value">$'+((null!=item.current_project_wage)?(Number(item.current_project_wage).toFixed(2)):0)+'</div>'
                +'</div>'@endcan
                +reliability_score
                +security_clearance_data+user_certificate_data
                +'</div>'
                +'<div class="col-5 user-image-div">'+image_html+'</div></div>'
            });
         });
         {!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\schedule\schedule-map')!!}
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: mapCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            gestureHandling: 'greedy'
        });


        var infowindow = new google.maps.InfoWindow();
        var marker, i, contentString;
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: locations[i].latlng,
                map: map,
                icon: locations[i].icon
            });
            if(marker.icon==null)
            {
            marker.setIcon('https://maps.google.com/mapfiles/ms/icons/green-dot.png');
            }
            markers.push(marker);
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    //popup content
                    contentString = '<div id="content" style="min-width:0px;" class="map-tooltip schedule-popup-div"> '
                    + ' <h4 id="firstHeading" class="firstHeading firstHeading-left">'+ logo
                    + '&nbsp;' + locations[i].title +'</h4> '
                    + ' <div id="bodyContent"> <label class="col-md-12 col-12 scrollable">'
                    + locations[i].info.replace(/\n/g, "<br />")
                    + '</label></div></div>';
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());

                    //fetch distance details
                    $('.distance_details').remove();
                    let user_id = locations[i].user? locations[i].user.id:'';
                    if(distanceUserArray[user_id] === undefined) {
                        let employeePosition = locations[i].latlng;
                        if((customerPosition!=null) && (employeePosition!=null)) {
                            loadDistanceMatrix(customerPosition, employeePosition, user_id);
                        }
                    }else{
                        $('.popup-listing').append(distanceUserArray[user_id]);
                    }
                }
            })(marker, i));
        }
        google.maps.event.addDomListener(window, 'resize', function() {
            infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());


        });
    }

    function openInfoWindow(id) {
        google.maps.event.trigger(markers[id], 'click');
    }

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
                $('.schedule-popup-div').addClass('centeredOverlay');
            },
            success: function (response) {
                $('body').loading('stop');
                $('.distance_details').remove();
                let resp = response.distance;
                distance_details +='<div class="row distance_details"><div class="col-6 p0">Last Schedule Updated</div><div class="col-6 p0 map-disc popup-value">'+response.last_update_date+'</div></div>';
                distance_details +='<div class="row distance_details"><div class="col-6 p0">Distance to Client Location</div><div class="col-6 p0 map-disc popup-value">'+resp.distance+'</div></div>';
                distance_details +='<div class="row distance_details"><div class="col-6 p0">Driving Time</div><div class="col-6 p0 map-disc popup-value">'+resp.duration+'</div></div>';
                $('.popup-listing').append(distance_details);
                distanceUserArray[user_id] = distance_details;
                $('.schedule-popup-div').removeClass('centeredOverlay');
            },
            error:function() {
                $('.schedule-popup-div').removeClass('centeredOverlay');
            }
        });
    }

    $(function () {
        initMap();
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $("#view-details,.filter-details").css("display", "none");
            $('#sidebar').css('height', $(window).height()-70);
            $('#content-div').css('height', $(window).height()-70);
            $('#content-div').css('overflow', 'hidden');
        });
        $("#filter-view").click(function () {
            $(".filter-details").toggleClass("toggled");
            $(".filter-details").css("display", "block");
        });

        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $('#searchbox').on('keyup',function(){
            search = $(this).val();
            $('#candidate-data-left-panel li').show();
            $('#candidate-data-left-panel li:not(:contains('+search+'))').hide();
        });
$(window).bind("load", function() {
    $('#sidebar').css('height', $(window).height()-70);
    $('#content-div').css('height', $(window).height()-70);
    $('#content-div').css('overflow', 'hidden');
});
    });

</script>

@stop
