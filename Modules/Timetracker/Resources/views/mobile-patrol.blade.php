@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
    <h4>Mobile Security Patrol</h4>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
<div id="message"></div>
{{-- @include('timetracker::payperiod-filter') --}}
<table class="table table-bordered" id="mobile_patrol_table">
    <thead>
        <tr>
            <th>#</th>
            <th>Project Number</th>
            <th>Customer</th>
            <th>Reported By</th>
            <th>Employee Number</th>
            <th>Date</th>
            <th>Time</th>
            <th>Subject</th>
            <th>Notes</th>



        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>
    $(function () {
        $('.select2').select2();
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#mobile_patrol_table').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'Blfrtip',
                buttons: [

                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url":'{{ route('mobilepatrol.list') }}',
                    "data": function ( d ) {
                        d.payperiod = $("#payperiod-filter").val();
                        d.client_id = $("#clientname-filter").val();
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
                {data: 'customer', name: 'customer'},
                {data: 'reported_by', name: 'reported_by'},
                {data: 'employee_number', name: 'employee_number'},
                {data: 'created_at', name: 'created_at'},
                {data: 'created_time', name: 'created_time'},
                {data: 'subject',  name:'subject'},
                {data: 'description',  name:'description'},

            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        $("#payperiod-filter").change(function(){
            table.ajax.reload();
        });
    });

    $(".client-filter").change(function(){
            var  table = $('#mobile_patrol_table').DataTable();
            table.ajax.reload();
        });



</script>
@stop
