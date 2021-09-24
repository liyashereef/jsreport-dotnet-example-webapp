{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Expense Category')
@section('content_header')
<h1>Expense Payment Mode</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Payment Mode">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="expense-payment-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Mode of Payment</th>
            <th>Reimbursement</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
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
                <h4 class="modal-title" id="myModalLabel">Expense Payment Mode</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'expense-payment-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group" id="mode_of_payment">
                    <label for="mode_of_payment" class="col-sm-3 control-label">Mode of Payment <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('mode_of_payment',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>       
                <div class="form-group" id="reimbursement">
                    <label for="reimbursement" class="col-sm-3 control-label">Reimbursement </label>                    
                    <div class="col-sm-9">
                        <fieldset id="reimburse">
                            <label style="margin-right: 10px;"><input type="radio" name="reimbursement" id="reimburse-on" value=1>&nbsp;ON</label>
                            <label style="margin-right: 10px;"><input type="radio" name="reimbursement" id="reimburse-off" value=0>&nbsp;OFF</label>
                            <label><input type="radio" name="reimbursement" id="reimburse-none" value='' checked>&nbsp;NONE</label>
                        </fieldset>
                        <small class="help-block"></small>
                    </div>
                </div>       
                       
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@section('js')
<script>
    $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#expense-payment-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2, 3,4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2, 3,4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Expense_Payment_Mode');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('expense-payment-mode.list') }}",
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
                    sortable:false,
                },
                {
                    data: 'mode_of_payment',
                    name: 'mode_of_payment'
                },
                {

                    data: null,
                    name: 'reimbursement',
                    render : function ( data) {                        
                        var actions = '';                      
                        if(data.reimbursement == 1){
                            actions = 'On'
                        }
                        else if(data.reimbursement == 0){
                            actions = 'Off'
                        }
                        else{
                            actions = 'None'
                        }                        
                        return actions;
                    }                  
                },
               
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
         } catch(e){
            console.log(e.stack);
        }    

        /* Posting data to ExpenseParentCategoryController - Start*/
        $('#expense-payment-form').submit(function (e) {
            e.preventDefault();
            if($('#expense-payment-form input[name="id"]').val()){
                var message = 'Expense payment mode has been updated successfully';
            }else{
                var message = 'Expense payment mode has been created successfully';
            }     
            formSubmit($('#expense-payment-form'), "{{ route('expense-payment-mode.store') }}", table, e, message);
        });
        /* Posting data to ExpenseParentCategoryController - End*/


        $("#expense-payment-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("expense-payment-mode.single",":id") }}';
            var url = url.replace(':id', id);
            $('#expense-payment-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {                       
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="mode_of_payment"]').val(data.mode_of_payment);
                        if(data.reimbursement === 1){
                            $('#myModal #reimburse-on').prop('checked', true);
                        }else if(data.reimbursement === 0){
                            $('#myModal #reimburse-off').prop('checked', true);
                        }else{
                            $('#myModal #reimburse-none').prop('checked', true);
                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Mode Of Payment: "+ data.mode_of_payment)
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

        $('#expense-payment-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('expense-payment-mode.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Expense payment mode  has been deleted successfully';
            deleteRecord(url, table, message);
        });

    }); 
</script>
@stop