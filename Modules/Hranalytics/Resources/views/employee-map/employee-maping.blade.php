@extends('layouts.app')
@section('content')
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
<div id="supervisor_panel">
    <div class="table_title">

            <h4>Employee Geomapping </h4>
    </div>
    <div id="wrapper" class="toggled siderbar-panel">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <div class="clearfix"></div>
                <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                <div class="second-child"></div>
                <div id="employee-data-left-panel">

                    <table class="ssd-table table">
                        <tbody>
                        @if(isset($list_data))
                            @foreach($list_data as $i=>$employees)
                                <tr>
                                    <td>
                                    <a onmouseover="openInfoWindow({{$i}});" href="{{ route('employee.performance-view',$employees['employee_id']) }}" class="ssd-text foa1 hideHover">{{ ucwords( $employees['full_name']) }}</a>
                                    </td>
                                    @if(isset($employees['rating']))
                                        <td class="js-ssd-pts-{{ $employees['rating'] }}"
                                            style="min-width:43px; text-align:center; font-size: 13px;
                                            background-color: @if(($employees['rating'])<=2)
                                                red; color: white;
                                            @elseif(($employees['rating'])<=3.5)
                                                yellow; color: black;
                                            @elseif(($employees['rating'])<=4.5)
                                                green; color: white;
                                            @else
                                                darkgreen; color: white;
                                            @endif !important;">
                                            {{ number_format(round($employees['rating'],2),2) }}
                                        </td>
                                    @else
                                        <td class="js-ssd-pts-{{$i}}"
                                            style="min-width:43px; text-align:center;background-color: black; color: white; font-size: 13px;">
                                            --
                                        </td>
                                    @endif

                                    <td style="min-width: 43px; text-align:center; padding:0px; vertical-align:middle;">
                                        <div class="filter_checkbox" style="">
                                            <input type="checkbox" name="employeeRating"
                                            id=chk-rating{{$employees['employee_id']}}
                                            value={{($employees['rating']== null)? -1: $employees['rating'] }}
                                            class="largerCheckbox"
                                            data-employee_id={{$i}}
                                            >
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                                <tr class='notfound' style="display:none;">
                                    <td colspan='3'>No guards to list</td>
                                </tr>
                        @else
                        <tr>
                            <td colspan='3'>No guards to list</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>

                </div>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <div class="mapping mapping-ie">
            <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x" aria-hidden="true"></i></a>
        </div>
    </div>

<div id="view-details" class="toggled filter-details" style="display: none">
        <div id="sidebar-view-details" class="hide-vertical-scroll filter-border">
            <h4 class="padding-top-filter">Filter Criteria</h4>
            {{ Form::open(array('url'=>$form_route,'id'=>'filtering-form','method'=>'GET')) }}
             <input type="hidden" id="employee_no" name="employee_no" value="">
            <div class="clearfix"></div>
             <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Employee Type</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('fundamental_role[]',$fundamental_roles,$role,array('class'=>'form-control select2  js-example-basic-multiple','id'=>'select_multiple','multiple'=>'multiple')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left"></label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                   Low
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                   High
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Current Wage</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                     {{ Form::text('wage_low',old('wage_low',$request->get('wage_low')),array('class ' => 'form-control','placeholder'=>'From')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('wage_high',old('wage_high',$request->get('wage_high')),array('class ' => 'form-control','placeholder'=>'To')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Age</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                       {{ Form::text('age_low',old('age_low',$request->get('age_low')),array('class' => 'form-control','placeholder'=>'From')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('age_high',old('age_high',$request->get('age_high')),array('class' => 'form-control','placeholder'=>'To')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Length of Service</label>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                        {{ Form::text('length_low',old('length_low',$request->get('length_low')),array('class' => 'form-control','placeholder'=>'From')) }}
                </div>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                    {{ Form::text('length_high',old('length_high',$request->get('length_high')),array('class' => 'form-control','placeholder'=>'To')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Employee Rating</label>
                <div class="col-sm-4  col-md-4 col-xs-12 float-left">
                        {{ Form::text('rating_low',old('rating_low',$request->get('rating_low')),array('class' => 'form-control','placeholder'=>'From')) }}
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 float-left">
                    {{ Form::text('rating_high',old('rating_high',$request->get('rating_high')),array('class' => 'form-control','placeholder'=>'To')) }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Veteran Status</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('veteran_status',[''=>'Please Select',1=>'Yes',0=>'No'],old('veteran_status',$request->get('veteran_status')),array('class'=>'form-control client-select')) }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Clearance</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                    {{ Form::select('clearance',[''=>'Please Select'] + $security_clearance,old('clearance',$request->get('clearance')),array('class'=>'form-control')) }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Position</label>
                <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                   {{ Form::select('position',[''=>'Please Select'] + $position,old('position',$request->get('position')),array('class'=>'form-control')) }}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="text-center margin-bottom-5">
                <button type="submit" class="btn submit">Filter</button>
                <button type="reset" class="btn submit reset">Reset</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <!-- /#wrapper -->
    <div class="embed-responsive embed-responsive-4by3">
        <div id="map" style="min-height:335px;" class="embed-responsive-item" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>
    </div>
</div>
@php

if(Auth::user()->can('guard'))
{
    $salary_perm='view_salary_in_employee_mapping';
}elseif(Auth::user()->can('supervisor'))
{
    $salary_perm='view_salary_in_supervisor_mapping';
}else{
    $salary_perm='';
}@endphp

@stop
@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script type="text/javascript">
    $(function() {
    $('#select_multiple').select2();
});
    var markers = [];
    var locations = [];

    @isset($list_data)

        lat = Number("{{(!empty($list_data[0]['latitude']))?$list_data[0]['latitude']:''}}");
        long = Number("{{(!empty($list_data[0]['longitude']))?$list_data[0]['longitude']:''}}");
        if(lat != 0 && long != 0){
            var mapCenter = {lat:lat, lng:long };
        }else{
            var mapCenter = getLocationCoordinate("{{(!empty($list_data[0]['postal_code']))?$list_data[0]['postal_code']:0}}");
             mapCenter = (mapCenter === null) ? ({lat:{{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}}) : mapCenter;
        }
        {!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\employee-map\employee-maping')!!}
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: mapCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl   : false,
            panControl       : false,
            gestureHandling: 'greedy',
        });
        var infowindow = new google.maps.InfoWindow();


    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';

        var var_url = "{{ route('employee.performance-view',':id') }}";

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

        var employee = {!! json_encode($list_data) !!};
        $.each(employee, function(i, item) {
            if(item['latitude'] == null || item['latitude'] == '' || item['longitude'] == null || item['longitude'] == ''){
                position = getLocationCoordinate(item['postal_code']);
                if(position != '' && position != null){
                    updateLatLong('emp',"{{route('location.store')}}",item['employee_id'],position);
                }
            }
            else{
                position = {lat: parseFloat(item['latitude']), lng: parseFloat(item['longitude'])};
            }

            url=var_url.replace(':id',item['employee_id']);
            var employee_phone = item['phone'];
            var employee_id = item['employee_no'];
            var employee_name  = camelcase(item['first_name']);
            var employee_last_name = (item['last_name'])?camelcase(item['last_name']):'--';
            var last_name = (item['last_name'])?(item['last_name']):'';
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

            var image_html = '';
            if(item['image'] != null && item['image'] != "") {
                var image = "{{asset('images/uploads/') }}/" + item['image'];
                image_html = '<img name="image" src="'+image+'"  class="profileImage">';
            }else{
                var initial_characters = (employee_name? employee_name.charAt(0): '') + ((last_name != "")? last_name.charAt(0): camelcase(employee_name.charAt((employee_name.length - 1))));
                image_html = '<div class="profileImage" style="background: linear-gradient(to bottom, #F2351F, #F17437);">'+initial_characters+'</div>';
            }

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
                security_clearance_data += '<div class="row"><div class="col-6 p0">Clearance Type</div><div class="col-6 p0 map-disc popup-value">'+(null !=security_clearance[sci]?security_clearance[sci]:'--')+'</div></div>';
                security_clearance_data += '<div class="row"><div class="col-6 p0">Clearance Expiry</div><div class="col-6 p0 map-disc popup-value">'+(null != clearance_expiry[sci]?formatDate(clearance_expiry[sci]):'--')+'</div></div>';
            }
            var icon = "{{ asset('images/markers/green-dot.png') }}";
            //console.log(Number(employee_rating));
            if(employee_rating=='--' || Number(employee_rating)<=2)
            {
                icon = "{{ asset('images/markers/red-dot.png') }}";
            }else if(Number(employee_rating)<=3.5){
                icon = "{{ asset('images/markers/yellow-dot.png') }}";
            }else if(Number(employee_rating)<=4.5){
                icon = "{{ asset('images/markers/green-dot.png') }}";
            }else{
                icon = "{{ asset('images/markers/green-dot.png') }}";
            }
            marker = new google.maps.Marker({
                employeeId: employee_id,
                position: position,
                map: map,
                icon : icon,
                content : '<div id="content" style="min-width:0px;" class="map-tooltip">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;<a style="color:#f26338;" href="'+url+'">'+full_name+'</a></h4>' +
                            '<div id="bodyContent"><label class="col-md-12 col-12 scrollable"><div class="row"><div class="col-7">'
                            +'<div class="row"><div class="col-6 p0">Employee Number</div><div class="col-6 p0 map-disc popup-value">'+employee_id+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">First Name</div><div class="col-6 p0 map-disc popup-value">'+employee_name+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Last Name</div><div class="col-6 p0 map-disc popup-value">'+employee_last_name+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Address</div><div class="col-6 p0 map-disc popup-value">'+employee_full_address+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">City</div><div class="col-6 p0 map-disc popup-value">'+city+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Postal Code</div><div class="col-6 p0 map-disc popup-value">'+postal_code+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Phone Number</div><div class="col-6 p0 map-disc popup-value">'+phone+phone_ext+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Work Email</div><div class="col-6 p0 map-disc popup-value">'+email+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Project Number</div><div class="col-6 p0 map-disc popup-value">'+project_number+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Project Name</div><div class="col-6 p0 map-disc popup-value">'+project_name+'</div></div>'
                            @can($salary_perm) + '<div class="row"><div class="col-6 p0">Current Wage</div><div class="col-6 map-disc popup-value">$'+current_wage+'</div></div>'@endcan
                            @can('view_dob_in_employee_geomapping') +'<div class="row"><div class="col-6 p0">Date of Birth</div><div class="col-6 p0 map-disc popup-value">'+formatDate(date_of_birth)+'</div></div>'@endcan
                            @can('view_age_in_employee_geomapping') +'<div class="row"><div class="col-6 p0">Age</div><div class="col-6 p0 map-disc popup-value">'+age+'</div></div>'@endcan
                            +'<div class="row"><div class="col-6 p0">Start Date</div><div class="col-6 p0 map-disc popup-value">'+formatDate(start_date)+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Length of Service (Year)</div><div class="col-6 p0 map-disc popup-value">'+length_of_service+'</div></div>'
                            @can('view_veteran_status_in_employee_geomapping') +'<div class="row"><div class="col-6 p0">Veteran Status</div><div class="col-6 p0 map-disc popup-value">'+veteran_status+'</div></div>' @endcan
                            @can('view_clearance_type_in_employee_geomapping') + security_clearance_data + @endcan @can('view_employee_rating_in_employee_geomapping')'<div class="row"><div class="col-6 p0">Employee Rating</div><div class="col-6 p0 map-disc popup-value">'+employee_rating+'</div></div>'@endcan
                            +'<div class="row"><div class="col-6 p0">Position</div><div class="col-6 p0 map-disc popup-value">'+positions+'</div></div></div>'
                            +'<div class="col-5 user-image-div">'+image_html+'</div></div></label>' +
                            '</div>' +
                            '</div>'
            });
            locations.push(marker);
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());
                }
            })(marker, i));

        });
    }
   @endisset

    function initEmptyMap(myCenter) {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locations = [];
        var mapProp = {center: myCenter, zoom: 8, mapTypeId: google.maps.MapTypeId.ROADMAP};
        var map = new google.maps.Map(document.getElementById('map'), mapProp);
    }

    function formatDate(date) {
        if(date != '--'){
            var d = new Date(date);
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            var today  = new Date(date);
            return today.toLocaleDateString("en-US", options);
        }else{
            return "--";
        }
    }

    function openInfoWindow(id) {
        google.maps.event.trigger(locations[id], 'click');
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

            if (!($(".filter-details").hasClass("toggled"))) {
                infowindow.close();
            }
        });

        $(".hideHover").mouseover(function(e){
            if (!($(".filter-details").hasClass("toggled"))) {
                infowindow.close();
            }
        });

        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $('#searchbox').keyup(function(){
            var search = $(this).val();
            $('table tbody tr').hide();
            var len = $('table tbody tr:not(.notfound) td:contains("'+search+'")').length;
            if(len > 0){
            $('table tbody tr:not(.notfound) td:contains("'+search+'")').each(function(){
                $(this).closest('tr').show();
            });
            }//else{
            // $('.notfound').show();
            // }
        });
         @if(!empty($request->all()))
        $("#menu-toggle").click();
        $(".search-input").click();
        @endif
        $(".reset").click(function(e) {
            e.preventDefault();
            $(this).closest('form').find("input[type='text']").val("");
            $(this).closest('form').find("select").prop('selectedIndex',0);
            $('#select_multiple').select2().val('');
            $('#select_multiple').select2();

        });
    });

     $(window).bind("load", function() {
    $('#sidebar').css('height', $(window).height()-70);
    $('#content-div').css('height', $(window).height()-70);
    $('#content-div').css('overflow', 'hidden');

    var marker, i, contentString;

    function filterMarkers() {
        for (var key in locations) {
            locations[key].setMap(null);
        }

        markers = [];
        var selectedIds = [];

        $('.largerCheckbox:checkbox:checked').each(function () {
            selectedIds.push($(this).data('employee_id'));
        });

        if (selectedIds.length <= 0) {
            for(var j = 0; j < locations.length; j++) {
                selectedIds.push(j);
            }
        }

                selectedIds.forEach((item,i) => {

                    var marker = new google.maps.Marker({
                        employeeId: locations[item].employeeId,
                        position: locations[item].position,
                        map: map,
                        icon: locations[item].icon,
                        content: locations[item].content
                    });
                    locations.push(marker);
                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            infowindow.setContent(marker.content);
                            infowindow.open(map, marker);
                            map.setCenter(marker.getPosition());
                        }
                    })(marker, i));
                });
            if (selectedIds.length != 0) {
                openInfoWindow(selectedIds[selectedIds.length-1]);
            }
    }

    $('.largerCheckbox').on('change', function () {
        filterMarkers();
        if($(this). is(":checked")){
            openInfoWindow($(this).data('employee_id'));
        }
    });
});
</script>
@stop
