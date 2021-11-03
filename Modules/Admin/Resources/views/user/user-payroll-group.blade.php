@extends('adminlte::page')
@section('title', 'User Payroll Groups')
@section('content_header')
<h1>User Payroll Groups</h1>
@stop

@section('css')
<style>
    .fa {
        margin-left: 11px;
    }

    .select2 .select2-container {
        width: 12% !important;
    }
</style>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" onclick="addnew()" data-title="Add New Payroll Group">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="user-payroll-group-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Apogee Code</th>
            <th>Created Date</th>
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
                <h4 class="modal-title" id="myModalLabel">Payroll Group</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'payroll-group-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="name">Name</label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group col-md-12" id="name">
                            {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Payroll Group Name')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="name">Apogee Code</label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group col-md-12" id="apogee_code">
                            {{ Form::text('apogee_code',null,array('class'=>'form-control','placeholder' => 'Apogee Code')) }}
                            <small class="help-block"></small>
                        </div>
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
    $(function() {
        $('#office-ids').select2(); //Added Select2 to office-ids listing
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#user-payroll-group-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Services');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('admin.upg.list') }}",
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // order: [[ 5, "desc" ]],
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'apogee_code',
                        name: 'apogee_code'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(o) {
                            var actions = "";
                            @can('edit_masters')
                            actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<a href="javascript:void(0)" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }

        /* Service Store - Start*/
        $('#payroll-group-form').submit(function(e) {
            e.preventDefault();
            if ($('#payroll-group-form input[name="id"]').val()) {
                var message = 'Payroll Group has been updated successfully';
            } else {
                var message = 'Payroll Group has been created successfully';
            }
            formSubmit($('#payroll-group-form'), "{{ route('admin.upg.store') }}", table, e, message);
        });
        /* Service Store - End*/

        /* Service Edit - Start*/
        $("#user-payroll-group-table").on("click", ".edit", function(e) {
            id = $(this).data('id');
            var url = '{{ route("admin.upg.single",":id") }}';
            var url = url.replace(':id', id);
            $('#payroll-group-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal input[name="apogee_code"]').val(data.apogee_code);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Payroll Group: " + data.name);
                    } else {
                        swal("Oops", "Edit was unsuccessful", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Service Edit - End*/

        /* Service Delete  - Start */
        $('#user-payroll-group-table').on('click', '.delete', function(e) {
            var id = $(this).data('id');
            var base_url = "{{ route('admin.upg.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Payroll Group has been deleted successfully';
            deleteServiceRecord(url, table, message);
        });

        /* Delete Record - Start */
        function deleteServiceRecord(url, table, message) {
            var url = url;
            var table = table;
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
                function() {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            if (data.success) {
                                swal("Deleted", 'Payroll group has been deleted successfully', "success");
                                if (table != null) {
                                    table.ajax.reload();
                                }
                            } else if (data.success == false) {
                                if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                                    swal("Warning", 'Something went wrong', "warning");
                                } else {
                                    swal("Warning", 'Data exists', "warning");
                                }
                            } else if (data.warning == true) {
                                swal("Warning", data.message, "warning");
                            } else {
                                console.log(data);
                            }
                        },
                        error: function(xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                });
        }
        /* Delete Record - End */
        /* Service Delete  - End */

    });

    function addnew(data = null) {

        $("#myModal").modal();
        $("#payroll-group-form")[0].reset();
        $('#myModal input[name="id"]').val('');
        $('#payroll-group-form').find('.form-group').removeClass('has-error').find('.help-block').text('');

    }
</script>
@stop