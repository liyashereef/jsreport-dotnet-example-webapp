@extends('layouts.app')
@section('content')

<style>
    #candidate-data-left-panel li:last-child {
        margin-bottom: 17px;
    }
    .round-name {
    font-size: 13px;
    padding-left: 36px;
    color:#f48452;
    }
    .border-top{
    border-top: 1px solid rgba(0, 0, 0, 0.44) !important;
    margin-bottom: 10px;
    }
    .title-style{
        color:#f48452 !important;
        padding-right:4px !important;padding-left:4px !important;
    }
    .data-style{
        color:#1164bd !important;
        padding-right:4px !important;padding-left:4px !important;
    }
    .total-style{
        color: #111 !important;
        padding-right:4px !important;padding-left:4px !important;
    }
    .split-line{
        border-bottom: 1px solid rgba(0, 0, 0, .1) !important;
        margin-bottom: 10px;
    }
</style>

    <div id="supervisor_panel">
        <div class="table_title">
            <h4>
            Fever Scan Dashboard
            </h4>

        </div>
        <div id="wrapper" class="toggled siderbar-panel">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
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
                                            <input type="checkbox" name="atl"
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

            <!-- Filter Start-->
            {{ Form::open(array('id'=>'filtering-form','method'=>'POST')) }}

            <div id="view-details" class="toggled filter-details" style="display: none;padding:10px">
                <div id="sidebar-view-details" class="hide-vertical-scroll filter-border">
                    <h4 class="padding-top-20">Filter Criteria</h4>


                <input type="hidden" id="customer_id" name="customer_id" value="">

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

                var customer_list = {!! json_encode($customers) !!};
                var agegroup = {!! json_encode($agegroup) !!};
                var start_date = {!! json_encode($start_date) !!};
                var end_date = {!! json_encode($end_date) !!};

                $.each(customer_list, function (i, item) {
                    var customer = item
                    var position = {
                        lat: parseFloat(customer.geo_location_lat),
                        lng: parseFloat(customer.geo_location_long)
                    };

                    // var project_number =  customer.project_number;
                    var customer_id = customer.id;
                    // var client_name = camelcase(customer.client_name);
                    // var address = camelcase(customer.address) + '<br/>' + camelcase(customer.city) + ', ' + customer.postal_code.trim() + ', ' + customer.province.trim();
                    var info_html = '';
                  

                    locations.push({
                        customerId: customer_id,
                        start_date: start_date,
                        end_date: end_date,
                        latlng: position,
                        info: info_html,
                        icon: '{{config('globals.markers')}}/green-dot.png'
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
                        // alert($(this).data('customerid'));
                        selectedIds.push($(this).data('customerid'));
                    });

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
                                icon: locations[i].icon,
                                customerId:locations[i].customerId
                            });

                            google.maps.event.addListener(marker, 'click', (function (marker, i)  {
                                return function () {
                                    var formdata = {
                                        'customer_id': locations[i].customerId,
                                        'start_date': locations[i].start_date,
                                        'end_date': locations[i].end_date
                                    };
                                    $.ajax({
                                         data: formdata,
                                         type: 'GET',
                                         url: "{{route('customer-fever-reading')}}",
                                         headers: {
                                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                         success: function (data) {
                                             info_html = data;
                                             contentString = getContentString(i,data);
                                             infowindow.setContent(contentString);
                                             infowindow.open(map, marker);
                                             map.setCenter(marker.getPosition());
                                         },
                                         error: function (data) { },
                                    });
                                }
                            })(marker, i))
                            markers[locations[i].customerId] = marker;
                        }
                    }
                }

                function getContentString(i, data) {
                    let contentString =
                        '<div id="content" style="min-width:0px;" class="map-tooltip">' +
                        '<h4 id="firstHeading" class="firstHeading firstHeading-left">' + logo + '&nbsp;' + '</h4>' +
                        '<div id="bodyContent">' +
                        '<label style="width:93% !important;padding-left:32px !important;">'
                        + data.replace(/\n/g, "<br />")
                        + '</label>' +
                        '</div>' +
                        '</div>';
                    return contentString;
                }

                filterMarkers();

                $('.largerCheckbox').on('change', function () {
                    var id= $(this).data('customerid');

                    filterMarkers(); //alert('chec - '+id);
                // openInfoWindow('cust-'+id);
                openInfoWindow('cust-'+id);


                });

                $("#province").trigger("change");


                //For Marker Clusterer
                // var markerCluster = new MarkerClusterer(map, markers,
                //     {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});



        }
        $(".filterbutton").on("click",function(e){
           e.preventDefault();
           var url = "{{route('fever.site-view')}}";
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
        function initEmptyMap(myCenter) {
            var logo = '<img src="{{ asset("images/short_logo.png") }}">';
            var locations = [];
            var mapProp = {center: myCenter, zoom: 8, mapTypeId: google.maps.MapTypeId.ROADMAP};
            var map = new google.maps.Map(document.getElementById('map'), mapProp);
        }

        // Trigger Map click
        function openInfoWindow(id) {
            google.maps.event.trigger(markers[id], 'click');
        }


        $(function () {
            initMap();
            //$('.dropdown-search').select2();
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



        });

        $(window).bind("load", function () {
            $('#sidebar').css('height', $(window).height() - 70);
            $('#content-div').css('height', $(window).height() - 70);
            $('#content-div').css('overflow', 'hidden');
        });


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

       if($("#province").val()==""){
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
    </style>
@stop
