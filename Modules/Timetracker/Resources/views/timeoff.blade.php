@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
<h4 align="left"> Time Off Requests </h4>
</div>
{{-- <div class="container"> --}}
<input type="hidden" name="baseurl" id="baseurl" value="{{url('/') }}" />
<table class="table table-bordered timesheet" id="timeoff-table">
    
    <thead>
        <tr>
            <th>#</th>
            <th>Project No</th>
            <th>Employee Name </th>
            <th>Pay Rate</th>
            <th>Start Date</th>
            <th>Start Time</th>
            <th>End Date</th>
            <th>End Time</th>
            <th>Reason</th>
            <th>Backfill Status</th>
        </tr>
    </thead>
</table>
{{-- </div> --}}
@stop




@section('scripts')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#timeoff-table').DataTable({
                bProcessing: false,
                processing: true,
               // fixedHeader: true,
                responsive: false,
                scrollX: true,
                pageLength: 10,
                initComplete:function(settings, json ){ 
                    $(".initiatebackfill").on("click",function(e)
                    {
                        var customer= $(this).attr("att-customer");
                        var timeoff_id= $(this).attr("att-val");
                        var url = '{{ route("timeoff.timeoffBackFill", ":customer_id/:requirement_id") }}';
                        url = url.replace(':customer_id', customer);
                        url = url.replace(':requirement_id', timeoff_id);
                        
                        window.location.href=url;
                    }
                    )},
                ajax: {
                        "url":"{{ route('timeoff.timeoff_requests') }}",
                        "data": function ( d ) {
                        },
                        "error": function (xhr, textStatus, thrownError) {
                            if(xhr.status === 401){
                                window.location = "{{ route('login') }}";
                            }
                        }
                    },
                dom: 'Blfrtip',
                buttons: [
                {
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o'
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-print'
                    },
                    ],
                    "columnDefs": [
                    {
                         className: "nowrap",
                         "targets": [ 7,8]
                    }],
                    
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    // order: [
                    // [0, 'desc']
                    // ],
                    lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                    columns: [
                    
                        {
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'project_number',
                        name: 'Project Number'
                    },
                    {
                        data: null,
                        name:'user',render:function(o){
                            
                            return o.user.first_name +  " " + o.user.last_name+"("+o.employee.employee_no+")";
                        }
                    },
                    {
                        data: null,
                        name:'Pay rate',
                         render:function(o){
                            if(o.cpid_rate==null){
                                return "Not defined";
                            }else{
                                return o.cp_idlabel+"($"+o.cpid_rate.p_standard+")";
                            }
                         }
                    },
                    {
                        data: 'start_date',
                        name: 'Start Date'
                    },
                    {
                        data: 'start_time',
                        name: 'Start Time'
                    },
                    {
                        data: 'end_date',
                        name: 'End date'
                    },
                    {
                        data: 'end_time',
                        name:'End Time'
                    },
                    {
                        data: null,
                        name:'reasons',render:function(o){
                            
                            return o.reasons.request_type;
                        }
                    },
                    {
                        data: null,
                        name:'backfillstatus',render:function(o){
                            var baseurl = $("#baseurl").val();
                            var requirementstatus = o.requirementstatus;
                            if(requirementstatus==2){
                                return '<a class="btn btn-primary">Backfill scheduled</a>';
                            }else{
                                if(o.customer.deleted_at!=null){
                                if(o.backfillstatus==0){
                                    return '<a class="btn btn-primary">Customer not found</a>';
                                }else{
                                    return '<a class="btn btn-primary">Backfill scheduled</a>';
                                }
                                }else{
                                    if(o.backfillstatus==0){
                               
                                        return '<a att-val="'+o.id+'" att-customer="'+o.customer.id+'" class="initiatebackfill btn btn-primary" style="cursor:pointer;text-align:center">Initiate Backfill</a>';
                                    }else{                                       
                                        return '<a att-val="'+o.id+'" att-customer="'+o.customer.id+'" class="btn btn-primary" href="'+baseurl+'/hranalytics/candidate/schedule/customer/'+o.project_id+'/'+o.backfillstatus+'" style="cursor:pointer;text-align:center">Backfill requested</a>';
                                     
                                    }
                                }
                            }
                            
                            
                            
                        }
                       
                    }
                   ]
                 });

        }catch(e){
            console.log(e.stack);
        }
    });

    
</script>
<script src="{{asset('js/auto-refresh.js')}}"></script>
@stop
