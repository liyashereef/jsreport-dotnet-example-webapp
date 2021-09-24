@extends('layouts.app')

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>Satellite Tracking</h4>
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
        <div class="col-md-3"><label class="filter-text employee-filter-text">Employee</label></div>
        <div class="col-md-7 filter employee-filter-align">
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

       <input type="hidden" name="cacheddata" id="cacheddata" value="0" />
        <div class="col-md-1" style="text-align:right">
            From
        </div>
        <div class="col-md-1">
            <input type="text" name="fromdate" id="fromdate"  class="form-control datepicker" value="{{date("Y-m-d",strtotime("-5 day",strtotime(date("Y-m-d"))))}}" />
        </div>
        <div class="col-md-1" style="text-align:right">
                To
            </div>
            <div class="col-md-1" style="width: 100%;">
                <input type="text" name="todate" id="todate"  class="form-control datepicker" value="{{date("Y-m-d")}}" />
            </div>
        <div class="col-md-1" style="margin-left: 1em;">
            <button class="form-control button btn submit" id="filterbutton" name="filterbutton" type="button" >Search</button>
        </div>

</div>
<div id="message"></div>
{{-- @include('timetracker::payperiod-filter') --}}
<table class="table table-bordered" id="geofence-table">
    <thead>
        <tr>
            <th></th>
            <th>Start Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>End Date</th>
            <th>Employee Name & No</th>
            <th>Project No</th>
            <th>Project Name</th>
            <th>Total Visits</th>
            <th>Missed</th>
            <th>Average</th>
        </tr>
    </thead>
</table>


<!-- Modal -->
<div class="modal fade" id="fenceSummaryExtraDetails" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="exampleModalLongTitle">Fence Details (<span class="js-fence-name fence-title"></span>)
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body fence-details-section"></div>
        </div>
    </div>
</div>

@stop
@section('scripts')
<script>

    $(function() {
        $("#cacheddata").val("0");
        loadDatatable();
    });

    $(".select2").select2()

    $("#filterbutton").on('click',function(event){
        $("#cacheddata").val("0");
        // dataTable();
        var limit = 10;
       // order[0] = {"column":0,"dir":"asc"};
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
         $('#geofence-table').DataTable().ajax.reload();
         $("#cacheddata").val("1");
        }

     });

    var loadDatatable = function(e){

        $.fn.dataTable.ext.errMode = 'hide';
        let dateFormat = 'DD-MMM-YY';
        let timeFormat = 'h:mm A';
        try {
            table = $('#geofence-table').DataTable({
                bProcessing: false,
                responsive: false,
                dom: 'Blfrtip',
                buttons: [

                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('msp.geofence.list') }}",
                    "data": function(d) {d.fromdate = $("#fromdate").val(),d.todate = $("#todate").val(),d.cacheddata = $("#cacheddata").val(), d.client_id=$("#clientname-filter").val(), d.employee_id=$("#employee-name-filter").val();},
                    "error": function(xhr, textStatus, thrownError) {}
                },
                // order: [[3, 'desc']],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [{
                        data: 'shift_id',
                        render: function(o) {
                            return '<button data-id="'+o.id+'" class="btn fa fa-plus-square buttons"></button>';
                        },
                        orderable: false,
                        className: 'details-control',
                        data: null,
                        defaultContent: ''

                    },
                    {
                        data: 'start',
                        render: function(o) {
                            return moment(o).format(dateFormat);
                        },
                    },
                    {
                        data: 'start',
                        render: function(o) {
                            return moment(o).format(timeFormat);
                        },
                    },
                    {
                        data: 'end',
                        render: function(o) {
                            return moment(o).format(timeFormat);
                        },
                    },
                    {
                        data: 'end',
                        render: function(o) {
                            return moment(o).format(dateFormat);
                        },
                    },
                    {
                        data: 'shift_payperiod.trashed_employee',
                        sortable: false,
                        render: function(o) {
                            return `${o.trashed_user.full_name} (${o.employee_no})`;
                        },
                    },
                    {
                        data: 'shift_payperiod.trashed_customer.project_number'
                    },
                    {
                        data: 'shift_payperiod.trashed_customer.client_name'
                    },
                    {
                        data: 'geofence_meta.total_visits'
                    },
                    {
                        data: 'geofence_meta.missed'
                    },
                    {
                        data: 'geofence_meta.average',
                        render: function(o) {
                            return Number(o).toFixed(2);
                        },

                    },
                ]
            });
            $("#cacheddata").val("1");
        } catch (e) {
            console.log(e.stack);
        }

        $(".client-filter").change(function(){
            table = $('#geofence-table').DataTable();
            table.ajax.reload();
        });
        $("#employee-name-filter").change(function(){
            table = $('#geofence-table').DataTable();
            table.ajax.reload();
        });

        //show geolocation summary table
        $('#geofence-table tbody').on('click', 'td.details-control', function() {
            var id=$(this).closest('tr').find('.buttons').data('id');
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square buttons" data-id=' + id + '></button>');
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                var view_url = '{{ route("msp.geofence.summary",":shift_id") }}';
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

    }

    $('body').on('click', '.geofence-summary-child', function(e) {
        var self = this;
        $.ajax({
                   type: "get",
                   url: "{{route('msp.geofence.datalist')}}",
                   data: {"shiftid":$(this).data('shift-id'),"fenceid":$(this).data('fence-id')},
                   success: function (response) {

                    let data = btoa(JSON.stringify(response[0]));
                    let fenceId = $(self).data('fence-id')
                    let fenceName = response[1];
                    let av = $(self).data('av');
                    let result = geoFenceSummaryChildTable({
                        data,
                        fenceId
                    });
                    let fenceDetails = $('#fenceSummaryExtraDetails');
                    fenceDetails.find('.modal-body').html(result);
                    fenceDetails.find('.js-fence-name').html(fenceName);
                    //set fence name in modal body
                    fenceDetails.modal('show');
                   }
               });

    });

    function geoFenceSummaryChildTable(input) {

        var html = '';
        let data = JSON.parse(atob(input.data));
        $.each(data, function(key, item) {
            //skip entries have invalid date
            if(!item.time_entry || !item.time_exit){
                return;
            }
            //verify data is correct
            if (Number(input.fenceId) === Number(item.fence_id)) {
                html += `
                <tr>
                    <td>${moment(item.time_entry).format('DD-MMM-YY h:mm:ss A')}</td>
                    <td>${moment(item.time_exit).format('DD-MMM-YY h:mm:ss A')}</td>
                    <td>${moment.utc(item.duration*1000).format('HH:mm:ss')}</td>
                </tr>
                `;
            }
        });

        return `
            <div class="table-responsive">
                <table  class="table table-striped">
                    <thead>
                        <tr>
                            <th>Enter Time</th>
                            <th>Exit Time</th>
                            <th>Visited Time</th>
                        </tr>
                    </thead>
                    <tbody class="child_elements">${html}</tbody>
                </table>
            </div>
            `;
    }

    function format(data) {
        var html = '';
        $.each(data, function(key, gfs) {
            let infoButton='<i class="fa fa-lg fa-info-circle fac-disabled" aria-hidden="true"></i>';
            let shift_id=gfs.shift_id;
            if(gfs.visit_count_actual){

                infoButton = `
                        <a class="geofence-summary-child"
                        href="javascript:void(0)"
                        data-fence-id="${gfs.fence_id}"
                        data-fence-name="${gfs.fence_name}"
                        data-shift-id ="${shift_id}"
                        data-info='${btoa(JSON.stringify(gfs.shift_fence_datas))}'
                        >
                            <i class="fa fa-lg fa-info-circle " aria-hidden="true"></i>
                        </a>
                `;
            }

            html += `
                <tr>
                    <td>${infoButton}</td>
                    <td>${gfs.fence_trashed.title}</td>
                    <td>${gfs.fence_trashed.address}</td>
                    <td>${gfs.visit_count_expected}</td>
                    <td>${gfs.visit_count_actual}</td>
                    <td>${gfs.visit_count_missed}</td>
                    <td>${moment.utc(gfs.hours_average*1000).format('HH:mm:ss')}</td>
                </tr>
            `;
        });

        return `<table  class="table table-geofence-info">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Location Name</th>
                            <th>Address</th>
                            <th>Expected Visits</th>
                            <th>Actual Visits</th>
                            <th>Missed</th>
                            <th>Average Visit Time</th>
                        </tr>
                    </thead>
                <tbody class="child_elements">${html}</tbody>
            </table>`;
    }
</script>

<style type="text/css">
    #content-div {
        padding-bottom: 60px;
    }

    table td ,table th{
        text-align: center !important;
    }

    .location_name {
        margin-bottom: 0;
    }

    .table-geofence-info thead th {
        background: #1f5e8c;
    }

    .table-geofence-info tbody td {
        background: #efdedb;
        border: solid 1px #f1c7bf !important
    }
    .fence-title{
        font-style: italic;
        font-size: 16px;
    }
    .fence-details-section table th {
        background-color: #fdd5c3;
    }
    .fac-disabled{
        color: #a2a2a2 !important;
    }
    .gj-datepicker-md {
        line-height: 1;
        color: rgba(0,0,0,.87);
        width: 130 !important;
        position: relative;
    }
</style>
@stop
