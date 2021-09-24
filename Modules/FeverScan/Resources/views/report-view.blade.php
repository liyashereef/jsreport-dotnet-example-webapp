@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>Fever Reports</h4>
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
    <table class="table table-bordered" id="fever_scan_table">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                
                <th>Gender</th>
                <th>Age</th>
                <th>Province</th>
                <th>City</th>
                <th>Temperature</th>
                <th>Notes</th>
                <th>Created Date</th>
    
            </tr>
        </thead>
    </table>
</div>
</div>

@stop
@section('scripts')

<script>
       $(function() {
            callDataTable();
    
            $("#searchbutton").click(function() { 
                callDataTable();
            });
            
        });

        function callDataTable(){
           
            $.fn.dataTable.ext.errMode = 'throw';
                try {

                    var view_url = '{{ route("fever-reading-report-data") }}';                    

                    if ( $.fn.dataTable.isDataTable( '#fever_scan_table' ) ) {
                        $('#fever_scan_table').DataTable().ajax.reload();
                    }
                    else {
                    
                   var table = $('#fever_scan_table').DataTable({
                        bProcessing: false,
                        responsive: true,
                        dom: 'Blfrtip',
               
               buttons: [{
                     extend: 'pdfHtml5',
                     pageSize: 'A2',
                 },
                 {
                     extend: 'excelHtml5',
                 },
                 {
                     extend: 'print',
                     pageSize: 'A2',
                 }
             ],
                        processing: true,
                        serverSide: true,
                        fixedHeader: true,
                        ajax: {
                            "url": view_url,
                            data: function(data) {
                                data.from_date = $('#fromdate').val();
                                data.to_date = $('#todate').val();
                            },
                            "error": function (xhr, textStatus, thrownError) {
                                if (xhr.status === 401) {
                                    window.location = "{{ route('login') }}";
                                }
                            }
                        },
                        lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                        ],
                        columns: [
                            {
                                data: 'id',
                                render: function (data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                },
                                orderable: false
                            },
                           
                            {
                                data:null,render:function(o){
                                   return o.customer.project_number +' - '+ o.customer.client_name
                                    orderable: false
                                },
                                name:'customer.client_name'
                            },
                           
                            { data: 'gender', name: 'gender' },
                            { data: 'age_group', name: 'age_group' },
                            { data: 'province', name: 'province' },
                            { data: 'city', name: 'city' },
                            { data: 'temperature', name: 'temperature' },
                            { data: 'notes', name: 'notes' },
                            { data: 'created_at', name: 'created_at' },
                                        
                        ]
                    });
                }
                } catch (e) {
                    console.log(e.stack);
                }

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
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop

