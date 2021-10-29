@extends('layouts.app')
@section('css')
<style>
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
</style>
@stop
@section('content')

    <!-- IDS Scheduling Report Form - Start -->
        <div class="table_title">
            <h4>IDS Appoinment Geomap</h4>
        </div>

    <div id="wrapper" class="siderbar-panel" style="height: 90%;">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
                <div class="clearfix"></div>
                <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                <div class="second-child"></div>
                <div id="candidate-data-left-panel">

                    @if(count($idsEntries)>0)
                    @foreach($idsEntries as $i=>$idsEntry)
                    <li class="ids_candidates {{'ids_candidate_li_'.$i}}" style="display: none;">
                        @php($idsEntryFormattedValue = str_replace(',', '', $idsEntry))
                        <i class="fa fa-map-marker float-right location-arrow" aria-hidden="true"></i>
                        <a target="_blank" onmouseover="openInfoWindow({{$i}});"  href="#" onClick="return false;">{{ ucfirst($idsEntryFormattedValue) }}</a>
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
            {{ Form::open(array('id'=>'report-form', 'class'=>'form-horizontal filter-section', 'method'=> 'POST')) }}
                        <div id="start_date" class="form-group row col-sm-12">
                            <label for="start_date" class="col-sm-4 col-form-label">Start & End Date<span class="mandatory"> *</span></label>
                            <div class="col-sm-4">
                                {{ Form::text('start_date', null, array('class'=>'form-control datepicker','placeholder'=>'Start Date', 'id'=>'report_start_date')) }}
                                <div class="form-control-feedback" id="startDateError"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                            <div class="col-sm-4">
                            {{ Form::text('end_date', null, array('class'=>'form-control datepicker','placeholder'=>'End Date', 'id'=>'report_end_date')) }}
                                <div class="form-control-feedback" id="endDateError"><span class="help-block text-danger align-middle font-12"></span></div>

                            </div>
                        </div>

                        <div id="ids_office_id" class="form-group row col-sm-12">
                            <label for="ids_office_id" class="col-sm-4 col-form-label">Office Location</label>
                            <div class="col-sm-8">
                                {{ Form::select('ids_office_id[]',$officeList, old('ids_office_id'),array('class'=> 'form-control select2','id'=>'office','multiple'=>"multiple")) }}
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12"></span>
                                </div>
                            </div>
                        </div>

                        <div id="ids_office_id" class="form-group row col-sm-12">
                            <label for="ids_office_id" class="col-sm-4 col-form-label">Service</label>
                            <div class="col-sm-8">
                                {{ Form::select('ids_service_id[]',$serviceList, old('ids_service_id'),array('class'=> 'form-control select2','id'=>'service','multiple'=>"multiple")) }}
                                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
                            </div>
                        </div>
                        <div id="ids_office_id" class="form-group row col-sm-12">
                            <label for="ids_office_id" class="col-sm-4 col-form-label">Client Showup</label>
                            <div class="col-sm-8">
                                {{ Form::select('client_show_up[]',array(0 => 'Select', 1=>'Yes', 2 => 'No'), old('client_show_up'),array('class'=> 'form-control','id'=>'client_show_up')) }}
                                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
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

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"> </script>

<script>
    var markerArray = [];
    $('.select2').select2({
        'allowClear': true,
        placeholder: ""
    });
    const GeoMap = {
        ref: {
            idsofficeId : null,
            idsServiceId:null,
            startDate : null,
            endDate : null,
            trendsRecords : [],
            dataTable : null,
            checkedLocationIds : [],
        },
        init() {
            //Event listeners
            this.registerEventListeners();
        },
        registerEventListeners() {
            let root = this;
            //Trend report filter
            $('#report-form').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = $(this).serializeArray();
                var trigerFunction = true;
                $('.form-group').removeClass('has-error').find('.help-block').text('');
                //Start date validation
                if($('#report_start_date').val() == ''){
                    form.find("[id='startDateError']").addClass('has-error').find('.help-block').text("Start date is required");
                    trigerFunction = false;
                }
                //End date validation
                if($('#report_end_date').val() == ''){
                    form.find("[id='endDateError']").addClass('has-error').find('.help-block').text("End date is required");
                    trigerFunction = false;
                }
                //Fetch Trend report data
                if(trigerFunction == true){
                    $('.form-group').removeClass('has-error').find('.help-block').text('');
                    root.ref.startDate = $('#report_start_date').val();
                    root.ref.endDate = $('#report_end_date').val();
                    root.ref.idsofficeId = $('#office').val();
                    root.ref.idsServiceId = $('#service').val();
                    root.ref.clientShowup = $('#client_show_up').val();
                    root.fetchIdsGeoMapDataEvent();
                }

            });

        },
        fetchIdsGeoMapDataEvent(){
            let root = this;
            let url = '{{ route("idsscheduling-admin.office.geomap-reports") }}';
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "ids_office_id":root.ref.idsofficeId,
                    'start_date':root.ref.startDate,
                    'end_date':root.ref.endDate,
                    'client_show_up':root.ref.clientShowup,
                    'ids_service_id':root.ref.idsServiceId,
                },
                type: 'GET',
                success: function(data) {
                  root.ref.trendsRecords = data;
                  root.loadMap(root.ref.trendsRecords);
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                    }
                },
                contentType: false
            });
       },
       loadMap(locations) {
        let root = this;

        $('.ids_candidates').hide();
        $('#map_div').css('display','block');
        let logo = '<img src="{{ asset("images/short_logo.png") }}">';
        let map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
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
        let infowindow = new google.maps.InfoWindow({
            maxWidth: 800,
        });

        let location_mapper_array = [];
        let markerObject1 = locations.map((location, i) => {
            $('.ids_candidate_li_' + location.id).show();
            let client_show_up = (location.is_client_show_up == '1') ? 'Yes': (location.is_client_show_up == '0')? 'No':'-';
            let payment_received = (location.is_payment_received == '1') ? 'Yes': (location.is_payment_received == '0')? 'No':'-';
            let icon_color_code = (location.ids_office && location.ids_office.icon_color_code !== null)? location.ids_office.icon_color_code: 'black';

            let marker_content =  '<div id="content" style="min-width:800px;" class="map-tooltip">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;<a style="color:#f26338;">'+(location.first_name? location.first_name:'') + (location.last_name? ' ' + location.last_name:'') +'</a></h4>';

            let latitude_longitude = (parseFloat(location.latitude) + '_' +  parseFloat(location.longitude)).toString();
            location_mapper_array[latitude_longitude] = '<div id="bodyContent"><label class="col-md-12 col-12 scrollable"><div class="row"><div class="col-12">'
                            +'<div class="row"><div class="col-6 p0">Postal Code</div><div class="col-6 p0 map-disc popup-value">'+location.postal_code+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Office</div><div class="col-6 p0 map-disc popup-value">'+(location.ids_office? location.ids_office.name:'-')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Phone</div><div class="col-6 p0 map-disc popup-value">'+(location.phone_number? location.phone_number:'-')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Service</div><div class="col-6 p0 map-disc popup-value">'+(location.ids_services? location.ids_services.name: '-')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Client Show Up</div><div class="col-6 p0 map-disc popup-value">'+client_show_up+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Payment Recieved</div><div class="col-6 p0 map-disc popup-value">'+payment_received+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Amount</div><div class="col-6 p0 map-disc popup-value">$'+(location.given_rate? location.given_rate: 0)+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Scheduled Date</div><div class="col-6 p0 map-disc popup-value">'+(location.slot_booked_date? moment(location.slot_booked_date).format('MMM D, Y'):'-')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Scheduled Time</div><div class="col-6 p0 map-disc popup-value">'+(location.ids_office_slots? moment(location.ids_office_slots.start_time,"HH:mm:ss").format("h:mm A") :'-')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Transaction Date</div><div class="col-6 p0 map-disc popup-value">'+ moment(location.created_at).format('MMM D, Y')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Payment Type</div><div class="col-6 p0 map-disc popup-value">'+(location.ids_payment_methods? location.ids_payment_methods.full_name: '-')+'</div></div>'
                            +'<div class="row"><div class="col-6 p0">Payment Reason</div><div class="col-6 p0 map-disc popup-value">'+(location.ids_payment_reasons? location.ids_payment_reasons.name:'-')+'</div></div>' +
                            '</div></div>' +
                            '</label></div>';

                marker_content += location_mapper_array[latitude_longitude]+'</div>';


            let marker =  new google.maps.Marker({
                position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                icon: root.pinSymbol(icon_color_code),
                content : marker_content,
                map: map,
            });

            markerArray[location.id] = marker;

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    if (infowindow) infowindow.close();
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                    map.setCenter(marker.getPosition());
                }
            })(marker, i));
            return marker;
        });

        let idsCenters = [];
        let office_marker_array = [];
        let markerObject2 = locations.filter(function(location) {
            if(location.ids_office && (!idsCenters.includes(location.ids_office_id))) {
                idsCenters.push(location.ids_office_id);
                return true;
            }
            else{
                return false;
            }
        }).map(function(location, j) {
            let special_instructions = (location.ids_office.special_instructions) ? location.ids_office.special_instructions : '-';
            let latitude_longitude = (parseFloat(location.ids_office.latitude) + '_' +  parseFloat(location.ids_office.longitude)).toString();
            let marker_content = '<div id="content" style="min-width:800px;" class="map-tooltip">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;<a style="color:#f26338;">'+location.ids_office.name+'</a></h4>';
                office_marker_array[latitude_longitude] = '<div id="bodyContent"><label class="col-md-12 col-12 scrollable"><div class="row"><div class="col-12">'
                            +'<div class="row"><div class="col-4 p0">Office Timing</div><div class="col-8 p0 map-disc popup-value">'+moment(location.ids_office.office_hours_start_time,"HH:mm:ss").format("h:mm A")+' - '+moment(location.ids_office.office_hours_end_time,"HH:mm:ss").format("h:mm A") +'</div></div>'
                            +'<div class="row"><div class="col-4 p0">Phone</div><div class="col-8 p0 map-disc popup-value">'+location.ids_office.phone_number+'</div></div>'
                            +'<div class="row"><div class="col-4 p0">Address</div><div class="col-8 p0 map-disc popup-value">'+location.ids_office.adress+'</div></div>'
                            +'<div class="row"><div class="col-4 p0">Special Instructions</div><div class="col-8 p0 map-disc popup-value">'+special_instructions+'</div></div>'
                            +'</label></div>';

            marker_content += office_marker_array[latitude_longitude]+'</div>';


            let locationMarker = new google.maps.Marker({
                position: { lat: parseFloat(location.ids_office.latitude), lng: parseFloat(location.ids_office.longitude) },
                icon: root.pinSymbol('#003A63',1.7,"M20.5 15h-9c-1.104 0-2 0.896-2 2s0.896 2 2 2h9c1.104 0 2-0.896 2-2s-0.896-2-2-2zM13.583 8l-1.083 6h7l-1.084-6h-4.833zM16 29l1.5-9h-3l1.5 9zM13 7h6c0.828 0 1.5-0.672 1.5-1.5s-0.672-1.5-1.5-1.5h-6c-0.829 0-1.5 0.672-1.5 1.5s0.671 1.5 1.5 1.5z",20),
                content : marker_content,
                map: map,
            });

            google.maps.event.addListener(locationMarker, 'click', (function (locationMarker, j) {
                return function () {
                    if (infowindow) infowindow.close();
                    infowindow.setContent(locationMarker.content);
                    infowindow.open(map, locationMarker);
                    map.setCenter(locationMarker.getPosition());
                }
            })(locationMarker, j));
            return locationMarker;
         });
       },
        pinSymbol(color, iconSize = 1, iconPath = "M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0",rotation=0) {
            return {
                path: iconPath,
                fillColor: color,
                fillOpacity: 1,
                strokeColor: '#000',
                strokeWeight: 1,
                rotation: rotation,
                scale: iconSize,
                anchor: new google.maps.Point(15, 30)
            };
        },
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

        $("#report_start_date").val(findDateByParam(new Date(), 2));
        $("#report_end_date").val(findDateByParam(new Date())); 
        <?php Log::channel('googleApi')->info(['Date' => \Carbon\Carbon::now()->format('Y-m-d'), 'Time' => \Carbon\Carbon::now()->format('H:i:s'), 'Service' => 'maps', 'Page' => 'appointment-geomap'])?>
        GeoMap.init();
        $(".search-input").click(function () {
            $(".filter-details").toggleClass("toggled").css("display", "block");
        });
        $('#report-form').trigger('submit');

        @if(!empty($request->all()))
        $("#menu-toggle").click();
        $(".search-input").click();
        @endif

        $(".reset").click(function(e) {
            e.preventDefault();
            $('#service').val('').trigger('change');
            $('#office').val('').trigger('change');
            $('#report_start_date').val('');
            $('#report_end_date').val('');
            $('#client_show_up').val('').trigger('change');
        });

        $('#searchbox').on('keyup',function(){
            search = $(this).val();
            $('#candidate-data-left-panel li').show();
            $('#candidate-data-left-panel li:not(:contains('+search+'))').hide();
        });
    });

    function findDateByParam(dateObject, substartDays = 0) {
        var today = new Date();
        today.setDate(today.getDate()-substartDays);
        return moment(today).format('Y-MM-DD');
    }
</script>
  @stop
