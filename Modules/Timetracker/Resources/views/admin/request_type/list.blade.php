@extends('adminlte::page')
<style>
    .delete {
        margin-left: 10px;
        margin-right: 10px;
    }
</style>
@section('content_header')
    <h1> Dispatch Request Types </h1>
@stop
@section('content')
    <div class="add-new" data-title="Add New Dispatch Request Type">Add
        <span class="add-new-label">New</span>
    </div>
    <table class="table table-bordered" id="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Rate</th>
            <th>Description</th>
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
                    <div class="form-group" id="request-type-name">
                        <label for="name" class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-9">
                            {{ Form::text('name',
                            null,
                            array('class' => 'form-control',
                              'Placeholder'=>'Dispatch Request Name',
                              'required'=>TRUE)) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="request-type-rate">
                        <label for="name" class="col-sm-3 control-label">Rate</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control"
                                   min="0" step="any"
                                   name="rate"
                                   required
                                   placeholder="Dispatch Request Rate">

                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="request-type-description">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9">
                            {{ Form::textarea('description',null,
                            array('class' => 'form-control',
                             'Placeholder'=>'Dispatch Request Description',
                              'rows' => 5,
                               'cols' => 40)) }}
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
                    dom: 'lfrtBip',
                    bprocessing: false,
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
                                emailContent(table, 'Dispatch Request Types');
                            }
                        }
                    ],
                    processing: false,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('dispatch-request-types.list') }}",
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
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'rate',
                            name: 'rate'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {
                                var actions = '';
                                actions += '<a href="#" class="edit fa fa-pencil" title="Edit" data-id=' + o.id + '></a>'
                                @can('lookup-remove-entries')
                                    actions += '<a href="#" class="delete fa fa-trash-o" title="Delete"  data-id=' + o.id + '></a>';
                                @endcan
                                return actions;
                            },
                        }
                    ]
                });
            } catch (e) {
                console.log(e.stack);
            }

            /*Dispatch Request Type Save - Start */
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
                    url: '{{ route('dispatch-request-types.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        if (data.success) {
                            if (data.data.created == false) {
                                swal("Saved", "Dispatch request type has been updated successfully" , "success");
                            } else {
                                swal("Saved", "Dispatch request type has been created successfully", "success");
                            }
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
            /* Dispatch Request Type Save- End */

            /* Dispatch Request Type Edit - Start */
            $("#table").on("click", ".edit", function (e) {
                var id = $(this).data('id');
                var url = '{{ route("dispatch-request-types.show",":id") }}';
                var url = url.replace(':id', id);
                $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data) {
                            $('#myModal input[name="id"]').val(data.id)
                            $('#myModal input[name="name"]').val(data.name)
                            $('#myModal input[name="rate"]').val(data.rate)
                            $('#myModal textarea[name="description"]').val(data.description)

                            $("#myModal").modal();
                            $('#myModal .modal-title').text("Edit Request Type: " + data.name)
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

            /* Dispatch Request Type Delete - Start */
            $('#table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('dispatch-request-types.destroy',':id') }}";
                var url = base_url.replace(':id', id);
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
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                if (data.success) {
                                    swal("Deleted", "Dispatch request type has been deleted successfully", "success");
                                    if (table != null) {
                                        table.ajax.reload();
                                    }
                                } else {
                                    swal("Alert", "Failed to delete dispatch request type.", "warning");
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
            /* Dispatch Request Type Delete- End */

            /* Clear Uploaded File label - Start */
            $('.add-new').click(function () {
                $("#myModal").modal();
                var title = $(this).data('title');
                $("#myModal").modal();
                $('#myModal form').trigger('reset');
                $('#myModal').find('input[type=hidden]').val('');
                $('#myModal .modal-title').text(title);
                $('#myModal form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            });
        });
    </script>
@stop
