@extends('adminlte::page')
@section('title', 'Client Feedback')
@section('content_header')
<h1>Customer Terms And Conditions</h1>
@stop @section('content')
<style>
    .view{
        padding-right: 8%;
    }
</style>

<a class="add-new" data-title="Add New Terms And Condition" href="{{ route('customer-terms-and-conditions.edit',1) }}">
    Show<span class="add-new-label">Default</span>
</a>
<a class="add-new" data-title="Add New Terms And Condition" href="{{route('customer-terms-and-conditions.create') }}">
    Add<span class="add-new-label">New</span>
</a>

<table class="table table-bordered" id="terms-and-conditions-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
           
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            
            <div class="modal-body">
              
                
            </div>

            <div class="modal-footer" style="text-align: right !important;">
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
          
        </div>
    </div>
</div>

@stop @section('js')
<script>
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#terms-and-conditions-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Experiences Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('customer-terms-and-conditions.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false
                },
                {data: 'customer.client_name', name: 'customer.client_name'},              
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var actions = '';
                         var terms_url = '{{ route("customer-terms-and-conditions.edit",":id") }}';
                         var terms_url = terms_url.replace(':id', o.id);

                         actions += '<a href='+terms_url+' class="edit fa fa-pencil"></a>';
                         actions += '<a href="#" class="view fa fa-eye" data-id=' + o.id + '></a>';
                         if(o.customer_id!=0) {
                            actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        }                           
                        
                     return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

       
        /* terms-and-conditions Delete - Start*/
         $('#terms-and-conditions-table').on('click', '.delete', function (e) {
             
            var id = $(this).data('id');
            var base_url = "{{ route('customer-terms-and-conditions.destroy',':id') }}";
            var url = base_url.replace(':id', id);
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
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", "Data has been deleted successfully", "success");
                            table.ajax.reload();
                        }else {
                              swal("Warning", "This data cannot be deleted", "warning");
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
            });

        });
        /* terms-and-conditions Delete - End*/

        /* Experience Edit - Start*/
        $("#terms-and-conditions-table").on("click", ".view", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("customer-terms-and-conditions.single",":id") }}';
            var url = url.replace(':id', id);
       
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $("#myModal").modal();
                        if(data.customer_id == 0){
                            $('#myModal .modal-title').text("View : Default terms and conditions");
                        }else{
                            $('#myModal .modal-title').text("Customer : " + data.customer.client_name);
                        }
                        $('#myModal .modal-body').html(data.terms_and_conditions);

                    } else {
                       
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                 
                },
                contentType: false,
                processData: false,
            });
        });
        /* Experience Edit - End*/

    });
 
</script>
@stop
