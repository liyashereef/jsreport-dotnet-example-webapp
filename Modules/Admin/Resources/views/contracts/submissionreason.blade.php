@extends('adminlte::page')
@section('title', 'Contract Submission Reasons')
@section('content_header')
<h1>Contract Submission Reasons</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Contract Submission Reason">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="reason-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Reason</th>
            <th>Sequence</th>
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
                <h4 class="modal-title" id="myModalLabel">Submission Reason</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'submission-reason-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            <input type="hidden" name="reason-id" id="reason-id" />
            <input type="hidden" name="reason-sequence" id="reason-sequence" />
            <div class="modal-body">
                    <div id="form-errors"></div>
                 <div class="form-group" id="submissionreason">

                    <label for="submissionreason" class="col-sm-3 control-label">Reason <span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        
                        <input type="text" class="form-control has-error" name="submissionreason" id="submissionreason" placeholder="Submission Reason" value="" />
                      
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
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
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
            ajax: "{{ route('submission-reason.list') }}",
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
                    data: 'reason',
                    name: 'reason',
                    width:"75%",
                },
                {
                    data: 'sequence', 
                    name: 'sequence',
                    width:"10%",
                },
                {
                    data: null,
                    sortable: false,
                    width:"10%",
                    render: function (o) {
                         var actions = '';
                        
                        actions += '<a href="#" class="edit editreason {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + ' data-reason=\'' + o.reason + '\' data-sequence=' + o.sequence + '></a>'
                        
                        @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete deletereason {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + ' data-reason=\'' + o.reason + '\' data-sequence=' + o.sequence + '></a>';
                            @endcan
                        return actions;
                        },
                }
            ]
        });
        } catch(e){
            
        }

        $(document).on("submit","#submission-reason-form",function(e){
            event.preventDefault();

            var submissionreason = $("#submissionreason").val();
            var id = $("#reason-id").val();
            var previoussequence = $("#reason-sequence").val();
            var url = "{{route('save-submission-reason')}}";
            var message = '';
            $form = $('#submission-reason-form');
            if (previoussequence < 1) {
                url = "{{route('save-submission-reason')}}";
                message = 'Submission reason has been created successfully';
            } else {
                url = "{{route('update-submission-reason')}}";
                message = 'Submission reason has been updated successfully';
            }
            console.log(table);
            formSubmit($('#submission-reason-form'), url, table, e, message);
            
        });

        $(document).on("click",".editreason",function(){
            var columnid = $(this).attr("data-id");
            $('#myModal input[name="reason-id"]').val(columnid);
            $('#myModal input[name="reason-sequence"]').val($(this).attr("data-sequence"));
            $('#myModal input[name="submissionreason"]').val($(this).attr("data-reason"));
            $('#myModal').modal("toggle");
        });

        $(document).on("click",".deletereason",function(event){
            var id = $(this).attr("data-id");
            var currentsequence = $(this).attr("data-sequence");
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
                        url: "{{route('delete-submission-reason')}}",
                        data: {reasonid:id,"reason-sequence":currentsequence},
                        success: function (response) { 
                            swal("Deleted", "Division lookup has been deleted successfully", "success");
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