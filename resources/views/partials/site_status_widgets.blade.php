
<script type="text/javascript">

    $(function () {

        function getSiteStatusData(){
            
            var base_url = "{{ route('dashboard-site-status-data') }}";
            $.ajax({
                        url: base_url,
                        type: 'GET',
                        success: function (data) { 
                                
                                var customer_score = data.customer_score;
                                var shift_flag = data.shift_flag;
                                
                                if(customer_score.length >= 1){
                                    initMap(customer_score,shift_flag);
                                }else{
                                    initEmptyMap(new google.maps.LatLng('43.6532', '-79.3832'));
                                }
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
        }
        
        getSiteStatusData();


        var markers = [];
        var locations = [];

        function initMap(customer_score,shift_flag) {  

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

            lat = 0;
            long = 0;

            if(customer_score.length >= 1){
                lat = Number(customer_score[0]['customer']['details']['geo_location_lat']);
                long = Number(customer_score[0]['customer']['details']['geo_location_long']);
            }
            if (lat != 0 && long != 0) {
                var mapCenter = {lat: lat, lng: long};
            } else {
                var mapCenter = null;
                if(customer_score.length >=1){
                    var mapCenter = getLocationCoordinate(customer_score[0]['customer']['details']['postal_code']);
                }
                
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
            
            var customer = customer_score;
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
                var shift_flag = shift_flag;
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

            /** Update lattitude and longitude */
            function updateLatLong(model, url, resource_id, latLng) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    url: url,
                    type: 'GET',
                    data: {
                        'model': model,
                        'id': resource_id,
                        'lat': latLng.lat,
                        'lng': latLng.lng
                    },
                    success: function (data) {
                        if (!data.success) {
                            console.log('Can not able to update location co-ordinates');
                        }
                    },
                    fail: function (response) {
                        console.log('Failed Resopnse');
                        console.log(response);
                    }
                });
            }

            /**
            * Get lattitude and longitude
            * 
            * @param {string} address 
            */
            function getLocationCoordinate(address) { 
                var position = null;
                // var googleApiKey = "AIzaSyCcQJW9vzC7cLEaekdaJcC0H-dlJ8lRUMs";
                var googleApiKey =`{{config('globals.google_api_key')}}`;
                //console.log('getLocationCoordinate called');
                if (address != null) {
                    var postal_code = address.toUpperCase().replace(/\W/g, '').replace(/(...)/, '$1 ');
                    //console.log('inside of  address != null ', address, postal_code);
                    $.getJSON({
                        url: 'https://maps.google.com/maps/api/geocode/json',
                        //type: 'GET',
                        data: {
                            key: googleApiKey,
                            address: postal_code,
                            sensor: false
                        },
                        async: false,
                        success: function (data, textStatus) {
                            //console.log('inside of  success ' + textStatus, data);
                            try {
                                position = data.results[0].geometry.location;
                                console.log('Located Postal Code :' + postal_code);
                            } catch (err) {
                                console.log('Unable to locate :' + postal_code);
                            }
                        },
                        fail: function (result) {
                            console.log("error ", result);
                        }
                    });
                }
                return position;
            }

        }

    
        function openInfoWindow(id) {
            google.maps.event.trigger(markers[id], 'click');
        }

    

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

    });

  
    //window.history.pushState("object or string", "Title", "plot-in-map");
</script>