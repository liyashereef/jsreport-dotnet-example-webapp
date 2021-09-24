@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@section('content')
<div class="table_title">
        <div class="row">
            <div class="col-md-10"><h4>Contract Summary</h4></div> 
            @canany(['add_contracts'])
            <div class="col-md-2" style="text-align:right"><a class="button btn submit" href="{{route('contracts.cmuf-upload-form')}}">Add New Contract</a></div>
            @endcan
        </div>
    
</div>
<div id="message"></div>
@canany(['add_contracts','view_contracts'])
<div class="row candidate-screen-head" id="showfilter" style="cursor:pointer">
    
        <div class="col-md-11"><h5>Search</h5></div>
    
    
            <div class="col-md-1" style="text-align: right">
                    <i  class="fa fa-angle-double-down" style="font-size:30px;cursor:pointer"></i>
            </div>
    
</div>
<form id="filterform" name="filterform" method="post">
<div class="col-lg-12 dropdown-adjust" id="employee-filter-div" style="display:none;" >
        <div class="row " style="padding-bottom:10px">
                <div class="col-md-2" style="padding-top:10px">Customer Name or Number</div> 
                <div class="col-md-3">
                        <div class="row">
                        <select name="customername" id="customername" value="" class="form-control" style="">
                            <option attr-project_number="0" value="0">Select</option>
                            @foreach ($lookups['customers'] as $key=>$value)
                                        <option attr-project_number="{{$value->project_number}}" value="{{$value->project_number}}">{{$value->project_number}}  - {{$value->client_name}}</option>
                                    @endforeach
                        </select>
                        </div>
                </div>
                <div class="col-md-1" style="padding-top:10px">Manager</div> 
                <div class="col-md-2">
                        <div class="row">
                        <select name="regionalmanager" id="regionalmanager" value="" class="form-control" style="margin-top:7px;;height:32px" >
                                <option value="0">Select</option>
                                @foreach ($lookups['regionalmanager'] as $key=>$value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>    
                        </div> 
                </div>
                <div class="col-md-1" style="padding-top:10px;text-align:center">Billing Value</div>
                <div class="col-md-1">
                        <div class="row">
                                <select name="billingvaluerange" id="billingvaluerange" value="" class="form-control" style="margin-top:0px;;" >
                                        
                                        <option value="greaterthan">Greater than</option>
                                        <option value="lessthan">Less than</option>
                                </select>
                        </div>   
                </div>
                
                <div class="col-md-1">
                        <div class="row" style="padding-left:10px;">
                                <input type="number" placeholder="Billing Value" name="contractbillingvalue" id="contractbillingvalue" value="" class="form-control" style=";height:39px" />
                     </div>
                              
                </div>
                
        </div>
        <div class="row " style="padding-bottom:10px">
                
        </div>
        <div class="row " style="padding-bottom:10px">
                <div class="col-md-2" style="padding-top:10px">Contract End Date Between                    </div> 
                <div class="col-md-2">
                        <div class="row">
                        <input type="text" name="contractenddate_from" id="contractenddate_from" placeholder="From " value="" class="form-control datepicker" style="" />     
                        </div>
                </div>
                
                <div class="col-md-2">
                    <div class="row" style="padding-left:10px">
                    <input type="text" name="contractenddate_end" id="contractenddate_end" placeholder="Till " value="" class="form-control datepicker" />     
                    </div>
                </div>
                
                
                
                <div  class="col-md-1">
                <div class="row" style="padding-left:10px"><button id="filterbutton" type="button" class="button btn submit" style="width:100%">Filter</button></div>
                </div>
                <div  class="col-md-1">
                    <div class="row" style="padding-left:10px"><button id="resetbutton" type="reset" class="button btn submit" style="width:100%">Reset</button></div>
                    </div>
        </div>
        <div class="row " style="padding-bottom:10px">
                
        </div>
    
   
    
</div>
</form>
@endcan

<table class="table table-bordered" id="table-id">
    <thead>
        <tr>
            <th>#</th>
            <th>Prepared By</th>
            <th>Project Number</th>
            <th>Contract Name</th>
            <th>Contract Start Date</th>
            <th>Contract End Date</th>
            <th>Regional Manager</th>
            <th>Supervisor</th>
            <th>Total Contract Billing Value</th>

            
            @canany(['add_contracts','view_contracts'])
            <th>Actions</th>
            @endcan
        </tr>
    </thead>
</table>



@stop
@section('scripts')
<script>
    $(function () {
        $("#customername").select2();
        $("#regionalmanager").select2();
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            table = $('#table-id').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'Blfrtip',
                processing: true,
                serverSide: true,
                fixedHeader: true,
                destroy : true,
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],

                buttons: [
                     'excel', 'pdf', 'print'
                ],
                ajax: {
                    "url":'{{ route('contracts.contracts-list') }}',
                    data: function ( d ) {
                        
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                order: [[0, 'desc']],
                columns: [
                {
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                {data: 'preparedby', name: 'preparedby'},
                {data: 'contract_number', name: 'contract_number'},
                {data: 'contract_name', name: 'contract_name'},
                {data: 'contract_startdate', name: 'contract_startdate_raw'},
                {data: 'contract_enddate', name: 'contract_enddate_raw'},
                {data: 'regional_manager', name: 'regional_manager'},
                {data: 'supervisor_name', name: 'supervisor_name'},
                {data: 'billing_value', name: 'billing_value'},

                {
                    data: null,
                    name: 'action',
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        actions = '<a href="edit-cmuf-form/' + o.id + '" class="edit fa fa-edit" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
               
                ]
            });
        } catch(e){
            console.log(e.stack);
        }
        

       

        
        
    });

   

  

    
    $("#showfilter").on("click",function(event){
        $("#employee-filter-div").toggle();
    });

    $("#filterbutton").on("click",function(event){
        event.preventDefault();
        var formulario = $('#filterform')[0];
        var formData = new FormData(formulario); 
        formData.append("project_number",$("#customername").attr("attr-project_number"));
       
        var datatable = $("#table-id").DataTable();
        //datatable.clear().rows.add(response).draw();
        datatable.destroy();
        $('#table-id tbody').empty();
        $('#table-id').html("");
        
        
        

        
        
        $.ajax({
            type: "post",
            url: "{{route('contracts.filtercontractsummary')}}",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            
            success: function (response) {
                
                $('#table-id').html(response);
                //datatable.refresh();
                
                $('#table-id').dataTable();
                
            }
        }); 
    })

    $("#resetbutton").on("click",function(event){
        $("#customername").select2().val("Select").trigger("change");
        $("#regionalmanager").select2().val("Select").trigger("change");
    });
</script>
@stop
@section('css')
    <style>
    .select2{
        ;
        height:34px
    }
        </style>
@endsection

