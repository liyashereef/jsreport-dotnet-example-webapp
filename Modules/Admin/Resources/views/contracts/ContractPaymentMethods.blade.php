@extends('adminlte::page')
@section('title', 'Contract Billing Frequency')
@section('content_header')
<h1>Contract Payment Method</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Contract Payment Methods">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="reason-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Payment Method</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <span id="field_error"></span>

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Payment Methods</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'payment-methods-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            <input type="hidden" name="id" id="id" />
            <div class="modal-body">
                    <div id="form-errors"></div>
                 <div class="form-group" id="paymentmethodtitle">

                    <label for="paymentmethodtitle" class="col-sm-3 control-label">Payment Methods <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        
                        <input type="text" class="form-control has-error" name="paymentmethodtitle" id="paymentmethodtitle" placeholder="Payment Methods" value="" />
                      
                        <small class="help-block"></small>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::reset('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop 



@section('js')
<script type="text/javascript">
     $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#reason-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            columnDefs: [
            { width: 200, targets: 0 }
            ],
            fixedColumns: true,
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1]
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
            ajax: "{{ route('view-payment-methods.show') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [2, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                    width:"5%",
                },
                {
                    data: 'paymentmethod',
                    name: 'paymentmethod',
                    width:"85%",
                },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var actions = '';
                        
                        actions += '<a href="#" class="edit editreason fa fa-pencil" data-id=' + o.id + ' data-change-title=\'' + o.paymentmethod + '\' ></a>'
                        
                        @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete deletereason fa fa-trash-o" data-id=' + o.id + ' data-change-title=\'' + o.paymentmethod + '\' ></a>';
                            @endcan
                        return actions;
                        },
                }
            ]
        });
        } catch(e){
            
        }

        $(document).on("submit","#payment-methods-form",function(e){
            event.preventDefault();

            var paymentmethodtitle = $("#paymentmethodtitle").val();
            var id = $("#id").val();
            var url = "{{route('save-payment-methods')}}";
            var message = '';
            $form = $('#contract-billing-cycle-form');
            if (id < 1) {
                url = "{{route('save-payment-methods')}}";
                message = 'Payment methods has been created successfully';
            } else {
                url = "{{route('update-payment-methods')}}";
                message = 'Payment methods has been updated successfully';
            }
            
            formSubmit($('#payment-methods-form'), url, table, e, message);
            
            
        });

        $(document).on("click",".editreason",function(){
            var columnid = $(this).attr("data-id");
            $('#myModal input[name="id"]').val(columnid);

            $('#myModal input[name="billingcycletitle"]').val($(this).attr("data-change-title"));
            $('#myModal').modal("toggle");
        });

        $(document).on("click",".deletereason",function(event){
            var id = $(this).attr("data-id");
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false
                },
                function () {
                    if(true){
                        $.ajax({
                type: "post",
                url: "{{route('delete-payment-methods')}}",
                data: {paymentmethodid:id},
                success: function (response) { 
                    swal("Deleted", "Deleted successfully", "success"); 
                    var datatable = $('#reason-table').DataTable();
                    datatable.ajax.reload();                                    
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function (err) {
                        console.log(xhr);
                }
            });
                        
                    }
                });           
            
        }); 
</script>
@endsection