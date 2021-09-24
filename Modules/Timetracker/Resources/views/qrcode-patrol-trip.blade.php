@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
.gj-datepicker-md {
    line-height: 1;
    color: rgba(0,0,0,.87);
    width: 140 !important;
    position: relative;
}
</style>
@section('content')
<div class="table_title">
    <h4>QR Patrol</h4>
</div>
<div class="row" style="padding-bottom:10px">
<div class="col-md-3">
            <div class="row">
                <div class="col-md-3"><label class="filter-text">Customer</label></div>
                <div class="col-md-7 filter">
                {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
                <span class="help-block"></span>
                </div>
            </div>
        </div>

    <div class="col-md-3 employee-filter employee-filter-main">
     <div class="row">
     <div class="col-md-3"><label class="filter-text">Employee</label></div>
        <div class="col-lg-7 filter">
            <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-name-filter">
                <option value="0">Select Employee</option>
                    @foreach($employeeLookup as $each_userlist)
                    <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                    </option>
                     @endforeach
            </select>
            <span class="help-block"></span>
            </div>
      </div>
   </div>
        <div class="col-md-1 col-lg-1">
        Start Date
        </div>
        <div class="col-md-1 col-lg-1" style="margin-left:-50px;">
            <input type="text" name="fromdate" id="fromdate"  class="form-control datepicker" value="{{date("Y-m-d",strtotime("-2 day",strtotime(date("Y-m-d"))))}}" />
        </div>
        <div class="col-md-1  col-lg-1" style="margin-left:30px;">
        End Date
            </div>
            <div class="col-md-1 col-lg-1" style="margin-left:-60px;">
                <input type="text" name="todate" id="todate"  class="form-control datepicker" value="{{date("Y-m-d")}}" />
            </div>
        <div class="col-md-1 col-lg-1" style="margin-left:30px;">
            <button class="form-control button btn submit" id="filterbutton" name="filterbutton" type="button" >Search</button>
        </div>
    </div>


<div id="message"></div>
{{-- @include('timetracker::payperiod-filter') --}}
<table class="table table-bordered" id="qrcode_patrol_table">
    <thead>
        <tr>
            <th>#</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Date</th>
            <th>Employee No.</th>
            <th>Name</th>
            <th>Project</th>
            <th>Project Name</th>
            <th>Scanned</th>
            <!-- <th>Missed</th> -->
            <!-- <th>Avg</th> -->


        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>
    $(function() {
        loadDatatable();
    });

   $("#filterbutton").on('click',function(event){
        var fromdate= $("#fromdate").val();
        var todate= $("#todate").val();
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
         $('#qrcode_patrol_table').DataTable().ajax.reload();
        }

     });

     $(".select2").select2()
     var loadDatatable = function(e){
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#qrcode_patrol_table').DataTable({
                bProcessing: false,
                responsive: false,
                dom: 'Blfrtip',
                buttons: [

                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,

                ajax: {
                    "url":'{{ route('qrcodepatrol.trips') }}',
                    "data": function(d) {d.fromdate = $("#fromdate").val(),d.todate = $("#todate").val(), d.client_id=$("#clientname-filter").val(), d.employee_id=$("#employee-name-filter").val();},
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
               // order: [[3, 'desc']],
               lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
                columns: [{
                        data: 'shift_id',
                        render: function(o) {
                            return '<button data-id="'+o.shift_id+'" class="btn fa fa-plus-square buttons"></button>';
                        },
                        orderable: false,
                        className: 'details-control',
                        data: null,
                        defaultContent: ''

                    },
                {data: 'start', name: 'start'},
                {data: 'end', name: 'end'},
                {data: 'created_at', name: 'created_at'},
                {data: 'employee_no', name: 'employee_no'},
                {data: 'first_name',  name:'first_name'},
                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {data: 'scanned', name: 'scanned'},
                // {data: 'missed', name: 'missed'},
                // {data: 'avg', name: 'avg'},


            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        $(".client-filter").change(function(){
            table = $('#qrcode_patrol_table').DataTable();
            table.ajax.reload();
        });

        $("#employee-name-filter").change(function(){
            table = $('#qrcode_patrol_table').DataTable();
            table.ajax.reload();
        });

           // Add event listener for opening and closing details
     /*   $('#qrcode_patrol_table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {

                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square "></button>');
                row.child.hide();
                tr.removeClass('shown');
                refreshSideMenu();
            }
            else {

                tr.find('td.details-control').html('<button  class="btn fa fa-minus-square "></button>');
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
                refreshSideMenu();
            }

        } ); */

        $('#qrcode_patrol_table tbody').on('click', 'td.details-control', function() {
            var id=$(this).closest('tr').find('.buttons').data('id');
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square buttons" data-id=' + id + '></button>');
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                var view_url = '{{ route("qrcodepatrol.details",":shift_id") }}';
                view_url = view_url.replace(':shift_id', id);
                $.ajax({
                type: 'GET',
                url: view_url,
                dataType: 'json',
                success: function (data) {
                   tr.find('td.details-control').html('<button  class="btn fa fa-minus-square buttons"  data-id=' + id + '></button>');
                   row.child( format(data)).show();
                   tr.addClass('shown');
                  },
                error: function () {}
                });

            }
            refreshSideMenu();
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

    }



/* Formatting function for row details - modify as you need */
function format (d) {
    console.log(d.qrcode_details);
    var html= '';
    var location= '';

    if(d.qrcode_details){
        $.each(d.qrcode_details,function(key,item){
            if(item.latitude !=''){
             location = '<a class="fa fa-lg fa-map-marker view_map" href="javascript:void(0);" id="'+item.id+'"></a>';
            }else{
             location ='--';
            }

                if(item.image.length > 0){
                    var image= '';
                 $.each(item.image, function(keys,value){
                    var url = '{{route('filedownload', [':attachment_id','qr-patrol'])}}'
                    url = url.replace(':attachment_id', value);
                    image += '<a href="'+url+'" target="_blank">' + '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 7px;"></i>' +'</a>';
                  });
                }else{
                    image ='--';
                }


            html +='<tr"><td>'+item.created_at+'</td><td>'+item.start_time+'</td><td>'+item.end_time+'</td><td>'+item.check_point+'</td><td align="center">'+image+'</td><td align="center">'+location+'</td><td>'+item.comments+'</td></tr><tr class="border_bottom hide-this-block" id="trip_'+item.id+'"><td colspan=4> <div class="w3-container" style="margin: 10px; padding: 5px;> <div class="clearfix"></div><div class="col-sm-6"><label  style="width:100%;"><div class="col-sm-6"></td><td colspan=4 class="test"><iframe src="mapview/'+item.id+'" style="float:left;display:inline-block;height:200px;width:500px"></iframe></div></div></td></tr>';
        });
    }else{
        html +='<tr><td colspan="7" align="center">No Data Found</td></tr>';
    }
    return '<table  class="DataTable subtable">'+
        '<tr><td width="10%"><b>Date</b></td><td width="10%"><b>Start Time</b></td><td width="10%"><b>End Time<b></td><td width="15%"><b>Checkpoint</b></td><td align="center"><b>Image</b></td><td align="center"><b>Location</b></td><td width="25%" ><b>Comments</b></td></tr>'+
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
</style>
@stop
