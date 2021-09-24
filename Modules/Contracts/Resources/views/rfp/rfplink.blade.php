@extends('layouts.app')
@section('content')
    <div class="embed-responsive embed-responsive-4by3">
        <div id="map" style="min-height:335px;" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d
            i n g . . . . . .
        </div>
    </div>


@stop

@section('scripts')
    <script  type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
    <script type="text/javascript">

                @if(isset($customer_score) )
        var customer_score = {!! json_encode($customer_score) !!};
                @endif
                @if(isset($postalCode) )
        var postalCode = {!! json_encode($postalCode) !!};
                @endif

        var markers = [];
        var locations = [];

                @if((isset($customer_score)) || (isset($postalCode) ))

        function initMap() {
            var logo = '<img src="{{ asset("images/short_logo.png") }}">';

            var head = document.getElementsByTagName('head')[0];
            // Save the original method
            var insertBefore = head.insertBefore;
            // Replace it!
            head.insertBefore = function (newElement, referenceElement) {
                if (newElement.href && newElement.href.indexOf('//fonts.googleapis.com/css?family=Roboto') > -1) {
                    console.info('Prevented Roboto from loading');
                    return;
                }
                insertBefore.call(head, newElement, referenceElement);
            };

            var postalCode = {!! json_encode($postalCode) !!};
            console.log(postalCode['rfp_site_postalcode'])
            var mapCenter = getLocationCoordinate(postalCode['rfp_site_postalcode']);

            mapCenter = (mapCenter === null) ? ({
                lat:{{config('globals.map_default_center_lat')}},
                lng: {{config('globals.map_default_center_lng')}} }) : mapCenter;
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: mapCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            var customer = {!! json_encode($customer_score) !!};

            var view_url = "{{ route('customer.details',':id') }}";
            var details_url = "{{ route('customer.guardTourDetails',':id') }}";

            $.each(customer, function (i, item) {
                var customer = item.customer.details;
                var supervisor = item.customer.supervisor;
                var areamanager = item.customer.areamanager;

                if (customer.geo_location_lat == null || customer.geo_location_lat == '' || customer.geo_location_long == null || customer.geo_location_long == '') {
                    position = getLocationCoordinate(customer.postal_code);

                } else {
                    position = {
                        lat: parseFloat(customer.geo_location_lat),
                        lng: parseFloat(customer.geo_location_long)
                    };
                }
                var project_number = '';

                if (customer.stc === 0) {
                    project_number = '<a href="' + view_url.replace(':id', customer.id) + '">' + customer.project_number + '</a>';
                } else {
                    project_number = customer.project_number;
                }


                var icons = '';
                if (customer.stc === 0) {
                    icons = '{{config('globals.markers')}}/black-dot.png';
                } else {
                    icons = '{{config('globals.markers')}}/yellow-dot.png';
                }

                var customer_id = customer.id;
                var client_name = camelcase(customer.client_name);
                var address = camelcase(customer.address) + '<br/>' + camelcase(customer.city) + ', ' + customer.postal_code.trim() + ', ' + customer.province.trim();
                var contact_person_name = (customer.contact_person_name) ? camelcase(customer.contact_person_name) : '--';
                var contact_person_phone = (customer.contact_person_phone) ? customer.contact_person_phone : '--';
                contact_person_phone += (customer.contact_person_phone_ext) ? ' x' + customer.contact_person_phone_ext : '';
                var client_email = (customer.contact_person_email_id) ? customer.contact_person_email_id : '--';
                var supervisor_name = (supervisor.full_name) ? camelcase(supervisor.full_name) : '--';
                var phone = (supervisor.phone) ? supervisor.phone : '--';
                phone += (supervisor.phone_ext) ? ' x' + supervisor.phone_ext : '';
                var email = (supervisor.email) ? ((supervisor.email == null) ? '--' : supervisor.email) : '--';
                var alter_email = (supervisor.alternate_email) ? ((supervisor.alternate_email == null) ? '--' : supervisor.alternate_email) : '--';
                var area_manager_name = (areamanager.full_name) ? camelcase(areamanager.full_name) : '--';
                var area_manager_phone = (areamanager.phone) ? areamanager.phone : '--';
                area_manager_phone += (areamanager.phone_ext) ? ' x' + areamanager.phone_ext : '';
                var area_manager_email = (areamanager.email) ? areamanager.email : '--';

                var info_html = '<div class="row map-row">';
                info_html += '<div class="col-md-6 col-xs-12 col-sm-6 col-lg-6"> <div class="row">';
                info_html += '<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Project No.</div>';
                info_html += '<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + project_number + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Client</div>';
                info_html += '<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + client_name + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Address</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + address + '</span></div></div></div><div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Client Contact</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + contact_person_name + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Client Phone</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + contact_person_phone + '</div></div>';

                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Client Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + client_email + '</span></div></div></div></div>';
                info_html += '<div class="row map-row"  style="padding-bottom: 5px; margin-bottom: 10px; border-bottom: 1px solid rgba(0, 0, 0, .1)"> <div class="col-md-6 col-xs-12 col-sm-6 col-lg-6">';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Supervisor</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + supervisor_name + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Phone</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + phone + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + email + '</span></div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Alternate Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + alter_email + '</span></div></div>';

                info_html += '</div><div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Area Manager</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc ">' + area_manager_name + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Phone</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + area_manager_phone + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + area_manager_email + '</span></div></div>';


                locations.push({
                    customerId: customer_id,
                    latlng: position,
                    info: info_html,
                    icon: icons
                });
//console.log(customer_id);
            });

            var postalCode = {!! json_encode($postalCode) !!};
//console.log(postalCode);
            /*$.each(postalCode, function (i, item) {*/

            var site_name = postalCode['rfp_site_name'];
            var site_address = postalCode['rfp_site_address'];
            var site_city = postalCode['rfp_site_city'];
            var site_postalcode = postalCode['rfp_site_postalcode'];

            position = getLocationCoordinate(site_postalcode);

            var info_html = '<div class="row map-row">';
            info_html += '<div class="col-md-6 col-xs-12 col-sm-6 col-lg-6"> <div class="row">';
            info_html += '<div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Name</div>';
            info_html += '<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + site_name + '</div></div>';
            info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Address</div>';
            info_html += '<div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + site_address + '</div></div>';
            info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">City</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + site_city + '</span></div></div></div><div class="col-md-6 col-xs-6 col-sm-6 col-lg-6">';
            info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Postal code</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + site_postalcode + '</div></div>';

            locations.push({
                customerId: '',
                latlng: position,
                info: info_html,
                icon: '{{config('globals.markers')}}/orange-dot.png'
            });

            /*});*/

            var infowindow = new google.maps.InfoWindow({
                maxWidth: 800
            });

            var marker, i, contentString;

            function filterMarkers() {
                markers = [];

                for (i = 0; i < locations.length; i++) {
                    marker = new google.maps.Marker({
                        position: locations[i].latlng,
                        map: map,
                        icon: locations[i].icon
                    });
                    markers.push(marker);
                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            contentString = '<div id="content" style="min-width:0px;" class="map-tooltip">' +
                                '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + '</h4>' +
                                '<div id="bodyContent">' +
                                '<label style="width:100%;">' + locations[i].info.replace(/\n/g, "<br />") + '</label>' +
                                '</div>' +
                                '</div>';
                            infowindow.setContent(contentString);
                            infowindow.open(map, marker);
                            map.setCenter(marker.getPosition());
                        }
                    })(marker, i));
                }
            }

            filterMarkers();

        }
                @endif

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

            //$('.dropdown-search').select2();
            @if((isset($postalCode)))
            initMap();
            @else
            initEmptyMap(new google.maps.LatLng('43.6532', '-79.3832'));
            @endif


        });

    </script>

@stop
@section('css'){
    <style>
        .embed-responsive{
            height: 100% !important;
        }
    </style>
}
    
@endsection
