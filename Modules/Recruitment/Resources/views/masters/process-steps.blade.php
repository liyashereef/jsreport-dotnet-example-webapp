{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'Process Steps')
@section('content_header')
<h1>Process Steps</h1>
@stop

@section('content')
<div class="add-new" data-title="Add New Process Step">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="tracking-table">
    <thead>
        <tr>
            <th width="10%">Step Order</th>
            <th width="45%" >Step Name</th>
            <th width="25%" >Notes</th>
            <th  width="10%" >Created Date</th>
            <th  width="5%" >Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Process Step</h4>
                
            </div>
            {{ Form::open(array('url'=>'#','id'=>'tracking-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                 <div class="form-group row" id="step_order">
                        <label for="step_order" class="col-sm-3 control-label">Step Order</label>
                        <div class="col-sm-2">
                            {{ Form::number('step_order',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                <div class="form-group row" id="display_name">
                    <label for="display_name" class="col-sm-3 control-label">Step Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('display_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="notes">
                    <label for="notes" class="col-sm-3 control-label">Notes</label>
                    <div class="col-sm-9">
                    {{ Form::textarea('notes',null,array('class'=>'form-control','maxlength'=>'300','rows'=>"3",'placeholder'=>"Notes")) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary','id'=>'mdl_save_change'))}}
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
        var table = $('#tracking-table').DataTable({
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
                        emailContent(table, 'Process Steps');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.process-steps.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 0, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'step_order', name: 'step_order',sortable:true},
                {data: 'display_name', name: 'display_name'},
                {data: 'notes', name: 'notes'},
                {data: 'created_at', name: 'created_at'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Process Save - Start */
        $('#tracking-form').submit(function (e) {
            e.preventDefault();
            if($('#tracking-form input[name="id"]').val()){
                var message = 'Process step has been updated successfully';
            }else{
                var message = 'Process step has been created successfully';
            }
            formSubmit($('#tracking-form'), "{{ route('recruitment.process-steps.store') }}", table, e, message);
        });
        /* Process Save - End */

        /* Process Edit - Start */
        $("#tracking-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.process-steps.single",":id") }}';
            var url = url.replace(':id', id);
            $('#tracking-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="display_name"]').val(data.display_name)
                        $('#myModal input[name="step_order"]').val(data.step_order)
                        $('#myModal textarea[name="notes"]').val(data.notes)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Process Step: " + data.display_name)
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
        /* Process Edit - End */


        /* Process Delete - Start */
        $('#tracking-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.process-steps.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Process step has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Process Delete - End */

    });

</script>
<style type="text/css">
    .modal-body{
        padding-left: 0px;
        margin-left: 0px;
    }
   
</style>
@stop
