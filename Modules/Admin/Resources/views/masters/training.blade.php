@extends('adminlte::page')
@section('title', 'Trainings')
 @section('content_header')
<h1>Trainings</h1>
@stop @section('content')
<div class="add-new" data-title="Add New Training">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="training-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Training</th>
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
            {{ Form::open(array('url'=>'#','id'=>'training-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="training">
                    <label for="training" class="col-sm-3 control-label">Training</label>
                    <div class="col-sm-9">
                        {{ Form::text('training',null,array('class'=>'form-control')) }}
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
        var table = $('#training-table').DataTable({
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
                        emailContent(table, 'Training');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('training.list') }}",
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
                    data: 'training',
                    name: 'training'
                },
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

        /* Posting data to Training Controller - Start*/
        $('#training-form').submit(function (e) {
            e.preventDefault();
            if($('#training-form input[name="id"]').val()){
                var message = 'Training has been updated successfully';
            }else{
                var message = 'Training has been created successfully';
            }
            formSubmit($('#training-form'), "{{ route('training.store') }}", table, e, message);
        });
        /* Posting data to Training Controller - End*/

        /* Edit Training type- Start*/
        $("#training-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("training.single",":id") }}';
            var url = url.replace(':id', id);
            $('#training-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="training"]').val(data.training)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Training: "+data.training)
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
        /* Edit Training type- End*/

        /* Delete Training type- Start*/
        $('#training-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('training.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Training has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Delete Training type- End*/

    });
</script>
@stop
