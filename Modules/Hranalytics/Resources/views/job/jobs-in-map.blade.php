@extends('layouts.app')
@section('content')
<div id="candidate_map">
    <div class="table_title">
        <h4>Job Post Geomapping </h4>
    </div>
    <div id="wrapper" class="toggled siderbar-panel">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <div class="clearfix"></div>
                <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                <div class="second-child"></div>
                <div id="candidate-data-left-panel">
                    @if(count($jobs)>0)
                    @foreach($jobs as $i=>$job)
                    <li>
                         <i style="color:@if($job->trackingstep!=null) @if($job->trackingstep->process_id>=0 && $job->trackingstep->process_id<=5) red @elseif($job->trackingstep->process_id>5 && $job->trackingstep->process_id<=10) yellow @elseif($job->trackingstep->process_id>10 && $job->trackingstep->process_id<=14) green @endif @else red  @endif !important;" class="fa fa-map-marker float-right location-arrow" aria-hidden="true"></i>
                       {{--  <i style="color:@if($job->status=='approved' || $job->status=='completed' ) green @else red @endif !important;" class="fa fa-map-marker float-right location-arrow" aria-hidden="true"></i> --}}
                        <a onmouseover="openInfoWindow({{$i}});" href="{{ route('job.view',$job->id) }}">{{ ($job->customer->client_name) }}</a>

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
            <a class="navbar-brand" href="#menu-toggle" id="menu-toggle">
                <i class="fa fa-caret-left fa-2x" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <!-- /#wrapper -->
    <div class="embed-responsive embed-responsive-4by3">
        <div id="map" style="min-height:335px;" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>
    </div>
</div>

@stop
@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script type="text/javascript">
    var markers = [];
    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var trackurl = "{{ route('job.hr-tracking',[':job_id']) }}";
        var locations = [];

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
        {!!\App\Services\HelperService::googleAPILog('map','Modules\Hranalytics\Resources\views\job\jobs-in-map')!!}
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: {lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}},
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl   : false,
            panControl       : false,
            gestureHandling  : 'greedy',
        });

        var jobs = {!! json_encode($jobs) !!};
        var view_url = "{{ route('job.view',':id') }}";
        var infowindow = new google.maps.InfoWindow();
        var marker, i, contentString,icon;
        $.each(jobs, function(i, item) {
            if(item.customer.geo_location_lat==null || item.customer.geo_location_long==null)
            {
                position = getLocationCoordinate(item.customer.postal_code);
                if(position != '' && position != null){
                    updateLatLong('cus',"{{route('location.store')}}",item.customer.id,position);
                }
            }else{
                position = {lat: Number(item.customer.geo_location_lat), lng: Number(item.customer.geo_location_long)};
            }
            url = view_url.replace(':id', item.id);
            if(item.trackingstep!=null){
            if(item.trackingstep.process_id>=0 && item.trackingstep.process_id<=5)
            {
                 icon = "{{ asset('images/markers/red-dot.png') }}";
            }
            else if (item.trackingstep.process_id > 5 && item.trackingstep.process_id<= 10) {
                 icon = "{{ asset('images/markers/yellow-dot.png') }}";
            }else if (item.trackingstep.process_id > 10 && item.trackingstep.process_id <= 14) {
                  icon = "{{ asset('images/markers/green-dot.png') }}";
            }
            }
            else
            {
            icon = "{{ asset('images/markers/red-dot.png') }}";
            }
            wage_low = '$' + item.wage_low.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            wage_high = '$' + item.wage_high.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            requestor_email = null!=item.email?item.email:'--';
            track_url = trackurl.replace(':job_id', item.id);
            track_step = '<a href="'+track_url+'">'+((item.trackingstep)?item.trackingstep.process.id+'.'+item.trackingstep.process.process_name:'--')+'</a>';
            marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    icon:icon,
                    content:'<div id="content" style="min-width:500px;">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo +
                            '&nbsp;<a style="color:#f26338;" href="'+url+'">'+item.customer.client_name+
                            '</a></h4><div id="bodyContent">' +
                            '<label style="width:100%;">'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Position being hired</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.position_beeing_hired.position+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Job Code</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.unique_key+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">No. of vacancies</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.no_of_vaccancies+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Requestor\'s name</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.requester+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Requestor\'s Emp.No.</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.employee_num+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Requestor\'s Email</span><span class="col-sm-6 col-6 float-left p0 map-disc">'+requestor_email+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Client Name</span> <span class="col-sm-5 col-5 float-left p0 map-disc">'+item.customer.client_name+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Address</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.customer.address+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">City</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.customer.city+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Postal Code</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.customer.postal_code+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Area Manager\'s Name</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+item.area_manager+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Area Manager\'s Email</span><span class="col-sm-6 col-6 float-left p0 map-disc">'+item.am_email+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Wage Low</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+wage_low+'</span>'+
                            '<div class="clearfix"></div>'+
                            '<span class="col-sm-7 col-7 float-left p0 map-label">Wage High</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+wage_high+'</span>'+
                            '<div class="clearfix"></div>'+
                             '<span class="col-sm-7 col-7 float-left p0 map-label">HR Assigned</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+((item.assignee)?item.assignee.first_name+' '+((item.assignee.last_name)?item.assignee.last_name:''):'--')+'</span>'+
                            '<div class="clearfix"></div>'+
                             '<span class="col-sm-7 col-7 float-left p0 map-label">Last Tracking Step</span><span class="col-sm-5 col-5 float-left p0 map-disc">'+track_step+'</span>'+
                            '<div class="clearfix"></div>'+
                            '</label>' +
                            '</div>' +
                            '</div>'
                });
            markers.push(marker);
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
    function openInfoWindow(id) {
        google.maps.event.trigger(markers[id], 'click');
    }

    $(function () {
        initMap();
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
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
    });

    $(window).bind("load", function() {
    $('#sidebar').css('height', $(window).height()-70);
    $('#content-div').css('height', $(window).height()-70);
    $('#content-div').css('overflow', 'hidden');
});

</script>
@stop
