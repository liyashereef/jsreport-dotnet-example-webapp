@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" />
<style>
.col-12 {
    position: inherit !important;
}
    .padding-l-r-0 {
        padding-left:0px !important;
        padding-right:0px !important;
    }

.c-avatar {
  position:relative;
  display:inline-block;
}
.c-avatar__image {
  width:14.8rem;
  height:14.8rem;
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
    line-height: 14.8rem;
}

    .user-image-div {
        text-align: left;
        padding-left: 0px !important;
    }

    .color-red {
        color: red;
    }

    .color-green {
        color: green;
    }

    .color-yellow {
        color: #ffc107;
    }

    .bg-color-red, .bg-color-red>a {
        background: red !important;
        color: white !important;
    }

    .bg-color-green, .bg-color-green>a {
        background: green !important;
        color: white !important;
    }

    .bg-color-yellow, .bg-color-yellow>a {
        background: #ffc107 !important;
        color: black !important;
    }

    .btn-li-color {
        color: #fff;
        background: #003A63;
    }

    .btn-li-color.active {
        border-color: #003A63;
        border-width: medium;
    }

    #content label {
        padding-left: 3px !important;;
    }

    #content-div {
        width: 97%;
    }
    .admin-container{
        padding: 0% !important;
    }

    .display-none{
        display:none;
    }

    .slingle-line{
         white-space: nowrap;
    }
    .record-center{
        text-align: center;
    }

    #tableHead tr th{
        background-color: #f36905;
        color: #fff !important;
    }
    .week-day-headding{
        background-color: #003A63 !important;
        color: #fff !important;
    }
    .week-day-data {
        background-color: lightyellow !important;
    }
    #tableHead tr th, #tableBody tr td {
        border : 1px solid #524c4c6e !important;
    }

    #tableHead th.rotate > span{
        transform: rotate(180deg);
        -webkit-transform: rotate(-180deg);
        -moz-transform: rotate(-180deg);
        writing-mode: vertical-rl;
        width: 25px;
        vertical-align:top;
    }


    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f26321;
        color: #fff !important;
    }

    div.dataTables_wrapper {
        width: 100% !important;
        margin: 0 auto;
    }
    .scrollX{
        overflow-x: scroll;
    }
    .js-chart-area{
        height: 500px !important;
    }

    .filter-section {
        margin-top: 20px;
    }

    #sidebar-view-details {
        left: 780px !important;
    }

    .stc-geomapping::-webkit-scrollbar {
        width: 12px;
        height: 5px;
    }

    .stc-geomapping::-webkit-scrollbar-thumb {
        box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }
</style>
@stop
@section('content')

    <!-- IDS Scheduling Report Form - Start -->
        <div class="table_title">
            <h4>STC Geomapping</h4>
        </div>

        <div id="wrapper" class="siderbar-panel" style="height: 90%;">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <div class="clearfix"></div>
                    <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                    <div class="second-child"></div>
                    <div id="candidate-site-list">
                        @if(count($customers)>0)
                            @foreach($customers as $i=>$customer)
                            <li class="customer-list {{'customer_li_'.$i}}" data-id="{{$i}}" style="display: none;">
                                <i class="fa fa-map-marker float-right location-arrow" aria-hidden="true"></i>
                                <a onclick="openInfoWindow({{$i}});" style="cursor: pointer;">{{ ucfirst($customer) }}</a>
                            </li>
                            @endforeach
                        @else
                        <li> No records found </li>
                        @endif
                    </div>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <div class="mapping mapping-ie">
                <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x" aria-hidden="true"></i></a>
            </div>
        </div>

        <div id="view-details" class="toggled filter-details" style="display: none">
            <div id="sidebar-view-details" class="hide-vertical-scroll" style="top: 6% !important;">
                <h4 class="padding-top-20">Filter Criteria</h4>
                {{ Form::open(array('id'=>'stc-geo-mapping-form', 'class'=>'form-horizontal filter-section', 'method'=> 'POST')) }}
                    <div id="start_date" class="form-group row col-sm-12">
                        <label for="start_date" class="col-sm-4 col-form-label">Start & End Date<span class="mandatory"> *</span></label>
                        <div class="col-sm-4">
                            {{ Form::text('start_date', date("Y-m-d"), array('class'=>'form-control datepicker','placeholder'=>'Start Date', 'id'=>'report_start_date')) }}
                            <div class="form-control-feedback" id="startDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <div class="col-sm-4">
                        {{ Form::text('end_date', date("Y-m-d"), array('class'=>'form-control datepicker','placeholder'=>'End Date', 'id'=>'report_end_date')) }}
                            <div class="form-control-feedback" id="endDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="start_time" class="form-group row col-sm-12">
                        <label for="start_time" class="col-sm-4 col-form-label">Start & End Time<span class="mandatory"> *</span></label>
                        <div class="col-sm-4">
                            {{ Form::text('start_time', null, array('class'=>'form-control timepicker','placeholder'=>'Start Time', 'id'=>'report_start_time')) }}
                            <div class="form-control-feedback" id="startTimeError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <div class="col-sm-4">
                        {{ Form::text('end_time', null, array('class'=>'form-control timepicker','placeholder'=>'End Time', 'id'=>'report_end_time')) }}
                            <div class="form-control-feedback" id="endTimeError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="wage_from" class="form-group row col-sm-12">
                        <label for="wage_from" class="col-sm-4 col-form-label">Wage From & To</label>
                        <div class="col-sm-4">
                            {{ Form::number('wage_from', null, array('class'=>'form-control','placeholder'=>'Wage From', 'id'=>'report_wage_from', 'min' => 0)) }}
                            <div class="form-control-feedback" id="wageFromError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                        <div class="col-sm-4">
                        {{ Form::number('wage_to', null, array('class'=>'form-control','placeholder'=>'Wage To', 'id'=>'report_wage_to', 'min' => 0)) }}
                            <div class="form-control-feedback" id="wageToError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="clearance" class="form-group row col-sm-12">
                        <label for="clearance" class="col-sm-4 col-form-label">Security Clearance</label>
                        <div class="col-sm-8">
                            {{ Form::select('clearance[]',$securityClearance, old('clearance'),array('class'=> 'form-control select2','id'=>'report_security_clearance','multiple'=>"multiple")) }}
                            <div class="form-control-feedback" id="clearanceError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="assignment_type" class="form-group row col-sm-12">
                        <label for="assignment_type" class="col-sm-4 col-form-label">Assignment Type</label>
                        <div class="col-sm-8">
                            {{ Form::select('assignment_type[]',$assignmentTypes, old('assignment_type'),array('class'=> 'form-control select2','id'=>'report_assignment_type','multiple'=>"multiple")) }}
                            <div class="form-control-feedback" id="clearanceError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="customer_type" class="form-group row col-sm-12">
                        <label for="customer_type" class="col-sm-4 col-form-label">Customer Type</label>
                        <div class="col-sm-8">
                            {{ Form::select('customer_type[]',$customerTypes, old('customer_type'),array('class'=> 'form-control','id'=>'report_customer_type')) }}
                            <div class="form-control-feedback" id="clearanceError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div id="city" class="form-group row col-sm-12">
                        <label for="city" class="col-sm-4 col-form-label">City</label>
                        <div class="col-sm-8">
                            {{ Form::select('city[]',$cityList, old('city'),array('class'=> 'form-control select2','id'=>'report_city','multiple'=>"multiple")) }}
                            <div class="form-control-feedback" id="cityError"><span class="help-block text-danger align-middle font-12"></span></div>
                        </div>
                    </div>

                    <div class="form-group row col-sm-12 justify-content-center" style="text-align:center">
                        <div class="col-sm-12">
                            {{ Form::submit('Filter', array('class'=>'button btn btn-primary blue','style'=>'margin-left: 30%;'))}}
                            {{ Form::reset('Reset', array('class'=>'btn cancel reset',))}}
                        </div>
                    </div>
                    {{ Form::close() }}
            </div>
        </div>

        <div id="map_div" class="embed-responsive embed-responsive-4by3" style="display:none;height: 96%;">
            <div id="map" style="min-height:335px;" class="embed-responsive-item" ></div>
        </div>

    @stop
    @section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>

<script>
            let items = [];
            let empColorArray = {};
            let subTabColorArray = {};
    $('.select2').select2({
        'allowClear': true,
        placeholder: ""
    });

    $('.timepicker').timepicker({
        timeFormat: 'h:i A',
        step: 15,
    });

    $('#report_start_time').timepicker('setTime', '08:00 AM');
    $('#report_end_time').timepicker('setTime', '03:59 PM');

    var markerArray = [];
    const GeoMap = {
        ref: {
            reportStartTime : null,
            reportEndTime : null,
            reportStartDate : null,
            reportEndDate : null,
            reportWageFrom :null,
            reportWageTo :null,
            reportSecurityClearance :null,
            reportCity :null,
            reportCustomerType:null,
            reportAssignmentType:null
        },
        init() {
            //Event listeners
            this.registerEventListeners();
        },
        registerEventListeners() {
            let root = this;
            //Trend report filter
            $('#stc-geo-mapping-form').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = $(this).serializeArray();
                var trigerFunction = true;
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                //Start date validation
                if($('#report_start_time').val() == ''){
                    form.find("[id='startTimeError']").addClass('has-error').find('.help-block').text("Start time is required");
                    trigerFunction = false;
                }

                if($('#report_end_time').val() == ''){
                    form.find("[id='endTimeError']").addClass('has-error').find('.help-block').text("End time is required");
                    trigerFunction = false;
                }

                if($('#report_start_date').val() == ''){
                    form.find("[id='startDateError']").addClass('has-error').find('.help-block').text("Start date is required");
                    trigerFunction = false;
                }

                if($('#report_end_date').val() == ''){
                    form.find("[id='endDateError']").addClass('has-error').find('.help-block').text("End date is required");
                    trigerFunction = false;
                }

                //Fetch Trend report data
                if(trigerFunction == true){
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    root.ref.reportStartTime = $('#report_start_time').val();
                    root.ref.reportEndTime = $('#report_end_time').val();
                    root.ref.reportStartDate = $('#report_start_date').val();
                    root.ref.reportEndDate = $('#report_end_date').val();
                    root.ref.reportWageFrom = $('#report_wage_from').val();
                    root.ref.reportWageTo = $('#report_wage_to').val();
                    root.ref.reportSecurityClearance = $('#report_security_clearance').val();
                    root.ref.reportCity = $('#report_city').val();
                    root.ref.reportAssignmentType = $('#report_assignment_type').val();
                    root.ref.reportCustomerType = $('#report_customer_type').val();
                    root.fetchStcGeoMapDataEvent();
                }
            });
        },
        fetchStcGeoMapDataEvent(){
            let root = this;
            let url = '{{ route("stc-schedule.geo-mapping-details") }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'start_time':root.ref.reportStartTime,
                    'end_time':root.ref.reportEndTime,
                    'start_date':root.ref.reportStartDate,
                    'end_date':root.ref.reportEndDate,
                    'wage_from':root.ref.reportWageFrom,
                    'wage_to':root.ref.reportWageTo,
                    'security_clearance':root.ref.reportSecurityClearance,
                    'city':root.ref.reportCity,
                    'assignment_type' : root.ref.reportAssignmentType,
                    'customer_type' : root.ref.reportCustomerType
                },
                type: 'GET',
                success: function(data) {
                  root.loadMap(data);
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                    }
                },
                contentType: false
            });
       },
       loadMap(sites) {
        let root = this;
        items = [];

        $('.ids_candidates').hide();
        $('#map_div').css('display','block');
        let logo = '<img src="{{ asset("images/short_logo.png") }}">';
        {!!\App\Services\HelperService::googleAPILog('map','Modules\Supervisorpanel\Resources\views\stc-schedule-geo-mapping')!!}
        let map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: {lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}},
            streetViewControl: false,
            mapTypeControl   : false,
            panControl       : false,
            gestureHandling  : 'greedy',
        });

        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        mapCenter = "{lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}}";
        let infowindow = new google.maps.InfoWindow({
            zoom: 7,
            center: mapCenter,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl   : false,
            panControl       : false,
            gestureHandling  : "greedy",
            maxWidth: 800,
        });

        let location_mapper_array = [];

        $('.customer-list').hide();
        if(sites) {
            let markers = [];
            $.each(sites, function(i, site) {
                $('.customer_li_' + site.customer.id).show();

                let coordinates = '';
                if((site.customer.geo_location_lat != null && site.customer.geo_location_long != null)) {
                    coordinates = { lat: parseFloat(site.customer.geo_location_lat), lng: parseFloat(site.customer.geo_location_long) };
                }else if(site.customer.postal_code != null) {
                    coordinates = getLocationCoordinate(site.customer.postal_code);
                }

                let marker =  new google.maps.Marker({
                    map: map,
                    position: coordinates,
                    icon: root.pinSymbol(site.color),
                    id : site.customer.id,
                });

                markerArray[site.customer.id] = marker;
                items.push(site.customer.id);

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        subTabColorArray = {};
                        empColorArray = {};
                        if (infowindow) infowindow.close();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '{{ route("stc-schedule.geo-mapping-by-customer") }}',
                            type: 'GET',
                            data: "customerId=" + marker.id + '&start_time='+ $('#report_start_time').val() + '&end_time='+ $('#report_end_time').val() +'&start_date='+ $('#report_start_date').val() + '&end_date='+ $('#report_end_date').val()+'&wage_from='+ $('#report_wage_from').val() + '&wage_to='+ $('#report_wage_to').val() +'&security_clearance=' + $('#report_security_clearance').val()+'&city='+$('#report_city').val(),
                            success: function (resp) {
                                if (resp) {
                                    let liClass= '';
                                    let contentClass = '';
                                    let listItemsHtml= '';
                                    let listItemsHeaderHtml= '';
                                    let tabContent = '';
                                    let css_collapse_header = '';
                                    let collapse_css = '';
                                    let popupHtml = '<div id="content" style="min-width:0px;" class="map-tooltip stc-geomapping"><div id="bodyContent" style="min-width:600px;min-height:250px;"><label class="col-md-12 col-12 scrollable"><div class="col-12"><ul class="col-12 nav nav-pills" style="flex-wrap:initial;overflow-x:auto;overflow-y:hidden;padding-right:0px;">';
                                    let ind = 0;
                                    $.each(resp, function(i, item) {
                                        liClass = (ind == 0)? 'active':'';
                                        contentClass = (ind == 0)? 'active':'fade';
                                        var image_html = '';
                                        if(item['image'] != null && item['image'] != "") {
                                            var image = "{{asset('images/uploads/') }}/" + item['image'];
                                            image_html = '<div class="c-avatar"><img name="image" src="'+image+'"  class="c-avatar__image profileImage"></div>';
                                        }else{
                                            var initial_characters = ((item['first_name'] != "" && item['first_name'] != null)? item['first_name'].charAt(0): '') + ((item['last_name'] != "" && item['last_name'] != null)? item['last_name'].charAt(0): (item['first_name'] != "" && item['last_name'] != null)? camelcase(item['first_name'].charAt((item['first_name'].length - 1))):'');
                                            image_html = '<div class="c-avatar"><div class="c-avatar__image profileImage" style="background: linear-gradient(to bottom, #F2351F, #F17437);">'+initial_characters+'</div></div>';
                                        }

                                        listItemsHtml += '<li class="list_item_'+item['id']+' col-3 btn btn-li-color '+liClass+'" onclick="changeListItemColor('+item['id']+');" style="pointer-events: auto;"><a data-toggle="pill" href="#customer_'+item['id']+'" style="color:white;pointer-events: auto;font-size:14px;">'+ item['first_name'] + '</a></li>';
                                        listItemsHeaderHtml ='</ul></div><div class="tab-content">';
                                        tabContent +='<div id="customer_'+item['id']+'" class="tab-pane '+contentClass+' tab-content-custom" style="padding-top:2%;padding-bottom:2%;">';

                                        $.each(item['details'], function(k, itemDetail) {
                                            css_collapse_header = (k == 0)? '':'collapsed';
                                            collapse_css = '';
                                            if(item['details'].length > 1) {
                                                collapse_css = (k == 0)? 'collapse show':'collapse';
                                                tabContent +='<div class="col-12" style="padding-right:0px;"><div class="col-12" style="padding-right:0px;"><a class="col-12 row card-link '+i+'_'+k+'_parent" data-toggle="collapse" style="background:#f36a27;color:white;border-bottom:solid white;font-size:14px;font-weight:bold;" href="#'+i+'_'+k+'">'+moment(itemDetail['shift_from']).format('MMM D, YYYY hh:mm A')+' - '+moment(itemDetail['shift_to']).format('MMM D, YYYY hh:mm A')+'</a></div></div>';
                                            }

                                            tabContent +='<div class="col-12 employee_wise_tail '+collapse_css+'" id="'+i+'_'+k+'" data-parent="#accordion"><div class="col-7 padding-l-r-0" style="float:left;">';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Project Number</div><div class="col-8 p0 map-disc popup-value">'+itemDetail['project_number']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Project Name</div><div class="col-8 p0 map-disc popup-value">'+itemDetail['client_name']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Start Time</div><div class="col-8 p0 map-disc popup-value">'+moment(itemDetail['shift_from']).format('MMM D, YYYY hh:mm A')+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">End Time</div><div class="col-8 p0 map-disc popup-value">'+moment(itemDetail['shift_to']).format('MMM D, YYYY hh:mm A')+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Employee ID</div><div class="col-8 p0 map-disc popup-value">'+item['employee_no']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Employee</div><div class="col-8 p0 map-disc popup-value">'+item['full_name']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Sign In</div><div class="col-8 p0 map-disc popup-value '+itemDetail['sign_in_color']+'">'+((itemDetail['sign_in'] != null)? moment(itemDetail['sign_in']).format('MMM D, YYYY hh:mm A') : '-')+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Sign Out</div><div class="col-8 p0 map-disc popup-value '+itemDetail['sign_out_color']+'">'+((itemDetail['sign_out'] != null)? moment(itemDetail['sign_out']).format('MMM D, YYYY hh:mm A'): '-')+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Score</div><div class="col-8 p0 map-disc popup-value">'+item['score']+'%</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Phone Number</div><div class="col-8 p0 map-disc popup-value">'+item['phone']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Email</div><div class="col-8 p0 map-disc popup-value">'+item['email']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Site Rate</div><div class="col-8 p0 map-disc popup-value">'+itemDetail['site_rate']+'</div></div>';
                                            tabContent += '<div class="col-12 row"><div class="col-4 p0">Accepted Rate</div><div class="col-8 p0 map-disc popup-value">'+itemDetail['accepted_rate']+'</div></div>';
                                            @can('candidate-schedule')
                                            let re_schedule_url ='{{ route("candidate.schedule",[":customer_id",":requirement_id",":customer_contract_type",":security_clearence_id"]) }}'
                                            re_schedule_url = re_schedule_url.replace(':customer_id', itemDetail['customer_id']);
                                            re_schedule_url = re_schedule_url.replace(':requirement_id', itemDetail['schedule_customer_requirement_id']);
                                            re_schedule_url = re_schedule_url.replace(':customer_contract_type', itemDetail['stc']);
                                            re_schedule_url = re_schedule_url.replace(':security_clearence_id', ((itemDetail['security_clearance_level'] == 'null') || (itemDetail['security_clearance_level'] == null) || (itemDetail['security_clearance_level'] == ""))? 0:itemDetail['security_clearance_level']);

                                            tabContent += '<div class="col-12 row"><u><a id="re_schedule_'+k+'" style="cursor:pointer !important;" target="_blank" href="'+re_schedule_url+'"><div class="col-12 p0" style="color:#44617f;">Reschedule</div></a></u></div>';
                                            @endcan
                                            tabContent +='</div><div class="col-5 padding-l-r-0" style="float:right;">'+image_html+'</div></div>';

                                            //tabs color popup
                                            let selected_in_color_css = 'bg-' + itemDetail['sign_in_color'];
                                            let selected_out_color_css = 'bg-' + itemDetail['sign_out_color'];

                                            //sub tab color picking
                                            if((selected_in_color_css == "bg-color-green") && (selected_out_color_css != "bg-color-yellow" && selected_out_color_css != "bg-color-red")) {
                                                subTabColorArray[i+'_'+k+'_parent'] = selected_in_color_css;
                                            }else if((selected_in_color_css == "bg-color-yellow") && (selected_out_color_css != "bg-color-red") && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-red") == null)) {
                                                subTabColorArray[i+'_'+k+'_parent'] = selected_in_color_css;
                                            }else if(selected_in_color_css == "bg-color-red"){
                                                subTabColorArray[i+'_'+k+'_parent'] = 'bg-color-red';
                                            }

                                            if((selected_out_color_css == "bg-color-green") && (selected_in_color_css != "bg-color-yellow" && selected_in_color_css != "bg-color-red")) {
                                                subTabColorArray[i+'_'+k+'_parent'] = selected_out_color_css;
                                            }else if((selected_out_color_css == "bg-color-yellow") && (selected_in_color_css != "bg-color-red")) {
                                                subTabColorArray[i+'_'+k+'_parent'] = selected_out_color_css;
                                            }else if(selected_out_color_css == "bg-color-red"){
                                                subTabColorArray[i+'_'+k+'_parent'] = 'bg-color-red';
                                            }

                                            //employee name tab color picking
                                            if((selected_in_color_css == "bg-color-green") && (selected_out_color_css != "bg-color-yellow" && selected_out_color_css != "bg-color-red") && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-red") == null) && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-yellow") == null)) {
                                                empColorArray['list_item_'+item['id']] = selected_in_color_css;
                                            }else if((selected_in_color_css == "bg-color-yellow") && (selected_out_color_css != "bg-color-red") && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-red") == null)) {
                                                empColorArray['list_item_'+item['id']] = selected_in_color_css;
                                            }else if(selected_in_color_css == "bg-color-red"){
                                                empColorArray['list_item_'+item['id']] = 'bg-color-red';
                                            }

                                            if((selected_out_color_css == "bg-color-green") && (selected_in_color_css != "bg-color-yellow" && selected_in_color_css != "bg-color-red") && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-red") == null) && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-yellow") == null)) {
                                                empColorArray['list_item_'+item['id']] = selected_out_color_css;
                                            }else if((selected_out_color_css == "bg-color-yellow") && (selected_in_color_css != "bg-color-red") && (findValue(empColorArray, 'list_item_'+item['id'], "bg-color-red") == null)) {
                                                empColorArray['list_item_'+item['id']] = selected_out_color_css;
                                            }else if(selected_out_color_css == "bg-color-red"){
                                                empColorArray['list_item_'+item['id']] = 'bg-color-red';
                                            }
                                        });
                                        tabContent +='</div>';
                                        ind++;
                                    });
                                    let markerContent = popupHtml+listItemsHtml+listItemsHeaderHtml+tabContent+'</div></label></div></div>';

                                    infowindow.setContent(markerContent);
                                    infowindow.open(map, marker);
                                    map.setCenter(marker.getPosition());
                                    fnMakeColorChanges();
                                }
                            },
                            contentType: false,
                            processData: false,
                        });
                    }
                })(marker, i));
                markers[i] =  marker;
            });
        }
       },
       pinSymbol(color, iconSize = 1) {
            return {
                path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0',
                fillColor: color,
                fillOpacity: 1,
                strokeColor: '#000',
                strokeWeight: 1,
                scale: iconSize,
            };
        },
    }

    function findValue(o, prop, value) {
        console.log('out', o,prop,value);
        if (o.hasOwnProperty(prop) && o[prop] === value) {
            console.log('in', o,prop,value);
            return prop;
        }
        return null;
    }

    function fnMakeColorChanges() {
            $.each(empColorArray, function(index, value) {
                $('.'+index).addClass(value);
            });

            $.each(subTabColorArray, function(index, value) {
                $('.'+index).addClass(value);
            });
    }

    function changeListItemColor(item_id) {
        $('.btn-li-color').removeClass('active');
        $('.tab-content-custom').addClass('fade').removeClass('active');
        $('.list_item_'+item_id).addClass('active');
        $('#customer_'+item_id).addClass('active').removeClass('fade');
    }

    function openInfoWindow(id) {
        google.maps.event.trigger(markerArray[id], 'click');
    }

    // Code to run when the document is ready.
    $(function() {
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $("#view-details,.filter-details").css("display", "none");
        });

        // $("#report_start_time").val(findDateByParam(new Date(), 2));
        // $("#report_end_time").val(findDateByParam(new Date()));

        GeoMap.init();
        $(".search-input").dblclick(function () {
            $(".filter-details").toggleClass("toggled").css("display", "block");
        });
        $('#report-form').trigger('submit');

        @if(!empty($request->all()))
        $("#menu-toggle").click();
        $(".search-input").click();
        @endif

        $(".reset").click(function(e) {
            e.preventDefault();
            $('#report_start_time').timepicker('setTime', '08:00 AM');
            $('#report_end_time').timepicker('setTime', '03:59 PM');
            $('#report_start_date').val('{{date("Y-m-d")}}');
            $('#report_end_date').val('{{date("Y-m-d")}}');
            $('#report_wage_from').val('');
            $('#report_wage_to').val('');
            $('#report_security_clearance').val('').trigger('change');
            $('#report_city').val('').trigger('change');
            $('#report_assignment_type').val('').trigger('change');
            $('#report_customer_type').val('').trigger('change');
        });

        $('#stc-geo-mapping-form').trigger('submit');

        $.expr[':'].contains = function(a, i, m) {
            return jQuery(a).text().toUpperCase()
                .indexOf(m[3].toUpperCase()) >= 0;
        };
        $('#searchbox').on('keyup',function(){
            search = $(this).val();
            $.each(items, function(p,itm) {
                $('.customer_li_'+itm).show();
            });
            $('#candidate-site-list li:not(:contains('+search+'))').hide();
        });
    });
</script>
  @stop
