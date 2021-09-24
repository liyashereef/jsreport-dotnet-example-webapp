@extends('layouts.app')
@section('title', 'Allocation Report')
@section('content')
<div class="table_title">
    <h4>Allocation Report</h4>
</div>


<div id="message"></div>
@include('timetracker::payperiod-filter')
<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th>#</th>
            <th>Employee Id</th>
            <th>Employee Name</th>
            <th>Role</th>
            <th>Reports To</th>
            <th>From</th>
            <th>To</th>
            <th>Project Number</th>
            <th>Client</th>
            <!--<th>Created Date</th>
            <th>Modified Date</th>-->
        </tr>
    </thead>
</table>
<textarea id="emailMessage" style="display: none;"></textarea>
@stop
@section('scripts')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            $('.select2').select2();
        table = $('#table-id').DataTable({
            bProcessing: false,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        stripNewlines: false,
                        format: {
                            body: function ( data, rowIdx , columnIdx) {
                                if(columnIdx == 7 || columnIdx == 8){
                                    data = data.replace(/<br>/g, "\r\n");
                                    return data;
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'excelHtml5',
                    //text: ' ',
                    //className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        stripNewlines: false,
                        format: {
                            body: function ( data, rowIdx , columnIdx) {
                                if(columnIdx == 7 || columnIdx == 8){
                                    data = data.replace(/<br>/g, "\r\n");
                                    return data;
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    //text: ' ',
                    pageSize: 'A2',
                    //className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        stripNewlines: false,
                        format: {
                            body: function ( data, rowIdx , columnIdx) {
                                if(columnIdx == 7 || columnIdx == 8){
                                    data = data.replace(/<br>/g, "\r\n");
                                    return data;
                                }
                                return data;
                            }
                        }
                    }
                },
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: {
                "url":'{{ route('timetracker.getAllocationReport') }}',
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
                {data: 'employee_no', name: 'employee_no'},
                {data: 'name', name: 'name'},
                {data: null, name: 'role',
                    render: function(data, type, row, meta) {
                        return uppercase(data.role.replace('_', ' '));
                    },
                },
                {data: 'supervisor', name: 'supervisor'},
                {data: 'from', name: 'from'},
                {data: 'to', name: 'to'},
                {data: 'employee_shift_payperiods[ <br>].project_number',name:'employee_shift_payperiods.0.project_number'},
                {data: 'employee_shift_payperiods[ <br>].client_name',name:'employee_shift_payperiods.0.client_name'},
            ]
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
