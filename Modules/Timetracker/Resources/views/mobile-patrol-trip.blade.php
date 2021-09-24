@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>Mobile Security Patrol Trips</h4>
</div>
<div id="message"></div>
<div class="row" style="padding-bottom:10px">
        <div class="col-md-2"></div>
        <div class="col-md-1">
            From
        </div>
        <div class="col-md-2">
            <input type="text" name="fromdate" id="fromdate"  class="form-control datepicker" value="{{date("Y-m-d",strtotime("-2 day",strtotime(date("Y-m-d"))))}}" />
        </div>
        <div class="col-md-1" style="text-align:center">
                To
            </div>
            <div class="col-md-2">
                <input type="text" name="todate" id="todate"  class="form-control datepicker" value="{{date("Y-m-d")}}" />
            </div>
        <div class="col-md-1">
            <button class="form-control button btn submit" id="searchbutton" name="searchbutton" type="button" >Search</button>
        </div>

</div>
<div class="row">
    <div class="col-md-12">
    <table class="table table-bordered" id="mobile_security_patrol_table">
        <thead>
            <tr>
                <th>#</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Date</th>
                <th>Emp No.</th>
                <th>Name</th>
                <th>Vehicle</th>
                <th>Project</th>
                <th>Project Name</th>
                <th>Plot</th>
                <th>Incident Reported</th>
                <th>Average Speed</th>
                <th>Total kms</th>

            </tr>
        </thead>
    </table>
</div>
</div>
{{-- @include('timetracker::payperiod-filter') --}}

@stop
@section('scripts')
<script>
    var dataTable = function(){
        $.fn.dataTable.ext.errMode = 'throw';

        $.fn.dataTable.pipeline = function ( opts ) {
    // Configuration options
    var conf = $.extend( {
        pages: 5,     // number of pages to cache
        url: '',      // script url
        data: null,   // function or object with parameters to send to the server
                      // matching how `ajax.data` works in DataTables
        method: 'GET' // Ajax HTTP method
    }, opts );

    // Private variables for storing the cache
    var cacheLower = -1;
    var cacheUpper = null;
    var cacheLastRequest = null;
    var cacheLastJson = null;

    return function ( request, drawCallback, settings ) {
        var ajax          = false;
        var requestStart  = request.start;
        var drawStart     = request.start;
        var requestLength = request.length;
        var requestEnd    = requestStart + requestLength;

        if ( settings.clearCache ) {
            // API requested that the cache be cleared
            ajax = true;
            settings.clearCache = false;
        }
        else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
            // outside cached data - need to make a request
            ajax = true;
        }
        else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                  JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                  JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
        ) {
            // properties changed (ordering, columns, searching)
            ajax = true;
        }

        // Store the request for checking next time around
        cacheLastRequest = $.extend( true, {}, request );

        if ( ajax ) {
            // Need data from the server
            if ( requestStart < cacheLower ) {
                requestStart = requestStart - (requestLength*(conf.pages-1));

                if ( requestStart < 0 ) {
                    requestStart = 0;
                }
            }

            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);

            request.start = requestStart;
            request.length = requestLength*conf.pages;

            // Provide the same `data` options as DataTables.
            if ( typeof conf.data === 'function' ) {
                // As a function it is executed with the data object as an arg
                // for manipulation. If an object is returned, it is used as the
                // data object to submit
                var d = conf.data( request );
                if ( d ) {
                    $.extend( request, d );
                }
            }
            else if ( $.isPlainObject( conf.data ) ) {
                // As an object, the data given extends the default
                $.extend( request, conf.data );
            }

            settings.jqXHR = $.ajax( {
                "type":     conf.method,
                "url":      conf.url,
                "data":     request,
                "dataType": "json",
                "cache":    false,
                "success":  function ( json ) {
                    cacheLastJson = $.extend(true, {}, json);

                    if ( cacheLower != drawStart ) {
                        json.data.splice( 0, drawStart-cacheLower );
                    }
                    if ( requestLength >= -1 ) {
                        json.data.splice( requestLength, json.data.length );
                    }

                    drawCallback( json );
                }
            } );
        }
        else {
            json = $.extend( true, {}, cacheLastJson );
            json.draw = request.draw; // Update the echo for each response
            json.data.splice( 0, requestStart-cacheLower );
            json.data.splice( requestLength, json.data.length );

            drawCallback(json);
        }
    }
};

// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    } );
} );

        try{
            table = $('#mobile_security_patrol_table').DataTable({
                bProcessing: false,
                responsive: false,
                dom: 'Blfrtip',
                buttons: [

                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,

                ajax: {
                    "url":'{{ route('mobilesecuritypatrol.trips') }}',
                    "data": function ( d ) {
                        d.fromdate=$("#fromdate").val();
                        d.todate=$("#todate").val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
               // order: [[3, 'desc']],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columnDefs: [
                             { "targets": [9,10,11,12],"className": "text-center", }
                            ],
                columns: [
                {
                    data: 'shift_id',
                    render: function (o) {
                        return '<button  class="btn fa fa-plus-square "></button>';
                        //return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    className:      'details-control',
                    data:  null,
                    defaultContent: ''

                },
                {data: 'start', name: 'start'},
                {data: 'end', name: 'end'},
                {data: 'created_at', name: 'created_at'},
                {data: 'employee_no', name: 'employee_no'},
                {data: 'first_name',  name:'first_name'},
                {data: 'vehicle', name: 'vehicle'},
                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var url = '{{ route("mobilesecuritypatrol.tripmapview",'') }}';
                        return '<a href="'+url+"/"+ o.shift_id +'" target="_blank" class="fa fa-map-marker"></a>'

                    },
                },
                {data: 'incident_reported', name: 'incident_reported'},
                {data: 'average_speed', name: 'average_speed'},
                {data: 'total_km', name: 'total_km'},

            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (parseInt(aData["average_speed_limit"]) < parseInt(aData["average_speed"])) {
                    $(nRow).find('td:eq(11)').css({backgroundColor: "red", color: "white", width: "5%" });
                }
             }
        });
        } catch(e){
            console.log(e.stack);
        }
    }

    $("#searchbutton").on('click',function(event){

       // dataTable();
       var limit = 10;
      // order[0] = {"column":0,"dir":"asc"};
       var fromdate= $("#fromdate").val();
       var todate= $("#todate").val();
       $length = $("#mobile_security_patrol_table_length").val();
       var table = $('#mobile_security_patrol_table').DataTable();
       if(fromdate == ""){
            swal("Alert", "From date cannot be empty", "warning");
       }
       else if(todate == ""){
            swal("Alert", "To date cannot be empty", "warning");
       }
       else if(fromdate > todate){
            swal("Alert", "From date cannot exceed To date", "warning");
       }
       else{
        $('#mobile_security_patrol_table').DataTable().ajax.reload();
       }

    });
    $(function () {


        dataTable();

           // Add event listener for opening and closing details
        $('#mobile_security_patrol_table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                // This row is already open - close it
                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square "></button>');
                row.child.hide();
                tr.removeClass('shown');
                refreshSideMenu();
            }
            else {
                // Open this row
                tr.find('td.details-control').html('<button  class="btn fa fa-minus-square "></button>');
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
                refreshSideMenu();
            }

        } );




        //$("#mobile_seurity_patrol_table_wrapper").addClass("no-datatoolbar datatoolbar");

        $("#payperiod-filter").change(function(){
            table.ajax.reload();
        });


            /*Showing map and details on next row*/
             $(document).on('click','.view_map',function(e) {
                if( $("#trip_"+$(this).attr('id')).hasClass('hide-this-block')){
                 $("#trip_"+$(this).attr('id')).removeClass('hide-this-block');
                }
                else
                {
                    $("#trip_"+$(this).attr('id')).addClass('hide-this-block');
                }

         });

    });



/* Formatting function for row details - modify as you need */
function format ( d) {
    // `d` is the original data object for the row
    var html= '';
    var speedLimitId = ''
    $.each(d.trip_details,function(key,item){
        if (parseInt(item.average_speed) > parseInt(d.average_speed_limit)) {
            var speedLimitId ='average-limit';
        }
        /*
            html +='<tr"><td>'+item.start_time+'</td><td>'+item.end_time+'</td><td>'+item.source+'<figure><img src="{{ asset('images/map_pointer.png') }}" alt="1" /><figcaption>'+item.source_count+'</figcaption></figure><b> ('+item.source_count+') </b> </td><td>'+item.destination+'<figure><img src="{{ asset('images/map_pointer.png') }}" alt="1" /><figcaption>'+item.destination_count+'</figcaption></figure><b> ('+item.destination_count+') </b></td><td>'+item.travel_time+'</td><td>'+item.total_km+'</td><td><a class="fa fa-map-marker view_map" href="javascript:void(0);" id="'+item.trip_id+'"></a></td></tr><tr class="border_bottom hide-this-block" id="trip_'+item.trip_id+'"><td colspan=4> <div class="w3-container" style="margin: 10px; padding: 5px;> <div class="clearfix"></div><div class="col-sm-6"><label  style="width:100%;"><span class="col-sm-7 col-7 float-left">Employee Name</span> <span class="col-sm-5 col-5 float-left">'+d.employee_name+'</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left ">Employee Number</span><span class="col-sm-5 col-5 float-left">'+d.employee_no+'</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Project Number</span> <span class="col-sm-5 col-5 float-left">'+d.project_number+'</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Client Name</span> <span class="col-sm-5 col-5 float-left">'+d.client_name+'</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Payperiod</span> <span class="col-sm-5 col-5 float-left">'+d.pay_period+'</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Date</span> <span class="col-sm-5 col-5 float-left">'+d.created_at+'</span><div class="clearfix"></label></div><div class="col-sm-6"></td><td colspan=4 class="test"><iframe src="mapview/'+item.trip_id+'" style="float:left;display:inline-block;height:200px;width:500px"></iframe></div></div></td></tr>';
        */
        html +='<tr"><td>'+item.start_time+'</td><td>'+item.end_time+'</td><td><div class="d-flex align-items-center"><p class="location_name">'+item.source+'</p><div class="location_marker"><span class="marker_font">'+item.source_count+'</span></div> </div></td><td><div class="d-flex align-items-center"><p class="location_name">'+item.destination+'</p><div class="location_marker"><span class="marker_font">'+item.destination_count+'</span></div></div></td><td class="text-center" id="'+speedLimitId+'">'+item.average_speed+'</td><td class="text-center">'+item.travel_time+'</td><td class="text-center">'+item.total_km+'</td><td class="text-center"><a class="fa fa-map-marker view_map" href="javascript:void(0);" id="'+item.trip_id+'"></a></td></tr><tr class="border_bottom hide-this-block" id="trip_'+item.trip_id+'"><td colspan=4> <div class="w3-container" style="margin: 10px; padding: 5px;> <div class="clearfix"></div><div class="col-sm-6"><label  style="width:100%;"><span class="col-sm-7 col-7 float-left">Employee Name</span> <span class="col-sm-5 col-5 float-left">'+d.employee_name+'</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left ">Employee Number</span><span class="col-sm-5 col-5 float-left">'+d.employee_no+'</span><div class="clearfix"></div><span class="col-sm-7 col-7 float-left">Project Number</span> <span class="col-sm-5 col-5 float-left">'+d.project_number+'</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Client Name</span> <span class="col-sm-5 col-5 float-left">'+d.client_name+'</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Payperiod</span> <span class="col-sm-5 col-5 float-left">'+d.pay_period+'</span><div class="clearfix"><span class="col-sm-7 col-7 float-left">Date</span> <span class="col-sm-5 col-5 float-left">'+d.created_at+'</span><div class="clearfix"></label></div><div class="col-sm-6"></td><td colspan=4 class="test"><iframe src="mapview/'+item.trip_id+'" style="float:left;display:inline-block;height:200px;width:500px"></iframe></div></div></td></tr>';
        //<a target="_blank" href="mapview/'+item.trip_id+'">View</a></td></tr>

    });

    return '<table  class="DataTable subtable">'+
        '<tr><b><td><b>Start Time</b></td><td><b>End Time<b></td><td><b>Starting Location</b></td><td><b>Destination</b></td><td class="text-center"><b>Average Speed</b></td><td class="text-center"><b>Travel time</b></td><td class="text-center"><b>Total kms</b></td><td class="text-center"><b>Map View</b></td></tr>'+
        '<tbody class="child_elements">'+html+
        '</tbody>'+
    '</table>';
}






</script>
<style type="text/css">
    #content-div {
        padding-bottom:60px;
    }

     .location_name
    {
        max-width:327px;
        min-width:327px;
        display:inline-block ;
    }
    .location_marker
    {
        width: 3rem;
        height: 3rem;
        display: inline-block;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-image: url({{ asset('images/map_pointer.png') }});
    }
    .marker_font
    {
        color:#fff;
        font-weight:bold
    }
    table td{
        vertical-align: middle !important;
    }
    .location_name{
        margin-bottom: 0;
    }
    #average-limit {
        background-color: red;
        color: white;
        width: 6%;


    }
</style>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop

