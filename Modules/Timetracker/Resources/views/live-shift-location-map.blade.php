@extends('layouts.app')
@section('css')
<style>
      .profileImage {
        width: 13.5rem;
        height: 13.5rem;
        border-radius: 50%;
        font-size: 2.5rem;
        color: #fff;
        text-align: center;
        line-height: 13.5rem;
    }

    .user-image-div {
        text-align: left;
        padding-left: 0px !important;
    }

    html, body, #map {
      height: 85%;
      margin: 0px;
      padding: 0px
    }
    .pac-item {
      top: 10px !important;
      height: 40px !important;
      font-size: 13px;
    }
    .pac-item-query { font-size: 13px;}

    .pac-dropdown-list
    {
      width: 23% !important;
      margin-left: .6rem !important;
    }
    #user-map-filter-input{
      background-color: #fff;
      border-radius: 4px;
      border: 1px solid #ced4da !important;
      padding: .375rem .75rem !important;
    }
    #searchMapInput{
      background-color: #fff;
      border-radius: 4px;
      border: 1px solid #ced4da !important;
      padding-left: 1rem;

    }
    .link{
    padding: .5rem;
    border: 1px solid #b3acac !important;
    border-radius: 4px !important;
    font-size: 14px !important;
    font-weight:bold;
    color: #13486b !important;
    width: 45%;
    display: flex;
    justify-content: center;
    cursor: pointer;
    white-space: nowrap;
    }
    .link:hover{
      color: #13486b;
      background: rgba(232, 233, 234, .5) !important;
    }
    .link.clicked{
      color: #ffffff !important;
      background: rgba(242, 53, 31, .8) !important;
      border-color: rgba(242, 53, 31, .8) !important;
    }

    #footer{
      position: relative !important;
    }
    #map
    {
      height: 100%;
    }
    .pac-item{
      color: unset;
    }
    #user-map-filter-input{
      appearance: none;
    }
    #user-map-filter-input option{
      color: #000000;
    }
    #user-map-filter-input:invalid ,#user-map-filter-input [disabled] {
      color: gray;
    }
    #openbtn{
      z-index: 99;
      left:-6px;
    }
    hr{
      width: 0;
      border: 2px solid black;
      height: 15px;
      margin: 0;
    }
    .main-tab.tab-customer li{
      font-size: 16px;
    }
    .b-none{
      border-bottom: none !important;
    }
    body .wrapper{
      margin-top: 70px !important;
    }
    #content-div{
      padding-bottom: 0;
    }
    #footer{
      margin-top: 1% !important;

    }
</style>
  <link href="{{ asset('faclitymanagementdashboard/dashboard-styles.css') }}" rel="stylesheet">
@stop
@section('content')

<input id="searchMapInput" class="mapControls pac-item pac-container pac-item-query" size="70" type="text" placeholder="Enter a Location">
<!-- <select name="user_id" id="user-map-filter-input" class="mapControls pac-item .pac-selected pac-container pac-dropdown-list" required >
  <option selected value="">Select Users</option>
  @foreach($users as $user)
   <option value="{{$user->id}}">
    {{$user->first_name}} {{$user->last_name}}
  </option>
@endforeach
</select> -->


<div class="row content-cards position-relative customer-show h-100 px-2 pb-5">
<div class="mapping mapping-ie mapping-site-dashboard" id="openbtn">
        <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x" aria-hidden="true"></i></a>
    </div>
<div class="customer-sidebar">
   <div class="h-100">
      <div class="px-4 py-2 bg-white rounded h-100">
            <div class="pt-3 pb-2 d-flex list-unstyled justify-content-between" >
              <a class="link" id="regular_shift_link"  href="{{ route('timetracker.shift-live-locations',["shift_type"=>SHIFT_TYPE_REGULER]) }}" >Regular Shift </a>
              <a class="link" id="msp_shift_link" href="{{ route('timetracker.shift-live-locations',["shift_type"=>SHIFT_TYPE_MSP]) }}">MSP Shift </a>
            </div>
            <!-- search -->
            <div class="form-group has-search position-relative mb-0">
                <span class="fa fa-search form-control-feedback"></span>
                <input type="text" id="customerSearch" class="form-control search-customer" placeholder="Enter Search Key">
            </div>

            <div class="pb-2 tab tab-customer d-flex list-unstyled justify-content-between align-items-center main-tab b-none" >
              <li class="tablinks2"  onclick="openTab(event, 'regularshift')" id="defaultOpenShift" >Customer </li>
              <hr>
              <li class="tablinks2"  onclick="openTab(event, 'mspshift')">Employees </li>
            </div>

            <div class="tabcontent2" id="regularshift">
              <div class="tab d-flex tab-customer list-unstyled justify-content-between align-items-center ">
                  <li class="tablinks pb-2 ml-0 mb-0  pr-0 cursor-pointer" onclick="getCustomerList(event, 'actual')" id="defaultOpen">Permanent</li>
                  <li class="tablinks pb-2 ml-0 mb-0  pr-0 cursor-pointer" onclick="getCustomerList(event, 'ytd')">Temporary</li>
              </div>

              <div id="actual" class="tabcontent">
                  <span onclick="this.parentElement.style.display='none'" class="topright"></span>

                  <div class="table-responsive ">
                      <table class="table customer-table">
                          <thead>

                          </thead>
                          <tbody id="myTable">
                              <tr>
                                  <td>
                                      @if(isset($permenentCustomers))
                                      <div class="scrollable">
                                          @foreach($permenentCustomers as $i => $customer)
                                          <li class="customer-name{{$i}}" style="text-align:left;">
                                              <div class="filter_checkbox atl m-r-checkbox">
                                                  <input type="checkbox" name="customer-map-filter-input" value="{{$customer->id}}"
                                                  id="chk-atl{{$customer->id}}" class="sat-filter-checkbox largerCheckbox"
                                                  data-customerid="{{$customer->id}}" style="margin-top:12px;float:right;" onclick="updateEmployeeList()" >
                                              </div>

                                              <div id="{{$i}}">
                                                  <div class="float-right" style="width:60px;margin-right:5px;" aria-hidden="true">
                                                  </div>
                                              </div>
                                              <a><span>{{ ucwords($customer->client_name) }}</span></a>
                                          </li>

                                          @endforeach
                                      </div>
                                      @else
                                      <li>No Customers</li>
                                      @endif
                                  </td>
                              </tr>

                          </tbody>
                      </table>
                  </div>
              </div>
              <div id="ytd" class="tabcontent">
                  <span onclick="this.parentElement.style.display='none'" class="topright"></span>
                  <div class="table-responsive ">
                      <table class="table customer-table">
                          <tbody id="myTable">
                              <tr>
                                  <td>
                                      @if(isset($stcCustomers))
                                      <div class="scrollable">
                                          @foreach($stcCustomers as $i=>$customer)
                                          <li class="customer-name{{$i}}" style="text-align:left;">
                                              <div class="filter_checkbox atl m-r-checkbox">
                                                  <input type="checkbox" name="customer-map-filter-input" value="{{$customer->id}}" name="atl"
                                                  id="chk-atl{{$customer->id}}" class="largerCheckbox sat-filter-checkbox"
                                                   data-customerid="{{$customer->id}}" style="margin-top:12px;float:right;" onclick="updateEmployeeList()">
                                              </div>

                                              <div id="{{$i}}">
                                                  <div class="float-right" style="width:60px;margin-right:5px;" aria-hidden="true">
                                                  </div>
                                              </div>
                                              <a><span>{{ ucwords($customer->client_name) }}</span></a>
                                          </li>

                                          @endforeach
                                      </div>
                                      @else
                                      <li>No Customers</li>
                                      @endif
                                  </td>
                              </tr>

                          </tbody>
                      </table>
                  </div>
              </div>
            </div>

            <div class="tabcontent2" id="mspshift">
                  <div class="table-responsive ">
                      <table class="table customer-table">
                          <thead>

                          </thead>
                          <tbody id="myTable">
                              <tr>
                                  <td>
                                      <div class="scrollable" id="employes-list"></div>
                                  </td>
                              </tr>

                          </tbody>
                      </table>
                  </div>
              <!-- </div> -->

            </div>

        </div>
    </div>
</div>
  <div class="col-xl-12 col-md-12 px-0 main-section">
    <div id="map"></div>
  </div>

</div>

@stop
@section('scripts')

<script src="https://maps.googleapis.com/maps/api/js?key={{config('globals.google_api_key')}}&libraries=places"></script>

<script type="text/javascript">
var logo = '<img src="{{ asset("images/short_logo.png") }}">';

//Side Filter bar
      $(function () {

        //Shift Type color chage
        $(".link").click(function(){
          $(".link").removeClass("clicked");
          $(this).addClass("clicked");
        });
        if({{(int)$shift_type_flag}} == {{SHIFT_TYPE_MSP}}){
          $('#msp_shift_link').addClass("clicked");
        }else{
          $('#regular_shift_link').addClass("clicked");
        }
        updateEmployeeList();
      });

        /*** On Customer Selecting list allocated employees*/
  function updateEmployeeList(){

         var selected_customerIds = [];
            $.each($("input[name='customer-map-filter-input']:checked"), function(){
              selected_customerIds.push($(this).val());
            });

        var url_emp_fetch = "{{ route('active_shift_employees')}}";
        var shift_type_flag = {{(int)$shift_type_flag}};
        var params_emp_fetch = {
          "shift_type_id": shift_type_flag,
          "customer_id": selected_customerIds
        }
                //append the query string
                url_emp_fetch += '?' + $.param(params_emp_fetch);

                $.get({
                    url: url_emp_fetch,
                    type: "GET",
                    global: false,
                    timeout: 15000,
                    success: function (data) {
                        setEmployees(data)
                    },
                  complete: function (data) {
                    }
                });

      }

      function setEmployees(data){
            $("#employes-list").empty();
            var contents = '';
            if(data.content.length >0){

              $.each(data.content, function (key, val) {

              contents += '<li class="customer-name'+key+'" style="text-align:left;">'+
                                '<div class="filter_checkbox atl m-r-checkbox">'+
                                  '<input type="checkbox" name="user-map-filter-input" value="'+val.id+'" id="chk-atl'+val.id+'"  class="largerCheckbox sat-filter-checkbox" data-customerid="'+val.id+'" style="margin-top:12px;float:right;">'+
                                '</div>'+
                                  '<div id="'+key+'">'+
                                     '<div class="float-right" style="width:60px;margin-right:5px;" aria-hidden="true"></div>'+
                                  '</div>';
                                  if(val.last_name){
                                    contents +=  '<a><span>'+val.first_name +' '+ val.last_name+'</span></a>';
                                  }else{
                                    contents +=  '<a><span>'+val.first_name+'</span></a>';
                                  }
                                  contents += '</li>';
            });

            }else{
              contents += '<li> <div> No records found. </div> </li>';
            }
            $("#employes-list").html(contents);
      }

// Driver Map Section
       $(function () {
       //Setting map icons(car icon for MSP shift and pointer for Reguler shift)
         if({{(int)$shift_type_flag}} == {{SHIFT_TYPE_MSP}}){
          var icons = {
                default: "{{asset('images/markers/car.png')}}",
                in_progress: "{{asset('images/markers/car_active.png')}}",
                idle: "{{asset('images/markers/car_idle.png')}}",
            };
         }else{
          var icons = {
                default: "{{ asset('images/markers/green-dot.png') }}",
                in_progress: "{{asset('images/markers/red-dot.png')}}",
                idle: "{{asset('images/markers/black-dot.png')}}",
            };
         }

            var mstMarkers = [];

            //Create the map
            var map = new google.maps.Map(document.getElementById('map'), {
                center: new google.maps.LatLng({{config('globals.map_default_center_lat')}}, {{config('globals.map_default_center_lng') }}),
                zoom: 7,
                streetViewControl: false,
                gestureHandling: 'greedy'
            });
            // bounds  = map.LatLngBounds();

            //Map search bar
            var input = document.getElementById('searchMapInput');
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            //Create the info window
            var mstInfoWindow = new google.maps.InfoWindow({
                content: '',
                maxWidth: 200
            });
            var markerSearch = new google.maps.Marker({
          map: map
          });

    /* Auto complete search */
    var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
    var markerSearch = new google.maps.Marker({
              map: map
        });

        autocomplete.addListener('place_changed', function() {
            // infowindow.close();
            markerSearch.setVisible(false);
            var place = autocomplete.getPlace();
            /* If the place has a geometry, then present it on a map. */
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(6);
            }
            markerSearch.setIcon(({
              url: "{{ asset('images/markers/blue-pin.png') }}",
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            markerSearch.setPosition(place.geometry.location);
            markerSearch.setVisible(true);

        });
    /* Auto complete search */
            function setDeviceCoordinates(data) { //console.log(data);
                //clean markers in map
                mstMarkers.forEach(function (marker) {
                    marker.setMap(null);
                });
                //clean the marker array
                mstMarkers = [];

                data.content.forEach(function (item) {
                    var currentMarkerIcon = icons.default;

                    // Create markers.
                    if (Object.prototype.hasOwnProperty.call(item, 'pending_dispatch_request')
                        && item.pending_dispatch_request) {
                        currentMarkerIcon = icons.in_progress;
                    }
                     if (item.is_idle) {
                        currentMarkerIcon = icons.idle;
                    }

                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(item.latitude, item.longitude),
                        icon: currentMarkerIcon,
                        map: map
                    });


                    //On click the marker
                    google.maps.event.addListener(marker, 'click', function () {
                        mstInfoWindow.setContent(buildVehicleCoordinateInfoWindowContent(item));
                        mstInfoWindow.open(map, marker);
                    });
                    //Save marker reference
                    mstMarkers.push(marker);
                })

            }

            function buildVehicleCoordinateInfoWindowContent(item) {
                var employee_name  = camelcase(item.first_name);
                var employee_last_name = camelcase(item.last_name);
                var last_name = item.last_name;
                var full_name=camelcase(item.first_name)+' '+item.last_name;
                var employee_full_address =camelcase(item.address);
                var city = camelcase(item.city);
                var postal_code = item.postal_code;
                var phone = item.phone;
                var cell_no = item.cell_no;
                var email = item.employee_work_email;

                var image_html = '';
                if(item.image != null && item.image != "") {
                    var image = "{{asset('images/uploads/') }}/" + item.image;
                    image_html = '<img name="image" src="'+image+'"  class="profileImage">';
                }else{
                  var initial_characters = (employee_name? employee_name.charAt(0): '') + ((last_name != "")? last_name.charAt(0): camelcase(employee_name.charAt((employee_name.length - 1))));
                    image_html = '<div class="profileImage" style="background: linear-gradient(to bottom, #F2351F, #F17437);">'+initial_characters+'</div>';
                }

                var customer ='--';
                var customer = item.client_name;
                var supervisor_first_name = camelcase(item.supervisor_first_name);
                var supervisor_last_name = camelcase(item.supervisor_last_name);
                var supervisor = supervisor_first_name+' '+supervisor_last_name;
                var supervisor_contact_no = item.supervisor_phone;
                var supervisor_cell_no = item.supervisor_cell_no;
                var area_manager_first_name = camelcase(item.area_manager_first_name);
                var area_manager_last_name = camelcase(item.area_manager_last_name);
                var area_manager = area_manager_first_name+' '+area_manager_last_name;
                var area_manager_contact_no = item.area_manager_phone;
                var area_manager_cell_no = item.area_manager_cell_no;
                var rating = item.employee_rating;


                    var content='<div id="content" style="min-width:0px;" class="map-tooltip">' +
                            '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;<a style="color:#f26338;" href="">'+full_name+'</a></h4>' +
                            '<div id="bodyContent">' +
                            '<label class="col-md-12 col-12 scrollable"><div class="row"><div class="col-7"><div class="row"><div class="col-6 p0">Employee Name</div><div class="col-6 p0 map-disc popup-value">'+full_name+'</div></div><div class="row"><div class="col-6 p0">Work Number</div><div class="col-6 p0 map-disc popup-value">'+phone+'</div></div><div class="row"><div class="col-6 p0">Cell Number</div><div class="col-6 p0 map-disc popup-value">'+cell_no+'</div></div><div class="row"><div class="col-6 p0">Work Email</div><div class="col-6 p0 map-disc popup-value">'+email+'</div></div><div class="row"><div class="col-6 p0">Customer</div><div class="col-6 p0 map-disc popup-value">'+customer+'</div></div><div class="row"><div class="col-6 p0">Supervisor</div><div class="col-6 p0 map-disc popup-value">'+supervisor+'</div></div><div class="row"><div class="col-6 p0">Work Number</div><div class="col-6 p0 map-disc popup-value">'+supervisor_contact_no+'</div></div><div class="row"><div class="col-6 p0">Cell Number</div><div class="col-6 p0 map-disc popup-value">'+supervisor_cell_no+'</div></div><div class="row"><div class="col-6 p0">Area Manager</div><div class="col-6 p0 map-disc popup-value">'+area_manager+'</div></div><div class="row"><div class="col-6 p0">Work Number</div><div class="col-6 p0 map-disc popup-value">'+area_manager_contact_no+'</div></div><div class="row"><div class="col-6 p0">Cell Number</div><div class="col-6 p0 map-disc popup-value">'+area_manager_cell_no+'</div></div><div class="row"><div class="col-6 p0">Rating</div><div class="col-6 p0 map-disc popup-value">'+rating+'</div></div></div><div class="col-5 user-image-div">'+image_html+'</div></div></label>'+
                            '</div>' +
                            '</div>';
                return content;
            }

            function getDriverMapFilterParams() {
              //Adding checked customerIds and EmployeeIds in an array
            var customerIds = [];
            var employeeIds = [];
            //customer
            $.each($("input[name='customer-map-filter-input']:checked"), function(){
              customerIds.push($(this).val());
            });
            //employee
            $.each($("input[name='user-map-filter-input']:checked"), function(){
              employeeIds.push($(this).val());
            });
                return {
                    "user_id": employeeIds,
                    "customerIds": customerIds,
                    "shift_type_flag":{{(int)$shift_type_flag}},
                };

            }

            //Fetch device coordinates from the server
            function fetchDeviceCoordinates() {

                var url = "{{ route('dispatch_request_coordinates_web')}}";
                var params = getDriverMapFilterParams();

                //append the query string
                url += '?' + $.param(params);

                $.get({
                    url: url,
                    type: "GET",
                    global: false,
                    timeout: 15000,
                    success: function (data) {
                        setDeviceCoordinates(data)
                    },
                    complete: function (data) {
                    }
                });
            }

            //initial loading
            fetchDeviceCoordinates();

            //frequently fetch & update locations
            setInterval(function () {
                fetchDeviceCoordinates();
            }, 10000);

            //On change filter(customer and employee) triger
            $('body').on('change',".sat-filter-checkbox",function(){
              fetchDeviceCoordinates();
              });

        });
</script>
  <!-- Dashboard Scripts -->
<script>



    //sidemenu script
    $(document).ready(function() {
        $("#customerSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable li").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        //customer sidemenu
        $("#openbtn").click(function(e) {
            e.preventDefault();
            $(".content-cards").toggleClass("customer-show");
        });

        $('.tab-btn .dropdown-menu a').click(function() {
            var selText = $(this).attr('data-value');
            $('.section-common').hide();
            $('.' + selText).show();
        });
    });

    //dropdown script
    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("drop-card-details");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    function getCustomerList(evt, customerType) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(customerType).style.display = "block";
        evt.currentTarget.className += " active";
    }
    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();

    function openTab(evt, shiftName) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent2");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }
      tablinks = document.getElementsByClassName("tablinks2");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }
      document.getElementById(shiftName).style.display = "block";
      evt.currentTarget.className += " active";
    }


    document.getElementById("defaultOpenShift").click();

    // $(".tab .tablinks").click(function (e) {
    //   e.preventDefault();
    //     $(this).tab('active');
    // });
</script>

 @stop
