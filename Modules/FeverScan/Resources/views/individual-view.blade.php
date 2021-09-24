@extends('layouts.app')
@section('content')

<style>
    #candidate-data-left-panel li:last-child {
        margin-bottom: 17px;
    }
    .map-tooltip{
        min-width: 330px !important;
    } 
</style>
    <div id="supervisor_panel">
        <div class="table_title">
            <h4>
                Dashboard - Macro View      
            </h4>

        </div>
        <div id="wrapper" class="toggled siderbar-panel">
            
            <!-- Sidebar -->
            <div id="sidebar-wrapper" >
                <ul class="sidebar-nav" style="margin-top:50px">
                    <div class="clearfix"></div>
                  <div class="searchbox" style="width:342px;margin-top:-50px;background:#fff">
                    <p><input type="text" id="searchbox" class="form-control search-input"   placeholder="Search"></p>
                    <!--
                    <p><button style="margin-left:10px" class="btn btn-primary">Search</button></p>
                    -->
                  </div>
                    <div class="second-child"></div>
                    <div id="candidate-data-left-panel">

                        @if(isset($customers))

                            @foreach($customers as $i=>$customer)
                                                            
                            <li class={{$i}} class='atl'>
                                        <div class="filter_checkbox m-r-checkbox">
                                            <input type="checkbox" name="chk-atl{{$customer->id}}y"
                                                   id="chk-atl{{$customer->id}}"
                                                   class=largerCheckbox
                                                   {{$i}}
                                                   data-customerid="{{$customer->id}}" data-liclass="{{$i}}"
                                                   style="margin-top:9px;float:right;">
                                                   <a href="#">{{ ucwords($customer->client_name) }} </a>
                                        </div>
                                </li>

                            @endforeach
                        @else
                            <li>No Customers</li>
                        @endif
                    </div>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <div class="mapping mapping-ie mapping-site-dashboard" {{--style="margin-left: 90px; left: -90px"--}}>
                <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x"
                                                                                aria-hidden="true"></i></a>
            </div>
        </div>
        {{ Form::open(array('id'=>'filtering-form','method'=>'POST')) }}

            <!-- Filter Start-->
                <div id="view-details" class="toggled filter-details" style="display: none;padding:10px">
                    <div id="sidebar-view-details" class="hide-vertical-scroll filter-border">
                        <h4 class="padding-top-20">Filter Criteria</h4>
                                           
                        
                    <input type="hidden" id="customer_id" name="customer_id" value="{{$customer_id}}">
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-md-3 col-xs-12 float-left" style="top: 27px !important;">Date Range</label>
                            <div class="col-sm-4 col-md-4 col-xs-12 ">
                            Start 
                            {{Form::text('startDate',$request->get('startDate'),array('class'=>'form-control datepicker','id'=>'startDate','placeholder'=>"Due Date",'max'=>"2900-12-31",'readonly'=>"readonly"))}}
                            </div>
                            <div class="col-sm-4 col-md-4 col-xs-12 ">
                            End 
                            {{Form::text('endDate',$request->get('endDate'),array('class'=>'form-control datepicker','id'=>'endDate','placeholder'=>"Due Date",'max'=>"2900-12-31",'readonly'=>"readonly"))}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-md-3 col-xs-12 float-left">Province</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('province',[''=>'Please Select'] + $province,old('province',$request->get('province')),array('class'=>'form-control dropdown-search','id'=>'province')) }}
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-md-3 col-xs-12 float-left">City</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                <select id="city" name="city" class="form-control dropdown-search">
                                    <option value="0">Please Select</option>
                                    @foreach ($cities as $city)
                                    @if($city->city!="")
                                        <option class="{{str_replace(" ","",$city->province)}} citydropdown">{{$city->city}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        

                        <div class="form-group row">
                            <label class="col-sm-3 col-md-3 col-xs-12 float-left">Age Group</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('agegroup',[''=>'Please Select'] + $agegroup,old('agegroup',$request->get('agegroup')),array('class'=>'form-control dropdown-search','id'=>'agegroup')) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-md-3 col-xs-12 float-left">Gender</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('gender',[''=>'Please Select'] + $gender,old('gender',$request->get('gender')),array('class'=>'form-control dropdown-search','id'=>'gender')) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-md-3 col-xs-12 float-left">Temperature </label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('tempgroup',[''=>'Please Select'] + $tempgroup,old('tempgroup',$request->get('tempgroup')),array('class'=>'form-control dropdown-search','id'=>'tempgroup')) }}                            
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="text-center margin-bottom-5">
                            <button type="submit" class="btn submit filterbutton" >Filter</button>
                            <input type="hidden" value="1" name="filtering">
                            <button type="reset" class="btn submit reset">Reset</button>
                        </div>
                        

                    </div>
                </div>
                {{ Form::close() }}
                <!-- Filter End-->

    <!-- Map Start -->
        <div class="embed-responsive embed-responsive-4by3">
            <div id="map" style="min-height:335px;" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d
                i n g . . . . . .
            </div>
        </div>
 <!-- Map End -->
        
        
    </div>
@stop
@section('scripts')
 <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
<script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"> </script>

    <script type="text/javascript">
      
      $('.select2').select2();
        $(function () { 
            $(".datepicker").datepicker();
            var customer = $("#customer_id").val();
            if(customer!=""){
                customeridarray = customer.split(",");
                customeridarray.forEach(element => {
                    if(element!=""){
                        $("#chk-atl"+element).prop("checked",true);
                    }
                });
            }
        });
        
        var markers = [];
        var locations = [];

        function initMap() {
                var logo = '<img src="{{ asset("images/short_logo.png") }}">';
                
                var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: {lat: {{config('globals.map_default_center_lat')}}, lng: {{config('globals.map_default_center_lng')}}},
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                gestureHandling: 'greedy'
                });
               
                // Create an array of alphabetical characters used to label the markers.
                var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                // Add some markers to the map.
                // Note: The code uses the JavaScript Array.prototype.map() method to
                // create an array of markers based on a given "locations" array.
                // The map() method here has nothing to do with the Google Maps API.

                // var info_html = ' method here has nothing to do with the Google Maps API';
                // var markers = locations.map(function(location, i) {
                // return new google.maps.Marker({
                //     position: location,
                //     // label: labels[i % labels.length]
                // });

                // });

                
                var mapData = {!! json_encode($individualviewdata) !!};
                $.each(mapData, function (i, item) {
                    var individualData = item
                    var position = {
                        lat: parseFloat(individualData.geo_location_lat),
                        lng: parseFloat(individualData.geo_location_long)
                    };
            
                    var project_number =  individualData.customer.project_number;
                    var customer_id = individualData.customer.id;
                    var colorcode = individualData.colorcode;
                    console.log(colorcode);
                    var client_name = camelcase(individualData.customer.client_name);
                    var address = camelcase(individualData.customer.address) + '<br/>' + camelcase(individualData.customer.city) + ', ' + individualData.customer.postal_code.trim() + ', ' + individualData.customer.province.trim();
                
                var info_html = '<div class="row map-row" style="">';
                info_html += '<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12"> ';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Project No.</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + project_number + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Client</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + client_name + '</div></div>';
                info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Address</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + address + '</span></div></div>';
                info_html += '</div>';
                info_html += '</div>';

                info_html += '<div class="row map-row" style="padding-bottom: 5px; margin-bottom: 10px; border-bottom: 1px solid rgba(0, 0, 0, .1)">';
                info_html += '<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Gender</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + individualData.gender + '</div></div>';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Age Group</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + individualData.age_group + '</div></div>';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Temperature</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + individualData.temperature + '</div></div>';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Date</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + moment(individualData.created_at).format('LL') + '</div></div>';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Time</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + moment(individualData.created_at).format('LT') + '</div></div>';
                info_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Note</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + individualData.notes + '</div></div>';
                info_html += '<div>';
                info_html += '</div>';
                             
                    
                locations.push({
                    customerId: customer_id,
                    latlng: position,
                    info: info_html,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        strokeColor: colorcode,
                        scale: 5
                    }
                });

                });

                var infowindow = new google.maps.InfoWindow({
                    maxWidth: 800
                });


                var marker, i, contentString;

                function filterMarkers() {
                    //reset all markers in the map
                    for (var key in markers) {
                        markers[key].setMap(null);
                    }
                    markers = [];
                    var selectedIds = [];
                    //skipp all items for first time

                    $('.largerCheckbox:checkbox:checked').each(function () {
                        
                        selectedIds.push($(this).data('customerid'));
                        
                    });
                    if (selectedIds.length <= 0) {
                        selectedIds = locations.map(function (location) {
                            return location.customerId;
                        });
                    }
                    var bounds = new google.maps.LatLngBounds();

                    for (i = 0; i < locations.length; i++) {
                        if (selectedIds.includes(locations[i].customerId) == true) {
                            marker = new google.maps.Marker({
                                position: locations[i].latlng,
                                map: map,
                                icon: locations[i].icon
                            });

                            google.maps.event.addListener(marker, 'click', (function (marker, i) { //alert(i);

                                return function () {
                                    contentString = '<div id="content" style="min-width:0px;" class="map-tooltip">' +
                                        '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + '</h4>' +
                                        '<div id="bodyContent">' +
                                        '<label style="width:93% !important;padding-left:32px !important;">' + locations[i].info.replace(/\n/g, "<br />") + '</label>' +
                                        '</div>' +
                                        '</div>';
                                    infowindow.setContent(contentString);
                                    infowindow.open(map, marker);
                                    map.setCenter(marker.getPosition());
                                }
                            })(marker, i))
                            markers[locations[i].customerId] = marker;

                            bounds.extend(marker.getPosition());
                            map.fitBounds(bounds);
                         markers['cust-'+locations[i].customerId] = marker;
                            
                        markers.push(marker);
                        }else{
                           
                        }
                    }
                }

                filterMarkers();

                $('.largerCheckbox').on('change', function () {
                    var id= $(this).data('customerid');
                    var customeridhidden = $("#customer_id").val();
                    if($(this).prop("checked")==true){
                        $("#customer_id").val(customeridhidden+""+id+",");
                    }else{
                        var removecustomerid= customeridhidden.replace(id+",","");
                        $("#customer_id").val(removecustomerid);
                    }
                    filterMarkers(); //alert('chec - '+id);
                    // openInfoWindow('cust-'+id);
                    openInfoWindow('cust-'+id,map);


                });
                
                //For Marker Clusterer
                // var markerCluster = new MarkerClusterer(map, markers,
                //     {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        
                $("#province").trigger("change");
        
        }

        function initEmptyMap(myCenter) {
            var logo = '<img src="{{ asset("images/short_logo.png") }}">';
            var locations = [];
            var mapProp = {center: myCenter, zoom: 8, mapTypeId: google.maps.MapTypeId.ROADMAP};
            var map = new google.maps.Map(document.getElementById('map'), mapProp);
        }

        function openInfoWindow(id,map) {
            google.maps.event.trigger(markers[id], 'click');
            //google.maps.event.trigger(map, 'resize');

        }

        $(function () {
            initMap();
            //$('.dropdown-search').select2();
            //$(".searchbox").show();
            $(".searchbox").hide();
            $("#menu-toggle").click(function (e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
                $("#view-details,.filter-details").css("display", "none");
                if($("#wrapper").attr("class")=="siderbar-panel"){
                    $(".searchbox").show();
                }else{
                    $(".searchbox").hide();
                }
                    
               

                
            });
            $("#menu-toggle1").click(function (e) {
                e.preventDefault();
                $("#wrapper-right").toggleClass("toggled");
            });
            $("#filter-view").click(function () {
                $(".filter-details").toggleClass("toggled");
                $(".filter-details").css("display", "block");
            });
           
            $('#searchbox').on('keyup', function () {
                search = $(this).val();
                $('#candidate-data-left-panel li').show();
                $('#candidate-data-left-panel li:not(:contains(' + search + '))').hide();
            });
            $(".search-input").click(function () {
                $(".filter-details").toggleClass("toggled");
                $(".filter-details").css("display", "block");
            });

            @if(isset($request))
            @if(!empty($request->all()))
            @if(!empty($request->shift_customerid))
            {
                $(".filter-details").toggleClass("toggled");
                $(".filter-details").css("display", "none")
                // $("#view-details,.filter-details").css("display", "none");
                $("#menu-toggle1").click();                
            }@else{
                $(".filter-details").toggleClass("toggled");
                $(".filter-details").css("display", "block")
                // $("#view-details,.filter-details").css("display", "block");
                $("#menu-toggle").click();
            }
            @endif
            
            $(".search-input").click();
            @endif
            @endif
            $(".reset").click(function (e) {
                e.preventDefault();
                $(".largerCheckbox").prop('checked', false); 
                $(this).closest('form').find("input[type='text']").val("");
                $(this).closest('form').find("select").prop('selectedIndex', 0);
            });

           // $(".citydropdown").css("display","none");
        });

        $(window).bind("load", function () {
            $('#sidebar').css('height', $(window).height() - 70);
            $('#content-div').css('height', $(window).height() - 70);
            $('#content-div').css('overflow', 'hidden');
        });

       $(".filterbutton").on("click",function(e){
           e.preventDefault();
           var url = "{{route('fever.individual-view')}}";
           var startDate = $("#startDate").val();
           var city = $("#city").val();
            var endDate = $("#endDate").val();
            var agegroup = $("#agegroup").val();
            var gender = $("#gender").val();
            var tempgroup = $("#tempgroup").val();
            var province = $("#province").val();
            var city = $("#city").val();
            
             url = url+"?startDate=" + encodeURIComponent(startDate) + "&endDate=" + encodeURIComponent(endDate)+ "&agegroup=" + encodeURIComponent(agegroup)+
              "&gender=" + encodeURIComponent(gender)+ "&tempgroup=" + encodeURIComponent(tempgroup)+ "&province=" + encodeURIComponent(province)+
               "&city=" + encodeURIComponent(city);
            window.location.href = url;


       })

       $("#province").on("change",function(e){
            $("#city").val("0");
            var selectprovince = ($(this).val()).replace(" ","");
            if(selectprovince!=""){
               $(".citydropdown").css("display","none");
            $("."+selectprovince).css("display","block");

            var filter_city = {!! json_encode($filter_city) !!};
            if(filter_city!=null){
                $("#city").val(filter_city);
            } 
            }
            
       });


       if($("#province").val()=="" || $("#province").val()==null){
        $(".citydropdown").css("display","none");

       }

        //window.history.pushState("object or string", "Title", "plot-in-map");
    </script>
    <style type="text/css">
   
    .site-status-wrap .table th{
	    font-weight: bold;
        }
    .site-status-wrap .table span{
	    color: #fff;
	    width: 100px;
	    padding: 5px 10px;
	    text-align: center;
	    font-weight: bold;border-radius: 5px;
	    display: inline-block;
        }
    .sort-link {
            background: none !important;
            border: none;
            padding: 0 !important;
            color: #069;
            text-decoration: underline;
            cursor: pointer;
        }

        .customer-score {
            display: -webkit-box;
            height: 30px !important;
            margin-top: 5px;
        }

        input.largerCheckbox {
            width: 20px;
            height: 20px;
        }

        #sidebar-view-details {
            left: 601px !important;
        }
        #sidebar-view-details1 {
            left: 601px !important;
        }
        .m-r-checkbox {
            margin-right: -29px !important
        }

        .p-41 {
            padding: 6px 41px !important;
        }
        

        /*#content-div{
            position: relative;
        }*/
        .live-status-wrap{
            width: 330px;
            height: 100%;
            position: fixed;
            right: 0;
            top: 72px;	
            background: #fff;
            z-index: 99;
            box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.2), 0px 4px 5px 0px rgba(0, 0, 0, 0.14), 0px 1px 10px 0px rgba(0, 0, 0, 0.12);
        }
        .ls-heading{
            width: 100%;
            float: left;
            background: #f7f7f7;
            padding: 10px 15px;
            color: #003b63;
            font-weight: bold;
        }
        .ls-heading span{
            font-size: 15px;
            padding-left: 10px;
        }
        .ls-heading a{
            float: right;
        }
        .ls-content{
            width: 100%;
            height: 500px;
            float: left;
            padding: 15px;
            overflow: auto;
            font-size: 13px;
        }
        .ls-content .form-control{
            border-radius: 0;
        }
        .height-100{
            height:82% !important;
        }
        .online-wrap{
            padding: 20px 15px;
            color: #263344;
            font-size: 13px;
        }
        .online-wrap ul{
            padding: 0;
            margin: 0;
            list-style-type: none;
        }
        .online-wrap li{
            padding: 5px 0;
            height: auto;
        }
        .online-wrap li span{
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        span.online{
            background: #21a71d;
        }
        span.offonline{
            background: #d21a1a;
        }
        span.sleep{
            background: #f8b30e;
        }
        .ls-overlay {
    width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    background: #0000004d;
    z-index: 9999;
    display: none;
}
        .site-status-wrap{
            width: 60%;
            position: fixed;
            top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
            background: #fff;
            z-index: 99;
            box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.2), 0px 4px 5px 0px rgba(0, 0, 0, 0.14), 0px 1px 10px 0px rgba(0, 0, 0, 0.12);
          
            
        }
        .site-status-wrap .table th{
            font-weight: bold;
        }
        .site-status-wrap .table span{
            color: #fff;
            width: 100px;
            padding: 5px 10px;
            text-align: center;
            font-weight: bold;border-radius: 5px;
            display: inline-block;
        }
        /* START -- scrollbar style */
        ::-webkit-scrollbar {
        width: 5px;
        height: 16px;
        }
        ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 4px rgba(0,0,0,0.5); 
        border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 8px;-webkit-box-shadow: inset 0 0 4px rgba(0,0,0,0.4);
        }
        /* END -- scrollbar style */
        .online-wrap li:last-child {
         margin-bottom: 14px !important;
     }
     .fixedsideblock{
       
       padding: 5px;
       z-index: 200
   }
   .citydropdown{
       display: none;
   }
    </style>
@stop
