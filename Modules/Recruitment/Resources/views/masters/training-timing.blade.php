@extends('adminlte::page')
@section('title', 'Timings')
@section('content_header')
<h1>Timings</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Timing">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="timing-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Timing</th>
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
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'timing-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('timings') ? 'has-error' : '' }}" id="timings">
                    <label for="timings" class="col-sm-3 control-label">Timing</label>
                    <div class="col-sm-9">
                        {{ Form::text('timings',null,array('class'=>'form-control')) }}
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
@stop @section('js')
<script>
    $(function () {
         $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#timing-table').DataTable({
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
                        emailContent(table, 'Timing');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.training-timing.list') }}",
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
                {
                    data: 'timings',
                    name: 'timings'
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

        /* Save Training Time- Start*/
        $('#timing-form').submit(function (e) {
            e.preventDefault();
            if($('#timing-form input[name="id"]').val()){
                var message = 'Timing has been updated successfully';
            }else{
                var message = 'Timing has been created successfully';
            }
            formSubmit($('#timing-form'), "{{ route('recruitment.training-timing.store') }}", table, e, message);
        });
        /* Save Training Time- End*/

        /* Edit Training Time- Start*/
        $("#timing-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.training-timing.single",":id") }}';
            var url = url.replace(':id', id);
            $('#timing-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="timings"]').val(data.timings)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Timing: "+data.timings)
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
        /* Edit Training Time- End*/

        /* Delete Training Time- Start*/
        $('#timing-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.training-timing.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Timing has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Delete Training Time- End*/

    });
</script>
@stop
