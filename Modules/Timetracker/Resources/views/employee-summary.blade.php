@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>Employee Summary Report</h4>
</div>
<div id="message"></div>
@include('timetracker::payperiod-filter')
<table class="table table-bordered table-responsive" id="table-id">
    <thead>
        <tr>
            <th>#</th>
            <th>Project Number</th>
            <th>Client</th>
            <th>Employee Id</th>
            <th>Role</th>
            <th>Employee Name</th>
            <th>Pay Period</th>
            <th>Pay Period Start</th>
            <th>Pay Period End</th>
            <th>Over Time</th>
            <th>Stat</th>
            <th>Total Hours by Employee</th>
            <th>Modified Hours</th>
            <th>Client Approved Over Time?</th>
            <th>Client Approved Stat?</th>
            <th>Approved By</th>
            <th>Approved Date & Time Stamp</th>
        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            $('.select2').select2();
            table = $('#table-id').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'Blfrtip',
                buttons: [
                {
                    extend: 'pdfHtml5',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-file-pdf-o'
                },
                {
                    extend: 'excelHtml5',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-file-excel-o'
                },
                {
                    extend: 'print',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-print'
                },
                {
                    //extend: 'email',
                    text: 'Email',
                    //className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Employee Summary Report');
                    }
                }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url":'{{ route('timetracker.getEmployeeSummaryReport') }}',
                    "data": function ( d ) {
                        d.payperiod = $("#payperiod-filter").val();
                        d.from_date = $("#from_date").val();
                        d.to_date = $("#to_date").val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [[0, 'desc']],
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
              
                {
                    
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                {data: 'project_number', name: 'project_number'},
                {data: 'client_name', name: 'client_name'},
                {data: 'employee_no', name: 'employee_no'},
                {data: null, name: 'role', 
                render: function (data, type, row, meta) {
                            return uppercase(data.role.replace('_', ' '));
                        },
                        },
                { data: 'full_name',  name:'full_name',
            },
            {data: 'pay_period_name', name: 'pay_period_name'},
            {data: 'start_date', name: 'start_date'},
            {data: 'end_date', name: 'end_date'},
            
            {data: 'total_overtime_hours', name: 'total_overtime_hours'},
            {data: 'total_statutory_hours', name: 'total_statutory_hours'},
            {data: 'total_work_hours', name: 'total_work_hours'},
            {data: 'total_hours_employee', name: 'total_hours_employee'},
            {
                data: 'client_approved_billable_overtime',
                name: 'client_approved_billable_overtime',
            },
            {
                data: 'client_approved_billable_statutory',
                name: 'client_approved_billable_statutory',
            },
            { data: 'approved_by_full_name',

            name:'approved_by_full_name',
        },

        {data: 'updated', name: 'updated'},
        ]
    });
    table.on('draw', function () {
            refreshSideMenu();
    });
        } catch(e){
            console.log(e.stack);
        }

        $("#table-id_wrapper").addClass("no-datatoolbar datatoolbar");

        $("#payperiod-filter").change(function(){
            $("#from_date").val('');
            $("#to_date").val('');
            table.ajax.reload();
        });
        
        $("#filterbutton").click(function(){
            if($("#from_date").val()!="" && $("#to_date").val()==""){
                swal("Warning", "End date cannot be null", "warning");
            }
            else if($("#from_date").val()=="" && $("#to_date").val()!=""){
                swal("Warning", "Start date cannot be null", "warning");
            }
            else if($("#from_date").val()>$("#to_date").val()!=""){
                swal("Warning", "End date cannot be less than Start date", "warning");
            }
            else{
                //$("#payperiod-filter").val('');
                table.ajax.reload();
            }
            
        });

        $("#from_date, #to_date").change(function(){
            $("#payperiod-filter").val('');
        });

        $("#resetbutton").click(function(){
            $("#payperiod-filter").val('');
            $("#from_date").val('');
            $("#to_date").val('');
            table.ajax.reload();
        });
        table.on('click', function () {
               refreshSideMenu();
        });
    });
    
</script>
@stop
