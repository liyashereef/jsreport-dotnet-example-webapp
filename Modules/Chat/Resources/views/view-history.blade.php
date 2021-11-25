@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
<style>
    #table-id .fa {
        margin-left: 11px;
    }
</style>
@stop
@section('content')
<div class="table_title">
    <h4>Chat History </h4>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$project_list,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>

<table class="table table-bordered" id="chat-table">
     <thead>
         <tr>
             <th class="sorting" width="10%">Employee Name</th>
             <th class="sorting" width="15%">Message</th>
             <th class="sorting" width="15%">Date</th>
             <th class="sorting" width="15%">Time</th>

         </tr>
     </thead>
 </table> 


@stop
@section('scripts')
<script>
    $(function () {


        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#chat-table').DataTable({
              bProcessing: false,
            responsive: true,
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('chat.view-history.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {
                    data: 'from',
                    name: 'from'
                },
                {
                    data: 'text',
                    name: 'text'
                },

                {data: 'date', name: 'date'},
                {data: 'time', name: 'time'}

            ]
        });
        } catch(e){
            console.log(e.stack);
        }
    });

</script>


@stop
