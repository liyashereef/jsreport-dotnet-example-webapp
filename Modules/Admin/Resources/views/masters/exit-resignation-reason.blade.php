{{-- resources/views/admin/dashboard.blade.php --}} @extends('adminlte::page')
@section('title', 'Reasons')
@section('content_header')
<h1>Exit Resignation Reasons</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Type">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Reason</th>
            <th>Short Name</th>
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
                <h4 class="modal-title" id="myModalLabel">Exit Termination Reason</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                </ul>
                <div class="form-group" id="reason">
                    <label for="type" class="col-sm-3 control-label">Reason</label>
                    <div class="col-sm-9">
                        {{ Form::text('reason',null,array('class'=>'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="type">
                        <label for="type" class="col-sm-3 control-label">Short Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('shortname',null,array('class'=>'form-control')) }}
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
        var table = $('#type-table').DataTable({
            dom: 'lfrtBip',
                bprocessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [ 0,1, 2]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0,1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [ 0,1, 2]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Positions');
                    }
                }
                ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('exit-resignation-reason.list') }}",
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
                    data: 'reason',
                    name: 'reason'
                },
                {
                    data: 'shortname',
                    name: 'shortname'
                },
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

        /* Posting data to PositionLookupController - Start*/
        $('#type-form').submit(function (e) {
            e.preventDefault();
            if($('#type-form input[name="id"]').val()){
                var message = 'Resignation reason has been updated successfully';
            }else{
                var message = 'Resignation reason has been created successfully';
            }
            formSubmit($('#type-form'), "{{ route('exit-resignation-reason.store') }}", table, e, message);
        });


        $("#type-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("exit-resignation-reason.single",":id") }}';
            var url = url.replace(':id', id);
            $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="reason"]').val(data.reason)
                        $('#myModal input[name="shortname"]').val(data.shortname)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Reason: "+ data.reason)
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

        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('exit-resignation-reason.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Resignation reason has been deleted successfully';
            deleteRecord(url, table, message);
        });
    });
</script>
@stop
