@extends('layouts.app')
@section('content')
<style>
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
</style>
    <div id="supervisor_panel">
        <div class="table_title">
            <h4>
                <?php $is_ssd = ((isset($shift_flag) && $shift_flag == 1) || isset($stc))
                ?false
                :true
                ?>
                @if(isset($shift_flag) && $shift_flag == 1)
                    Guard Tour / Shift Journal
                @elseif(isset($stc))
                    STC Client Geomapping
                @else
                    Site Status Dashboard
                    <div class="pull-right" style="font-size: 16px;">Average Score :
                        {{--<span id="tot-avg-span" class="customer-score"><span class="customer-map-score" id="total-avg"> -- </span></span>--}}
                        <span id="tot-avg-span" class="customer-score"><span id="total-avg"> -- </span></span>
                        <?php
                        $selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
                        if (!empty($selected_customer_ids)) {
                            echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary"> Reset Filter</button>';
                        }
                        ?>
                    </div>
                @endif

                @if(isset($shift_flag) && $shift_flag == 1 || isset($stc))
                    <?php
                    $selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
                    if (!empty($selected_customer_ids)) {
                        echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary pull-right"> Reset Filter</button>';
                    }
                    ?>
                @endif
            </h4>

        </div>
        <div id="wrapper" class="toggled siderbar-panel">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <div class="clearfix"></div>
                    @if (!isset($stc)  && isset($shift_flag) && $shift_flag != 1 )
                        <button class="btn btn-primary blue actual p-41 ssd-cb" id="actual" data-dismiss="modal"
                                aria-hidden="true">
                            Actual
                        </button>
                        <button class="btn btn-primary blue ytd p-41 ssd-cb" id="ytd" data-dismiss="modal" aria-hidden="true">
                            YTD
                        </button>
                        {{-- <i class="fa fa-sort-down" onclick="sorting()" aria-hidden="true"></i>
                         <i class="fa fa-sort-up" aria-hidden="true"></i> --}}
                    @endif
                    <input type="text" id="searchbox" class="form-control search-input" placeholder="Search">
                    <div class="second-child"></div>

                    <div id="candidate-data-left-panel">
                        <table class="<?php echo $is_ssd ? 'ssd-table':''?> table">
                            <tbody>
                            @if(isset($customer_score))

                                @foreach($customer_score as $i=>$customer)

                                    <tr>
                                        <td>
                                        <?php  $in = ucwords($customer['customer']['details']['client_name']) ?>
                                        @if($shift_flag==0)
                                            @if($customer["customer"]["details"]["stc"] == PERMANENT_CUSTOMER)
                                                <a onmouseover="openInfoWindow('cust-{{$customer['customer']['details']['id']}}');"
                                                    href="{{ route('customer.details',$customer['customer']['details']['id']) }}"
                                                    class="ssd-text foa1 hideHover" id="customer-details" data-id="{{$customer['customer']['details']['id']}}">{{ strlen($in) > 38 ? substr($in,0,35)."..." : $in }}</a>
                                            @else
                                                <a onmouseover="openInfoWindow('cust-{{$customer['customer']['details']['id']}}');"
                                                    href="javascript:void(0)"
                                                    class="ssd-text foa2 hideHover"
                                                    id="customer-details"
                                                    data-id="{{$customer['customer']['details']['id']}}"
                                                    >{{ strlen($in) > 38 ? substr($in,0,35)."..." : $in }}</a>
                                            @endif
                                        @else
                                        <a onmouseover="openInfoWindow({{$customer['customer']['details']['id']}});"
                                                href="{{ route('customer.guardTourDetails',$customer['customer']['details']['id']) }}"
                                                class="ssd-text foa3 hideHover" id="customer-details" data-id="{{$customer['customer']['details']['id']}}">{{ strlen($in) > 38 ? substr($in,0,35)."..." : $in }}</a>
                                        @endif
                                        </td>

                                        <td class="js-ssd-pts-{{$i}}"
                                        style="min-width:43px; text-align:center;"></td>

                                        @if (!isset($stc) &&  ($shift_flag==0))
                                          <td style="min-width: 43px; text-align:center; padding:0px; vertical-align:middle;">
                                                <div class="filter_checkbox atl">
                                                    <input type="checkbox" name="atl"
                                                        id=chk-atl{{$customer['customer']['details']['id']}}
                                                                value={{($customer['score_details']['score']['total'] == -1)? -1:$customer['score_details']['score']['total'] }}
                                                                class=largerCheckbox
                                                        {{$i}} data-customerid="{{$customer['customer']['details']['id']}}" data-liclass="{{$i}}"
                                                        >
                                                </div>

                                                <div class="filter_checkbox ytdl">
                                                    <input type="checkbox" name="ytdl"
                                                        id=chk-ytdl{{$customer['customer']['details']['id']}}
                                                                value={{($customer['score_details']['score_1']['total']== -1)? -1: $customer['score_details']['score_1']['total'] }}
                                                                class=largerCheckbox
                                                        {{$i}}
                                                        data-customerid="{{$customer['customer']['details']['id']}}">
                                                </div>
                                            </td>
                                        @else
                                           <td>
                                            <i style="color:{{$customer['score_details']['color_class']['total']}} !important;"
                                            class="fa fa-map-marker float-right location-arrow" aria-hidden="true"> </i>
                                            </td>

                                        @endif
                                    </tr>
                                @endforeach
                                <tr class='notfound' style="display:none;">
                                    <td colspan='3'>No Customers to list</td>
                                </tr>
                            @else
                            <tr>
                                <td colspan='3'>No Customers to list</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <div class="mapping mapping-ie mapping-site-dashboard" {{--style="margin-left: 90px; left: -90px"--}}>
                <a class="navbar-brand" href="#menu-toggle" id="menu-toggle"><i class="fa fa-caret-left fa-2x"
                                                                                aria-hidden="true"></i></a>
            </div>
        </div>
        @can('view_employees_live_status')
        @if(isset($shift_flag) && $shift_flag == 1)

        @elseif(isset($stc))

        @else
        <!------wrapper for shift live status   -->

        <div id="wrapper-right" class="toggled siderbar-panel_right " style="height: 74.4%;">
            <!-- Sidebar -->
            <div id="sidebar-wrapper-right" style="overflow-y: hidden; overflow-x: hidden;">
                <div class="ls-heading">
						<i><img src="{{ asset('images/Live-Status-Icon.png') }}"></i> <span>Live Status </span>@if(!empty($request->shift_customerid)) <a href="#" id="show-live"><img src="{{ asset('images/Maximize.png') }}" alt=""></a>@endif
				</div>
                <div  class="ls-content height-100" style="">
                    <?php $repository = app()->make(\Modules\Admin\Repositories\CustomerEmployeeAllocationRepository::class);
                    $customerIds = $repository->getAllocatedPermanentCustomers(\Auth::user());
                    $customers = \Modules\Admin\Models\Customer::orderBy('client_name')->findMany($customerIds);
                    ?>
                    <div class="ls-search">
                    <select class="form-control select2" id="client_id" name="client_id">
                        <option value="">Customers</option>
                        @foreach($customers as $id => $customer)
                        <option value='{{ $customer->id }}' @if(isset($shift_customerid) && $shift_customerid == $customer->id ) {{ 'selected' }} @endif>{{$customer->client_name}}</option>
                        @endforeach
                    </select>
                    </div>
                    <div  class="online-wrap">
                        <ul>
                        @if(!empty($request->shift_customerid))
                            @if(isset($employee_list) && !empty($employee_list))

                                @foreach($employee_list as $i=>$emp)
                                    <li>
                                    <?php if($emp['live_status'] == AVAILABLE) {
                                            $class="online";
                                        }elseif($emp['live_status'] == MEETING) {
                                            $class="sleep";
                                        }else {
                                            $class="offonline";
                                        }
                                    ?>
                                    <span class="{{$class}}"></span>  {{ ucwords($emp['first_name']) }}

                                    </li>
                                @endforeach
                            @else
                                <li>No Employees</li>
                            @endif
                        @else
                                <li>Please Select Customers</li>
                        @endif


                        </ul>
                    </div>
                </div>
            </div>
            <!-- /#sidebar-wrapper -->
            <div class="mapping-right mapping-ie-right mapping-site-dashboard pull-right" {{--style="margin-left: 90px; left: -90px"--}}>
            <a class="navbar-brand" href="#menu-toggle1" id="menu-toggle1"><i class="fa fa-caret-right fa-2x"                                                          aria-hidden="true"></i></a>
            </div>
        </div>
        <!------wrapper for shift live status   -->
        @endif
        @endcan


    @can('filter_supervisorpanel')
        @if(isset($project_number, $area_manager, $status, $region, $city, $industry_sector, $supervisor))
            <!-- Filter Start-->
                <div id="view-details" class="toggled filter-details" style="display: none">
                    <div id="sidebar-view-details" class="hide-vertical-scroll filter-border">
                        <h4 class="padding-top-20">Filter Criteria</h4>
                        @if (!isset($stc)  && isset($shift_flag) && $shift_flag != 1 )
                            <div class="form-group row">
                                <label class="col-sm-4 col-md-4 col-xs-12 float-left">Sort</label>
                                <div class="col-sm-8 col-md-8 col-xs-12 float-left" id="filter">
                                    <form action="{{ route('customers.mapping') }}" method="GET" id="sort_asc_desc">
                                        {{ Form::hidden('sort_order',null,array('class'=>'form-control sort_order')) }}
                                        {{ Form::hidden('sort_param','actual',array('class'=>'form-control default_button')) }}
                                        <label><input type="submit" data-id="0" class="sort-link sort"
                                                      value="Ascending"></label>
                                        <label><input type="submit" data-id="1" class="sort-link sort"
                                                      value="Descending"></label>
                                    </form>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('id'=>'filtering-form','method'=>'GET')) }}
                        <input type="hidden" id="customer_id" name="customer_id" value="">
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">Project Number</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('project_number',[''=>'Please Select'] + $project_number,old('project_number',$request->get('project_number')),array('class'=>'form-control client-select dropdown-search')) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">Area Manager</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('area_manager',[''=>'Please Select'] + $area_manager,old('area_manager',$request->get('area_manager')),array('class'=>'form-control client-select dropdown-search')) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">Status</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('status',[''=>'Please Select'] + $status,old('status',$request->get('status')),array('class'=>'form-control client-select dropdown-search')) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">Region</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('region',[''=>'Please Select'] + $region,old('region',$request->get('region')),array('class'=>'form-control client-select dropdown-search')) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">City</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('city',[''=>'Please Select'] + $city,old('city',$request->get('city')),array('class'=>'form-control dropdown-search')) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">Industry Sector</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('industry_sector',[''=>'Please Select'] + $industry_sector,old('industry_sector',$request->get('industry_sector')),array('class'=>'form-control dropdown-search')) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-md-4 col-xs-12 float-left">Supervisor</label>
                            <div class="col-sm-8 col-md-8 col-xs-12 float-left">
                                {{ Form::select('supervisor',[''=>'Please Select'] + $supervisor,old('supervisor',$request->get('supervisor')),array('class'=>'form-control dropdown-search')) }}
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="text-center margin-bottom-5">
                            <button type="submit" class="btn submit">Filter</button>
                            <input type="hidden" value="1" name="filtering">
                            <button type="reset" class="btn submit reset">Reset</button>
                        </div>
                        {{ Form::close() }}

                    </div>
                </div>
                <!-- Filter End-->
        @endif
    @endcan

    <!-- Site Status -->

    @can('view_employees_live_status')
    <div class="ls-overlay" id="live_popup" style="display:none">
        <div class="site-status-wrap">
            <div class="ls-heading"> Site Status <a href="#" class="float-right" id="hide-live"><img src="{{ asset('images/Close.png') }}" width="19" height="19" alt=""/></a></div>
        <div class="ls-content">
        @if(isset($today_shift_details) && count($today_shift_details) > 0)

            <table class="table">
            <thead>
                <tr>
                    <th style="width:10px"># </th>
                    <th>Employee Details</th>
                    <th>Status</th>
                    <th>Shift Start </th>
                    <th>Shift End </th>
                    <th>Notes </th>
                </tr>
                </thead>
                <tbody>
                @foreach($today_shift_details as $i=>$shifts)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ data_get($shifts->shift_payperiod->user,'name_with_emp_no') }}</td>
                    @if($shifts->live_status_id == AVAILABLE)
                    <td><span class="online">Available</span></td>
                    @elseif($shifts->live_status_id == MEETING)
                    <td><span class="sleep">Meeting</span></td>
                    @else
                    <td><span class="offonline">Unavailable</span></td>
                    @endif
                    <td>{{ date('F j, Y g:i A', strtotime($shifts->start)) }}</td>
                    <td>
                        @if(isset($shifts->end) && !empty($shifts->end))
                            {{ date('F j, Y g:i A', strtotime($shifts->end)) }}
                        @else
                            -------
                        @endif

                    </td>
                    <td>
                        @if(isset($shifts->latest_meeting_note->note) && !empty($shifts->latest_meeting_note->note) && $shifts->live_status_id == MEETING)
                            {{ $shifts->latest_meeting_note->note }}
                        @else
                            -------
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>

            </table>
        @else
            <p align="center">No Shift Found</p>
        @endif
        </div>
        </div>
    </div>

    @endcan
<!-- Close Site Status -->

    <!-- /#wrapper -->
        <div class="embed-responsive embed-responsive-4by3">
            <div id="map" style="min-height:335px;" class="embed-responsive-item">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d
                i n g . . . . . .
            </div>
        </div>



    </div>
@stop
@section('scripts')
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>
    <script type="text/javascript">
        $('.select2').select2();
        $(document).ready(function () {
                    @isset($sort)
            var sort = ("{!!($sort) !!}");
            $('.' + sort).click();
            @endisset
        });

        /* Dropdown change jquery - Start */

        $("#client_id").change(function (e) {
           e.preventDefault();
           var customerid = this.value;
           var url='{{ route("customers.mapping") }}';
           params='';
           if(customerid)
           {
            params="?shift_customerid="+customerid;
           }
           window.location.href = url + params;
        });

                /* Dropdown change jquery  - End */
        $(".sort").unbind().click(function () {
            $(".sort_order").val($(this).data('id'));
        });
                @isset($customer_score)
        var customer_score = {!! json_encode($customer_score) !!};

        @endisset

        $(".actual").unbind().click(function () {
            //alert('actual');
            $(".atl").show();
            $(".ytdl").hide();

            /*Filter checkbox function Start*/

            $('input:checkbox[name=atl]').change(function () {
                var sum = 0;
                var avg = '';

                $("input:checkbox[name=atl]:checked").each(function () {
                    var val = this.checked ? this.value : '';

                    if(val != -1){
                    sum = sum + parseFloat(val);
                    //console.log($('[name="atl"][value!=-1]:checked').length);
                    //avg = sum / $('[name="atl"]:checked').length;
                    avg = sum / $('[name="atl"][value!=-1]:checked').length;
                    }
                });

                if(avg === ''){
                    var avg_score = '----';
                }else{
                    var avg_score = Dec2(avg);
                }
                $('#total-avg').text(avg_score);
                getAverageColor(avg_score);
            }).trigger("change");

            let score_html = '';
            $(".ytd").css('background-color')
            $(".default_button").val('actual');
            $(".ytd").addClass('btn-primary blue')
            $(this).css('background-color', '#f26222').removeClass('btn-primary blue');

            @isset($customer_score)
            $.each(customer_score, function (i, customer) {
                var color_codes = customer.score_details.color_class.total;

                var scores = (customer.score_details.score.total != '-1') ? Dec2(customer.score_details.score.total) : '--';

                score_html =
                    '<span  class="pts-txt font-color-' + color_codes + '">' +scores +'</span>';
                 if(scores =="--")
                 {
                    score_html =
                    '<span class="pts-txt font-color-' + color_codes + '">' +scores +'</span>';
                 }
                 $(".js-ssd-pts-" + i).css({
                     'background-color':color_codes
                 })
                $(".js-ssd-pts-" + i).html(score_html);
            });
            @endisset
        });

        $(".ytd").off().on('click',function () {
            //alert('ytd');
            $(".atl").hide();
            $(".ytdl").show();

            /*Filter checkbox function Start*/
            $('input:checkbox[name=ytdl]').change(function () {
                var sum = 0;
                var avg = '';
                $("input:checkbox[name=ytdl]:checked").each(function () {
                    var val = this.checked ? this.value : '';

                    if(val != -1){
                    sum = sum + parseFloat(val);
                    avg = sum / $('[name="ytdl"][value!=-1]:checked').length;
                    }
                });
                if(avg === ''){
                    var avg_score = '----';
                }else{
                    var avg_score = Dec2(avg);
                }

                $('#total-avg').text(avg_score);
                getAverageColor(avg_score);
            }).trigger("change");

            /*End of filter checkbox function*/

            $(".default_button").val('ytd');
            $(".actual").css('background-color')
            $(".actual").addClass('btn-primary blue')
            $(this).css('background-color', '#f26222').removeClass('btn-primary blue');
            var cls_enable='customer-map-score1';
            @isset($customer_score)

            $.each(customer_score, function (i, customer) {
                var color_code = customer.score_details.color_class_1.total;
                var cls_enable='customer-map-score';
                var score = (customer.score_details.score_1.total != '-1') ? Dec2(customer.score_details.score_1.total) : '--';
                score_html =
                    '<span  class="pts-txt font-color-' + color_code + '">' +
                    score +
                    '</span>'
                 $(".js-ssd-pts-" + i).css({
                     'background-color':color_code
                 })
                $(".js-ssd-pts-" + i).html(score_html);
            });
            @endisset
        });
        @isset($colors)
         var colors = {!! json_encode($colors) !!};
        @endisset
       /* function for getting the average color*/
         function getAverageColor(average) {
             var itemColor = 'black';
             colors.forEach(function(item){
                 if(item.min_value <= average && item.max_value >= average){
                    itemColor = item.color.color_class_name;
                    return;
                 }
             });

          $('#tot-avg-span').removeClass();
             $('#tot-avg-span').css({"background-color": itemColor, "padding": "8px 16px"});
            $('#tot-avg-span').addClass("font-color-" + itemColor)

         }

        /*end of function average color*/

        //Convert to 2 decimal places
        function Dec2(num) {
            if(typeof num == 'string'){
                num = parseFloat(num);
            }
            return num.toFixed(2);
        }

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
            lat = Number("{{(!empty($customer_score[0]['customer']['details']['geo_location_lat']))?$customer_score[0]['customer']['details']['geo_location_lat']:''}}");
            long = Number("{{(!empty($customer_score[0]['customer']['details']['geo_location_long']))?$customer_score[0]['customer']['details']['geo_location_long']:''}}");
            if (lat != 0 && long != 0) {
                var mapCenter = {lat: lat, lng: long};
            } else {
                var mapCenter = getLocationCoordinate("{{ $customer_score[0]['customer']['details']['postal_code'] }}");
                mapCenter = (mapCenter === null) ? ({
                    lat:{{config('globals.map_default_center_lat')}},
                    lng: {{config('globals.map_default_center_lng')}}}) : mapCenter;
            }
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: mapCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            });

            var customer = {!! json_encode($customer_score) !!};

            customer_rating = {!! json_encode($customer_rating) !!};
            customer_rating_color = {!! json_encode($customer_rating_color) !!};
            var view_url = "{{ route('customer.details',':id') }}";
            var details_url = "{{ route('customer.guardTourDetails',':id') }}";


            $.each(customer, function (i, item) {
                var customer = item.customer.details;
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

                var client_name = camelcase(customer.client_name);
                var address = camelcase(customer.address) + '<br/>' + camelcase(customer.city) + ', ' + customer.postal_code.trim() + ', ' + customer.province.trim();
                var contact_person_name = (customer.contact_person_name) ? camelcase(customer.contact_person_name) : '--';
                var contact_person_phone = (customer.contact_person_phone) ? customer.contact_person_phone : '--';
                contact_person_phone += (customer.contact_person_phone_ext) ? ' x' + customer.contact_person_phone_ext : '';
                var client_email = (customer.contact_person_email_id) ? customer.contact_person_email_id : '--';
                var site_rating = item.score_details.score.total;
                var site_rating_color = item.score_details.color_class.total;
                var site_rating_desc = (site_rating == {{SURVEY_DEFAULT_SCORE}}) ? "--" : customer_rating[site_rating];

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
                info_html += '<div class="row map-row"  style="padding-bottom: 5px; margin-bottom: 10px; border-bottom: 1px solid rgba(0, 0, 0, .1)"> <div id="load-supervisor" class="col-md-6 col-xs-12 col-sm-6 col-lg-6">';

                if (customer.stc && !shift_flag) {
                    @can('add-stc-rating')
                        info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label"><label class="btn btn-primary" id="rate-site" style="width:auto !important;" >Rate this site</label></div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"></div></div>';
                    @endcan
                }
                info_html += '</div><div id="load-areamanager" class="col-md-6 col-xs-6 col-sm-6 col-lg-6">';

                if (shift_flag != 1) {
                    info_html += '<div class="row" id="last_update"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Last Update</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + ((item.last_update) ? (item.last_update) : '--') + '</div></div>';
                }
                if (customer.stc && !shift_flag) {
                    info_html += '<div class="row"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Site Rating</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc rating-star-' + site_rating_color + '">' + site_rating_desc + '</div></div>';
                    info_html += '</div></div>';
                    @can('add-stc-rating')
                        info_html += '<div class="row map-row" id="rating"> <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">';
                    info_html += '<input type="hidden" id="customer-id" value="' + customer_id + '">'
                    info_html += '<div class="row rating-question"> <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-label">How do you rate this site</div><div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-disc"><select class="form-control" name="rating" id="select-rating"><option selected id="rating-star-0" value=0>Choose the rating</option>';
                    $.each(customer_rating, function (key, value) {
                        info_html += '<option value=' + key + ' class="rating-star-' + customer_rating_color[key] + '" data-rating-color="' + customer_rating_color[key] + '">&#xf005; ' + value + '</option>';
                    });
                    info_html += '</select></div></div>';
                    info_html += '<div class="row"> <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-label"></div><div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-disc"><small class="help-block" id="rating-errors"></small></div></div>';
                    info_html += '<div class="row rating-question"> <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-label">Please explain your rating</div><div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-disc"><textarea class="form-control" name="notes" id="notes" placeholder="Maximium 256 characters" rows="4" required></textarea/></div></div>';
                    info_html += '<div class="row"> <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-label"></div><div class="col-md-6 col-lg-6 col-xs-12 col-sm-12 map-disc"><small class="help-block" id="notes-errors"></small></div></div>';
                    info_html += '<div class="row"> <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 map-label"><input class="btn btn-primary" id="submit" type="submit" value="Save"><input class="btn btn-primary" id="cancel-rating" type="reset" value="Cancel"></div></div>';
                    info_html += '</div></div>'
                    @endcan
                            @can('view-stc-rating')
                            info_html += '<div id="load-rating"></div>'
                    @endcan
                } else {
                    if (shift_flag != 1) {
                        info_html += '</div></div>';
                        info_html += '<div class="row map-row"> <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">';
                        info_html += '<div class="row" id="loading_message_'+customer_id+'">Please Wait....</div><div class="row"><div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 map-legend-style">';
                        info_html += '</div></div>';
                        info_html += '<div class="row operational-dashbaord" id="dashboard_test_view_'+customer_id+'"></div><div class="row"><div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 map-legend-style">';
                        info_html += '</div></div>';
                    }
                }
                locations.push({
                    customerId: customer_id,
                    latlng: position,
                    info: info_html,
                    icon: '{{config('globals.markers')}}/' + item.score_details.color_class.total + '-dot.png'
                });

            });

            var infowindow = new google.maps.InfoWindow({
                maxWidth: 800
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
            //        infowindow.close(); //Todo: need to findout why we are closing this infowindow  
                }
            });


            var marker, i, contentString;

            function filterMarkers() {
                //reset all markers in the map
                for (var key in markers) {
                    markers[key].setMap(null);
                }
                markers = [];
                var operationalDashboard = [];
                var selectedIds = [];
                var flag = 0;
                var customerMoreDetails = [];
                //skipp all items for first time

                $('.largerCheckbox:checkbox:checked').each(function () {
                    //alert($(this).data('customerid'));
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
                                //fetch Operational Details
                                let distance_details = "";
                                var customerId = locations[i].customerId;
                                getCustomerMoreDetails(customerId);
                                if(typeof(operationalDashboard[customerId]) === 'undefined'){
                                     operationalDashboard[customerId] = [];
                                     operationalDashboard[customerId]['data'] = null;
                                     operationalDashboard[customerId]['pending'] = 0;
                                }
                                if((operationalDashboard[customerId]['data'] != null))
                                {
                                    $('#dashboard_test_view_'+customerId).html(operationalDashboard[customerId]['data']);
                                    $('#loading_message_'+customerId).hide();

                                }
                                else if (operationalDashboard[customerId]['pending'] == 0){
                                    operationalDashboard[customerId]['pending'] = 1;
                                    // console.log(operationalDashboard[customerId]['status']);
                                        $.ajax({
                                        data: {'id':customerId},
                                        type: 'GET',
                                        url: "{{route('customers.score.list')}}",
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        global: false,
                                        success: function (data)  {
                                            var html = '';
                                            if(data.success){
                                                if(data.content.length>0){
                                                html+= '<h6 class="text-center pt-2 pb-2"  style="color: #343F4E; margin-bottom: 5px !important;"><b>Operational Dashboard</b></h6>';
                                                html+= '<table class="table period-report table-bordered m-0 p-0"><thead><tr>';
                                                $.each(data.payperiods, function( index, value ) {
                                                    html+= '<th style="color: white; background-color: #343F4E;text-align:left;padding:2px;margin:2px;padding-top:7px;padding-bottom:3px;padding-left: 5px;font-size: 13px;">'+value+'</th>';
                                                });
                                                html+= '</tr></thead><tbody>';
                                                $.each(data.content, function( dataindex, eachList ) {
                                                    html+= '<tr><td class="text-capitalize" scope="row" style="text-align:left;color: white; background-color: #E65100;padding:2px;margin:2px;padding-top:3px;padding-right:25px;padding-left: 5px;font-size: 13px;"><span title="'+eachList+'">'+eachList+'</span></td>';
                                                        if(data.contentColors.length>0){
                                                            $.each(data.contentColors, function( index, value ) {
                                                                if(value.color_class != null && value.color_class[eachList]){
                                                                    html+= '<td align="center" class="bar-color-'+value.color_class[eachList]+'"></td>';
                                                                } else {
                                                                    html+= '<td align="center"style="background-color: black;"></td>';
                                                                }
                                                         });
                                                        }
                                                        html+= "</tr>";
                                                });
                                                html+= '</tbody></table>';
                                                }
                                                console.log($('#dashboard_test_view_'+customerId));

                                                    $('#dashboard_test_view_'+customerId).html(html);
                                                    $('#loading_message_'+customerId).hide();

                                            }else{
                                                $('#loading_message_'+customerId).hide();
                                                console.log($('#dashboard_test_view_'+customerId));
                                            }
                                            // $('#loading-message').css("display", "none");
                                            operationalDashboard[customerId]['data'] = html;
                                            operationalDashboard[customerId]['pending'] = 0;
                                        },
                                        error: function (error) {
                                            alert('error');
                                        }
                                    });
                                }
                            }
                        })(marker, i))
                        markers[locations[i].customerId] = marker;


                       // markers['cust-'+locations[i].customerId] = marker;

                        //markers.push(marker);
                    }
                }
            }

            filterMarkers();

            $('.largerCheckbox').on('change', function () {
                var id= $(this).data('customerid');

                filterMarkers(); //alert('chec - '+id);
              // openInfoWindow('cust-'+id);
               openInfoWindow('cust-'+id);


            });

            /* Rating store - Start*/
            google.maps.event.addListener(infowindow, 'domready', function () {

                $('#rating-panel').hide();

                /*Rate button jquery - Start*/
                $("#rate-site").on('click', function () {
                    $('#select-rating').val(0);
                    $('#notes').val('');
                    $('#rate-site,#rating-panel,#comments,#display-rating').hide();
                    $('#rating').show();
                    //map.panBy(0, -200);
                });
                /*Rate button jquery - End*/

                /* Cancel button jquery - Start */
                $("#cancel-rating").on('click', function () {
                    $('#select-rating').val(0);
                    $('#notes').val('');
                    $('#rating-panel,#rating').hide();
                    $('#rate-site,#comments,#display-rating').show();
                    $('#display-rating').find('i').removeClass('fa-minus').addClass('fa-plus');
                   // map.panBy(0, 200);
                });
                /* Cancel button jquery - End */



                /* Rating panel toggle - Start */
                $("#display-rating").on('click', function () {
                    if ($(this).find('i').hasClass('fa-plus')) {
                       // map.panBy(0, -300);
                        $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
                    } else {
                        //map.panBy(0, 300);
                        $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
                    }
                    $('#rating-panel').toggle();

                });
                /* Rating panel toggle - End */

                var previous_value = "";
                /* Rating select element - Start */
                $("#select-rating").change(function () {
                    if (previous_value !== "") {
                        $('#select-rating').removeClass('rating-star-' + previous_value);
                    }
                    var current_value = $(this).find("option:selected").data('rating-color');
                    $('#select-rating').addClass('rating-star-' + current_value);
                    previous_value = current_value;
                });
                /* Rating select element - End */

                /* Rating submit - Start */
                $('#submit').click(function () {
                    $formdata = {
                        'rating_id': $("#select-rating").val(),
                        'notes': $("#notes").val(),
                        'customer_id': $("#customer-id").val(),
                    };
                    $("#rating-errors").text('');
                    $("#notes-errors").text('');
                    $.ajax({
                        data: $formdata,
                        type: 'POST',
                        url: "{{route('customers.rating.store')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            swal({title: "Saved", text: "Rating has been saved", type: "success"},
                                function () {
                                    $('#rating').hide();
                                    $('#rate-site').show();
                                    location.reload();
                                }
                            );
                        },
                        error: function (data) {

                            errors = data.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                if (key == "rating_id") {
                                    $("#rating-errors").text(value[0]);
                                } else if (key == "notes") {
                                    $("#notes-errors").text(value[0]);
                                } else {

                                }
                            });
                        },
                    });
                });
                /* Rating submit - End */
            });
            /* Rating store - End*/

                //   $(".hideHover").mouseover(function(){
                //     // infowindow.setContent(contentString);
                //     var customerIds = $(this).attr('data-id');
                //        $.ajax({
                //         data: {'id':customerIds},
                //         type: 'GET',
                //         url: "{{route('customers.score.list')}}",
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         },
                //         global: false,
                //         success:(data) =>   {
                //             // $('body').loading('stop');
                //             contentString+= data
                //             infowindow.setContent(contentString);
                //         },
                //         error: function (data) {
                //             errors = data.responseJSON.errors;
                //             $.each(errors, function (key, value) {
                //                 if (key == "rating_id") {
                //                     $("#rating-errors").text(value[0]);
                //                 } else if (key == "notes") {
                //                     $("#notes-errors").text(value[0]);
                //                 } else {

                //                 }
                //             });
                //         },
                //     });

                //     });

        }

        @endisset

        function initEmptyMap(myCenter) {
            var logo = '<img src="{{ asset("images/short_logo.png") }}">';
            var locations = [];
            var mapProp = {center: myCenter, zoom: 8, mapTypeId: google.maps.MapTypeId.ROADMAP};
            var map = new google.maps.Map(document.getElementById('map'), mapProp);
        }

        function openInfoWindow(id) {
            google.maps.event.trigger(markers[id], 'click');
        }


        function getCustomerMoreDetails(id){
            $.ajax({
                     data: {'id':id},
                     type: 'GET',
                     url: "{{route('customers.more-details')}}",
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     global: false,
                     success: function (data)  {
                    if (data) {
                         var supervisor_html = '';
                         var areamanager_html = '';
                         var rating_html = '';

                if($("#more-details-".id).length == 0) {
                       $(".chng").remove();                      
                        var supervisor = data.supervisor;
                        var areamanager = data.areamanager;
                        var rating = data.rating_details;
           
                        var supervisor_name = '--';
                        var phone ='--';
                        var email ='--';
                        var alter_email ='--';
                        var area_manager_name = '--';
                        var area_manager_phone = '--';
                        var area_manager_email = '--';
                        if(supervisor != null){
                        supervisor_name = (supervisor.full_name) ? camelcase(supervisor.full_name) : '--';
                        phone = (supervisor.phone) ? supervisor.phone : '--';
                        phone += (supervisor.phone_ext) ? ' x' + supervisor.phone_ext : '';
                        email = (supervisor.email) ? ((supervisor.email == null) ? '--' : supervisor.email) : '--';
                        alter_email = (supervisor.alternate_email) ? ((supervisor.alternate_email == null) ? '--' : supervisor.alternate_email) : '--';
                        }
                        if(areamanager != null){
                        area_manager_name = (areamanager.full_name) ? camelcase(areamanager.full_name) : '--';
                        area_manager_phone = (areamanager.phone) ? areamanager.phone : '--';
                        area_manager_phone += (areamanager.phone_ext) ? ' x' + areamanager.phone_ext : '';
                        area_manager_email = (areamanager.email) ? areamanager.email : '--';
                        }
                        supervisor_html += '<div id="more-details-'+id+'" class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label chng">Supervisor</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + supervisor_name + '</div></div>';
                        supervisor_html += '<div class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Phone</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + phone + '</div></div>';
                        supervisor_html += '<div class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + email + '</span></div></div>';
                        supervisor_html += '<div class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Alternate Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + alter_email + '</span></div></div>';
                    
                        $("#load-supervisor").prepend(supervisor_html);
           
                        areamanager_html += '<div class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Area Manager</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc ">' + area_manager_name + '</div></div>';
                        areamanager_html += '<div class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Phone</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">' + area_manager_phone + '</div></div>';
                        areamanager_html += '<div class="row chng"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Email</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc email-break"><span>' + area_manager_email + '</span></div></div>';
                    //    areamanager_html += '<div class="row" id="last_update"> <div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label">Last Update</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc">ddd</div></div>';
                        $("#load-areamanager").prepend(areamanager_html);
           
                        if (rating.length > 0) {
                                rating_html += '<div class="row"> <div class="col-md-2 col-lg-2 col-xs-12 col-sm-12 map-label" id="comments">Comments:</div><div class="col-md-10 col-lg-10 col-xs-12 col-sm-12 map-disc email-break" id="display-rating"><i class="fa fa-plus display-rating" aria-hidden="true"></i></div></div>';
                                rating_html += '<div class="row map-row" id="rating-panel"> <div id="load-rating" class="col-md-12 col-xs-12 col-sm-12 col-lg-12">';
                                $.each(rating, function (key, value) {
                                    last_name = value.user != null ? ((value.user.last_name != null) ? ' ' + value.user.last_name : '') : '--';
                                    if (rating.length > 0) {
                                        rating_html += '<div id="each-rating">';
                                        rating_html += '<div class="row" id="rating-details"><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc comments-font">' + (value.user != null ? uppercase(value.user.roles[0].name.replace('_', ' ')) : '--') + ' : ' + (value.user != null ? value.user.first_name : '--') + last_name + '/' + (value.user != null ? value.user.trashed_employee.employee_no : '--') + '</div><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-disc comments-font">Date and Time: ' + datetime(value.created_at) + '</div></div>';
                                        rating_html += '<div class="row"><div class="col-md-5 col-lg-5 col-xs-12 col-sm-12 map-label rating-star comments-font rating-star-' + customer_rating_color[value.rating_id] + '">&#xf005; ' + customer_rating[value.rating_id] + '</div><div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 map-disc comments-font"> - ' + value.notes + '</div></div>';
                
                                        rating_html += '</div>';
                                    }
                                });
                                rating_html += '</div></div>'
                               $("#load-rating").html(rating_html);
           
                        }
           
                     }
                    } 
                },
                error: function (error) {
                  alert('error');
                }
             });
        }
        $(function () {

            //$('.dropdown-search').select2();
            @if(isset($customer_score))
            initMap();
            @else
            initEmptyMap(new google.maps.LatLng('43.6532', '-79.3832'));
            @endif
            $("#menu-toggle").click(function (e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
                $("#view-details,.filter-details").css("display", "none");
            });
            $("#menu-toggle1").click(function (e) {
                e.preventDefault();
                $("#wrapper-right").toggleClass("toggled");
            });
            $("#filter-view").click(function () {
                $(".filter-details").toggleClass("toggled");
                $(".filter-details").css("display", "block");
            });
            // $("#filter-view1").click(function () {
            //     $(".filter-details").toggleClass("toggled");
            //     $(".filter-details").css("display", "block");
            // });

            $.expr[':'].contains = function (a, i, m) {
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
            //$('.notfound').show();
            //}
            });
            // $(".search-input").click(function () {
            //     $(".filter-details").toggleClass("toggled");
            //     $(".filter-details").css("display", "block");
            // });
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
                $('.filter_checkbox').hide();
                $(this).closest('form').find("input[type='text']").val("");
                $(this).closest('form').find("select").prop('selectedIndex', 0);
            });
        });

        $(window).bind("load", function () {
            $('#sidebar').css('height', $(window).height() - 70);
            $('#content-div').css('height', $(window).height() - 70);
            $('#content-div').css('overflow', 'hidden');
        });

        /*---------------Site Status Pop UP-----------------*/
		$("#show-live").click(function(){
  			$(".ls-overlay").show();
		});
		$("#hide-live").click(function(){
  			$(".ls-overlay").hide();
        });

        const sd = {
            init(){
                let args = globalUtils.uraQueryParamToJson(window.location.href);
                let cids = globalUtils.decodeFromCsv(args.cIds);
                cids.forEach(function(id){
                   $('.largerCheckbox').each(function(i,el){
                       if($(el).data('customerid') == id){
                           $(el).trigger('click');
                       }
                   });
                });
            }
        }
        $(function() {
            sd.init();
        });

        //window.history.pushState("object or string", "Title", "plot-in-map");
    </script>
    <style type="text/css">
    .operational-dashbaord{
        padding-bottom: 15px;
    }
    .operational-dashbaord span{
        display: inline-block;
        width: 75px;
        white-space: nowrap;
        overflow: hidden !important;
        text-overflow: ellipsis;
    }
    #dashboard-test-view{
        padding-bottom: 20px;
    }
    .table-bordered th td{
        padding: 0px;
        margin: 0px;
        height: 50px;
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
