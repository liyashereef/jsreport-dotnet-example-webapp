@extends('adminlte::page')
@section('title', 'Customer Allocation')
@section('content_header')
<h3>Shift Module</h3>
@stop
@section('content')
<style>
    .add-new{
       
        margin-bottom:-10px;
    }
</style>
<div class="add-new" onclick="addnew()" data-title="Add New Customer">Add <span class="add-new-label">New</span>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12" id="shift-module-container">
        <div class="form-group row">
            <div class="col-md-2">
                Choose Customer:
            </div>
            <div class="col-md-3">
                <select name="customer_id" id="customer_id" class="form-control">
                    <option value=0 selected>Select All</option>
                    @foreach($customer_list as $id=>$data)
                    <option value={{$data->id}}>{{$data->project_number}}-{{$data->client_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered" id="shift-module-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Module Name</th>
            <th>Customer</th>
            <th width="10%">Status</th>
            <th width="10%">Post Order</th>
            <th width="10%">Action</th>
        </tr>
    </thead>
</table>

@stop
@section('js')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $('#customer_id').select2();//Added Select2 to project listing
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#shift-module-table').DataTable({
                bProcessing: false,
                processing: true,
                serverSide: true,
                fixedHeader: true,
                dom: 'lfrtBip',
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            stripNewlines: false
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4]
                        }
                    },
                ],
                ajax:{
                    url: '{{ route('customer-shift-module.list') }}', // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function(d) {
                        d.customer_id = $('#customer_id').val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'module_name', name: 'module_name'},
                {data: 'customer', name: 'customer'},
                {data: 'status', name: 'status'},
                {data: 'post_order', name: 'post_order'},
                { data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="{{route("customer-shift-module.update", ["id" => ""])}}/'+ o.id +'" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                },
                ]
            });
        } catch(e){
            console.log(e.stack);
        }


        $('#shift-module-table').on('click', '.delete', function (e) {
            id = $(this).data('id');
            console.log(e);
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "{{route('customer-shift-module.destroy')}}",
                    type: 'GET',
                    data: "id=" + id,
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", "Module has been deleted successfully", "success");
                            table.ajax.reload();
                        } else {
                            alert(data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
            });
        });

        $('#customer_id').on('change', function () {
               $customer_id = $('#customer_id').val();
               if($customer_id != 0){
                table.ajax.url('customer-shift-module/list/'+$customer_id).load();
               }else{
                table.ajax.url('customer-shift-module/list').load();
               }
        });
        /*Filters for customer - End*/

        /*Filters for Permanent and STC customer list in dropdown - Start*/
        $('#filter').on('change', 'input[name=customer-contract-type]', function () {
            url = "{{route('customer.namelist',':type')}}";
            type = $('input[name=customer-contract-type]:checked').val();
            if($(this).val() == {{PERMANENT_CUSTOMER}}){
                url = url.replace(':type', type);
            } else {
                url = url.replace(':type', type);
            }
            $.ajax({
                url:url,
                method: 'GET',
                success: function (data) {
                    var options = '<option selected="selected" value="">Select</option>';
                    $.each(data, function(key,value){
                        options += '<option value="'+value.id+'">'+value.client_name+'</option>'
                    });
                    $("#customer_id").html('');
                    $("#customer_id").html(options);
                    table.ajax.url('customer-allocation/list').load();
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                },
            });
        });
        /*Filters for Permanent and STC customer list in dropdown - End*/
    });




    function addnew() {
        window.location.href = "{{route('customer-shift-module.add')}}";
    }
</script>
@stop
