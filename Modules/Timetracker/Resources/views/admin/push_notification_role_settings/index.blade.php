@extends('adminlte::page')
<style>
    .delete {
        margin-left: 10px;
        margin-right: 10px;
    }
</style>
@section('content_header')
    <h1>Push Notification Roles List</h1>
@stop
@section('content')

    <div class="add-new" data-title="Attach Role">Add
        <span class="add-new-label">New</span>
    </div>
    <table class="table table-bordered" id="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Role</th>
            <th>Created At</th>
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
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Role</label>
                        <div class="col-sm-9">
                            <select name="role" id="push-role-select" required class="form-control">
                                <option value="" selected disabled>Select Role</option>
                                <option value="2">Test</option>
                            </select>
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

        function fetchRolesDataForAllocation() {
            var url = '{{route('push_notification.role_settings.roles_for_allocation')}}'
            $.get({
                url: url,
                type: "GET",
                timeout: 15000,
                global: false,
                success: function (response) {
                    var options = [
                        '<option value="" selected disabled>Select Role</option>'
                    ];
                    for (var key in response.data) {
                        var buildOption = '<option value="' + key + '">' + response.data[key] + '</option>'
                        options.push(buildOption);
                    }

                    $('#push-role-select').html(options.join(''));
                },
                complete: function (data) {
                }
            });
        }

        $('select#push-role-select').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Select Role',
            width: '100%'
            });

        $(function () {
            //Load for first time
            fetchRolesDataForAllocation();
        });

        $(function () {
            $.fn.dataTable.ext.errMode = 'throw';
            try {
                var table = $('#table').DataTable({
                    processing: false,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('push_notification.role_settings.list') }}",
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
                            data: 'role',
                            name: 'role'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: null,
                            sortable: false,
                            render: function (o) {
                                var actions = '';
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
                    url: '{{ route('push_notification.role_settings.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        fetchRolesDataForAllocation();
                        if (data.success) {
                            swal("Saved", "Role has been attached successfully", "success");
                            $("#form")[0].reset();
                            bar.width('0%')
                            percent.html('0%');
                            $("#myModal").modal('hide');
                            if (table != null) {
                                table.ajax.reload();
                            }
                        } else {
                            swal("Failed", "Failed to attach role ", "warning");
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
            /*  Save- End */

            /*  Delete - Start */
            $('#table').on('click', '.delete', function (e) {
                var id = $(this).data('id');
                var base_url = "{{ route('push_notification.role_settings.destroy',':id') }}";
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
                                fetchRolesDataForAllocation();
                                if (data.success) {
                                    swal("Deleted", "Role has been detached successfully", "success");
                                    if (table != null) {
                                        table.ajax.reload();
                                    }
                                } else {
                                    swal("Alert", "Failed to detach role", "warning");
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
            /*  Delete- End */

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
