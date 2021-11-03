{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'Tracking Process')
@section('content_header')
<h1>Process Steps List</h1>
@stop

@section('content')
<div class="add-new" data-title="Add New Tracking Process Step">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="tracking-table">
    <thead>
        <tr>
            <th>Step Number</th>
            <th>Process Step</th>
            <th>Created Date</th>
            <th>Last Modified Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Tracking Process</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'tracking-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group" id="process_steps">
                    <label for="process_steps" class="col-sm-3 control-label">Process Step</label>
                    <div class="col-sm-9">
                        {{ Form::text('process_steps',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="step_number">
                        <label for="process_steps" class="col-sm-3 control-label">Step Number</label>
                        <div class="col-sm-9">
                            {{ Form::number('step_number',null,array('class'=>'form-control')) }}
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
            ajax: "{{ route('tracking-lookup.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 0, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'step_number', name: 'step_number',sortable:true},
                {data: 'process_steps', name: 'process_steps'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
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

        /* Tracking Process Save - Start */
        $('#tracking-form').submit(function (e) {
            e.preventDefault();
            if($('#tracking-form input[name="id"]').val()){
                var message = 'Process step has been updated successfully';
            }else{
                var message = 'Process step has been created successfully';
            }
            formSubmit($('#tracking-form'), "{{ route('tracking-lookup.store') }}", table, e, message);
        });
        /* Tracking Process Save - End */

        /* Tracking Process Edit - Start */
        $("#tracking-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("tracking-lookup.single",":id") }}';
            var url = url.replace(':id', id);
            $('#tracking-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="process_steps"]').val(data.process_steps)
                        $('#myModal input[name="step_number"]').val(data.step_number)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Process Step: " + data.process_steps)
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
        /* Tracking Process Edit - End */


        /* Tracking Process Delete - Start */
        $('#tracking-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('tracking-lookup.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Process step has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Tracking Process Delete - End */

    });

</script>
@stop
