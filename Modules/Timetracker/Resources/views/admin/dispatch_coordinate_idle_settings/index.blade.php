@extends('adminlte::page')
<style>
    .delete {
        margin-left: 10px;
        margin-right: 10px;
    }
</style>
@section('content_header')
    <h1> MST Settings</h1>
@stop
@section('content')
    <table class="table table-bordered" id="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Idle Time (Minutes)</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        </thead>
    </table>
    <div class="modal fade" id="myModal"
         data-backdrop="static" tabindex="-1"
         role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                {{ Form::hidden('id', null) }}
                <div class="modal-body">
                    <div class="form-group" id="request-type-rate">
                        <label for="name" class="col-sm-3 control-label">Idle Time (Minutes)</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control"
                                   min="0" step="any"
                                   name="idle_time"
                                   required
                                   placeholder="Idle Time (Minutes)">

                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                    {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop @section('js')
    <script>
        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                var table = $('#table').DataTable({
                    processing: false,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('dispatch_coordinate.settings_api') }}",
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
                    columns: [
                        {
                            data: 'id',
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            },
                            orderable: false
                        },

                        {
                            data: 'idle_time',
                            name: 'idle_time'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'

                        },
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {
                                var actions = '';
                                actions += '<a href="#" class="edit fa fa-pencil" title="Edit" data-id=' + o.id + '></a>'
                                return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }


            /* Save - Start */
            var bar = $('.bar');
            var percent = $('.percent');
            $('#form').submit(function (e) {
                e.preventDefault();
                var $form = $('#form');
                var formData = new FormData($form[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        var percentVal = '0%';
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                bar.width(percentComplete + '%')
                                percent.html(percentComplete + '%');
                                if (percentComplete === 100) {
                                    console.log('completed');
                                }
                            }
                        }, false);

                        return xhr;
                    },
                    url: '{{ route('dispatch_coordinate.settings.update') }}',
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            swal("Saved", "Dispatch coordinate settings  has been updated successfully", "success");
                            $("#form")[0].reset();
                            bar.width('0%')
                            percent.html('0%');
                            $("#myModal").modal('hide');
                            if (table != null) {
                                table.ajax.reload();
                            }
                        } else {
                            console.log('else', data);
                        }
                    },
                    fail: function (response) {
                        console.log(response);
                    },
                    error: function (xhr, textStatus, thrownError) {
                        associate_errors(xhr.responseJSON.errors, $form);
                        bar.width('0%')
                        percent.html('0%');
                    },
                    contentType: false,
                    processData: false,
                });
            });
            /*Save- End */

            /* Dispatch Request Type Edit - Start */
            $("#table").on("click", ".edit", function (e) {
                var id = $(this).data('id');
                var url = '{{ route("dispatch_coordinate.settings.show",":id") }}';
                var url = url.replace(':id', id);
                $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            $('#myModal input[name="id"]').val(data.id)
                            $('#myModal input[name="idle_time"]').val(data.idle_time)

                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit Idle Settings:")
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
            /* Dispatch Request Type Edit - End */
        });
    </script>
@stop
