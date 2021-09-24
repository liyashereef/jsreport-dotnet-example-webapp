<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>

<script type="text/javascript">

    @isset($customer_score)
    var customer_score = {!! json_encode($customer_score) !!};
    @endisset

    var markers = [];
    var locations = [];

    @isset($customer_score)

    function initMap() {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';

        var head = document.getElementsByTagName('head')[0];
        // Save the original method
        var insertBefore = head.insertBefore;
        // Replace it!
        head.insertBefore = function (newElement, referenceElement) {
            if (newElement.href && newElement.href.indexOf('//fonts.googleapis.com/css?family=Roboto') > -1) {
                //console.info('Prevented Roboto from loading!');
                return;
            }
            insertBefore.call(head, newElement, referenceElement);
        };
        @if(isset($customer_score) && sizeof($customer_score) >=1)
        lat = Number("{{(!empty($customer_score[0]['customer']['details']['geo_location_lat']))?$customer_score[0]['customer']['details']['geo_location_lat']:''}}");
        long = Number("{{(!empty($customer_score[0]['customer']['details']['geo_location_long']))?$customer_score[0]['customer']['details']['geo_location_long']:''}}");
        @else
        lat = 0;
        long = 0;
        @endif
        if (lat != 0 && long != 0) {
            var mapCenter = {lat: lat, lng: long};
        } else {
            var mapCenter = null;
            @if(isset($customer_score) && sizeof($customer_score) >=1)
            var mapCenter = getLocationCoordinate("{{ $customer_score[0]['customer']['details']['postal_code'] }}");
            @endif
            
            mapCenter = (mapCenter === null) ? ({
                lat:{{config('globals.map_default_center_lat')}},
                lng: {{config('globals.map_default_center_lng')}}}) : mapCenter;
        }
        var mapEl = document.getElementById('map');
        if (typeof(mapEl) === 'undefined' || mapEl === null){
            return;
        }
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
            var rating = item.customer.rating_details; //Get customer rating details.
            if (customer.geo_location_lat == null || customer.geo_location_lat == '' || customer.geo_location_long == null || customer.geo_location_long == '') {
                position = getLocationCoordinate(customer.postal_code);
                if (position != '' && position != null) {
                    updateLatLong('cus', "{{route('location.store')}}", customer.id, position);
                }
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
            var shift_flag = {!! json_encode($shift_flag) !!};
            if (shift_flag == 1) {
                details_url1 = details_url.replace(':id', customer.id);
                project_number = '<a href="' + details_url1 + '">' + customer.project_number + '</a>';
            }

            var customer_id = customer.id;

            locations.push({
                customerId: customer_id,
                latlng: position,
                icon: '{{config('globals.markers')}}/' + item.score_details.color_class.total + '-dot.png'
            });
//console.log(customer_id);
        });

        var infowindow = new google.maps.InfoWindow({
            maxWidth: 800
        });

        //console.log(locations);
        var marker, i, contentString;

        function filterMarkers() {
            //reset all markers in the map
            markers.forEach(function (marker) {
                marker.setMap(null);
            });
            markers = [];
            var selectedIds = [];

            //---Start----Customer filter from Main Dashboard.
            let dashboardCustomerIds = '';
             dashboardCustomerIds = $('#dashboard-filter-customer').val(); 
            if(dashboardCustomerIds){
                var dashboardCustomerIds_int_values = dashboardCustomerIds.map(function(item) {
                    return parseInt(item);
                });
                selectedIds = dashboardCustomerIds_int_values;
            }
            //---End----Customer filter from Main Dashboard.

            //skipp all items for first time
            $('.largerCheckbox:checkbox:checked').each(function () {
                selectedIds.push($(this).data('customerid'));
            });
            //console.log(selectedIds);

            if (selectedIds.length <= 0) {
                selectedIds = locations.map(function (location) {
                    return location.customerId;
                });
            }

            for (i = 0; i < locations.length; i++) {

                if (selectedIds.indexOf(locations[i].customerId) > -1) {

                    marker = new google.maps.Marker({
                        position: locations[i].latlng,
                        map: map,
                        icon: locations[i].icon
                    });

                    markers.push(marker);
                }
            }
        }

        filterMarkers();

        $('.largerCheckbox').on('change', function () {
            filterMarkers();
        });

    }

    @endisset

    function openInfoWindow(id) {
        google.maps.event.trigger(markers[id], 'click');
    }

    $(function () {

        //$('.dropdown-search').select2();
        @if(isset($customer_score))
        initMap();
        @else
        initEmptyMap(new google.maps.LatLng('43.6532', '-79.3832'));
        @endif

    });

    function initEmptyMap(myCenter) {
        var logo = '<img src="{{ asset("images/short_logo.png") }}">';
        var locations = [];
        var mapProp = {center: myCenter, zoom: 8, mapTypeId: google.maps.MapTypeId.ROADMAP};
        var map = new google.maps.Map(document.getElementById('map'), mapProp);
    }

    function show1(){
        document.getElementById('div1').style.display ='none';
    }
    function show2(){
        document.getElementById('div1').style.display = 'block';
    }
    //window.history.pushState("object or string", "Title", "plot-in-map");
</script>